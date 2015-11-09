<?php namespace Comodojo\ExtenderInstaller;

require("vendor/mustangostang/spyc/Spyc.php");

use \Comodojo\ExtenderInstaller\AbstractInstaller;
use \Spyc;

/**
 * Extender installer
 * 
 * @package     Comodojo extender
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @license     GPL-3.0+
 *
 * LICENSE:
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class ExtenderInstaller extends AbstractInstaller {

	public static function loadPlugin($package_name, $package_loader) {

        $events = Spyc::YAMLLoad(self::$extender_plugins_cfg);

        foreach ($package_loader as $loader) {

            $events = self::addPlugin($events, $package_name, $loader);

        }

        $action = file_put_contents(self::$extender_plugins_cfg, Spyc::YAMLDump($events));

        if ( $action === false ) throw new Exception("Cannot activate plugin' package: ".$package_name);

    }

    public static function unloadPlugin($package_name) {

        $events = Spyc::YAMLLoad(self::$extender_plugins_cfg);

        $events = self::deletePackage("plugin", $events, $package_name);

        $action = file_put_contents(self::$extender_plugins_cfg, Spyc::YAMLDump($events));

        if ( $action === false ) throw new Exception("Cannot deactivate plugins' package: ".$package_name);

    }

    public static function loadTasks($package_name, $package_loader) {

        $tasks = Spyc::YAMLLoad(self::$extender_tasks_cfg);

        foreach ($package_loader as $loader) {

            $tasks = self::addTask($tasks, $package_name, $loader);

        }

        $action = file_put_contents(self::$extender_tasks_cfg, Spyc::YAMLDump($tasks));

        if ( $action === false ) throw new Exception("Cannot activate tasks' package: ".$package_name);

    }

    public static function unloadTasks($package_name) {

        $tasks = Spyc::YAMLLoad(self::$extender_tasks_cfg);

        $tasks = self::deletePackage("task", $tasks, $package_name);

        $action = file_put_contents(self::$extender_tasks_cfg, Spyc::YAMLDump($tasks));

        if ( $action === false ) throw new Exception("Cannot deactivate tasks' package: ".$package_name);

    }

    public static function loadCommands($package_name, $package_loader) {

        $commands = Spyc::YAMLLoad(self::$extender_commands_cfg);

        foreach ($package_loader as $command => $actions) {

            $commands = self::addCommand($commands, $package_name, $command, $actions);

        }

        $action = file_put_contents(self::$extender_commands_cfg, Spyc::YAMLDump($commands));

        if ( $action === false ) throw new Exception("Cannot activate commands' package: ".$package_name);

    }

    public static function unloadCommands($package_name) {

        $commands = Spyc::YAMLLoad(self::$extender_commands_cfg);

        $commands = self::deletePackage("command", $commands, $package_name);

        $action = file_put_contents(self::$extender_commands_cfg, Spyc::YAMLDump($commands));

        if ( $action === false ) throw new Exception("Cannot deactivate commands' package: ".$package_name);

    }

    private static function addPlugin($events, $package, $plugin) {

        if ( empty($plugin["class"]) || empty($plugin["event"]) ) {

            echo "! Skipping invalid plugin: ".implode(":",$plugin)."\n";

        } else if ( empty($plugin["method"]) ) {

            $events[] = array(
                "package" => $package,
                "data" => array(
                    "class" => $plugin["class"],
                    "event" => $plugin["event"]
                )
            );

            echo "+ Enabling plugin ".$plugin["class"]." on event ".$plugin["event"]."\n";

        } else {

            $events[] = array(
                "package" => $package,
                "data" => array(
                    "class" => $plugin["class"],
                    "method" => $plugin["method"],
                    "event" => $plugin["event"]
                )
            );

            echo "+ Enabling plugin ".$plugin["class"]."::".$plugin["method"]." on event ".$plugin["event"]."\n";

        }

        return $events;

    }

    private static function addTask($tasks, $package, $task) {

        if ( empty($task["name"]) || empty($task["class"]) ) {

            echo "! Skipping invalid task: ".implode(":",$task)."\n";

        } else if ( array_key_exists($task["name"], $tasks) ) {

            echo "! Skipping duplicate task: ".$task["name"]."\n";

        } else {

            $tasks[$task["name"]] = array(
                "package" => $package,
                "data" => array(
                    "class" => $task["class"],
                    "description" => empty($task["description"]) ? null : $task["description"]
                )
            );

            echo "+ Enabling task ".$task["name"]."\n";

        }

        return $tasks;

    }

    private static function addCommand($commands, $package, $command, $actions) {

        if ( empty($actions["class"]) ) {

            echo "! Skipping invalid command: ".$command."\n";

        } else {

            $description = empty($actions["description"]) ? "" : $actions["description"];

            $aliases = array();

            if ( isset($actions["aliases"]) && @is_array($actions["aliases"]) ) {

                foreach ($actions["aliases"] as $alias) array_push($aliases, $alias);

            }

            $options = array();

            if ( isset($actions["options"]) && @is_array($actions["options"]) ) {

                foreach ($actions["options"] as $option => $oparameters) $options[$option] = $oparameters;

            }

            $arguments = array();

            if ( isset($actions["arguments"]) && @is_array($actions["arguments"]) ) {

                foreach ($actions["arguments"] as $argument => $aparameters) $arguments[$argument] = $aparameters;

            }

            $parameters = array(
                "package" => $package,
                "data" => array(
                    "class"       => $actions["class"],
                    "description" => $description, 
                    "aliases"     => $aliases,
                    "options"     => $options,
                    "arguments"   => $arguments
                )
            );
            
            $commands[$command] = $parameters;

            echo "+ Enabling command ".$command." (from ".$package.")\n";

        }

        return $commands;

    }

    private static function deletePackage($type, $haystack, $package) {

        $registered = array_keys(array_column($haystack, 'package'), $package);

        foreach ($registered as $needle) unset($haystack[$needle]);

        echo "- ".count($registered)." ".$type."(s) from ".$package." deleted\n";

    }

}
