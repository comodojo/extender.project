<?php namespace Comodojo\ExtenderInstaller;

use Comodojo\ExtenderInstaller\AbstractInstaller;

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

        $line_mark = "/****** PLUGIN - ".$package_name." - PLUGIN ******/";

        if ( is_array($package_loader) ) {

            $line_load = "";

            foreach ($package_loader as $loader) {

                if ( !isset($loader['method']) OR empty($loader["method"]) ) {

                    echo "+ Enabling plugin ".$loader["class"]." on event ".$loader["event"]."\n";

                    $line_load .= '$extender->addHook("'.$loader["event"].'", "'.$loader["class"].'");'."\n";

                } else {

                    echo "+ Enabling plugin ".$loader["class"]."::".$loader["method"]." on event ".$loader["event"]."\n";

                    $line_load .= '$extender->addHook("'.$loader["event"].'", "'.$loader["class"].'", "'.$loader["method"].'");'."\n";

                }

            }

        }
        else {

            if ( !isset($package_loader['method']) OR empty($package_loader["method"]) ) {

                echo "+ Enabling plugin ".$package_loader["class"]." on event ".$package_loader["event"]."\n";

                $line_load = '$extender->addHook("'.$package_loader["event"].'", "'.$package_loader["class"].'");'."\n";

            } else {

                echo "+ Enabling plugin ".$package_loader["class"]."::".$package_loader["method"]." on event ".$package_loader["event"]."\n";

                $line_load = '$extender->addHook("'.$package_loader["event"].'", "'.$package_loader["class"].'", "'.$package_loader["method"].'");'."\n";

            }

        }
        
        $to_append = "\n".$line_mark."\n".$line_load.$line_mark."\n";

        $action = file_put_contents(self::$extender_plugins_cfg, $to_append, FILE_APPEND | LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot activate plugin");

    }

    public static function unloadPlugin($package_name) {

        echo "- Disabling plugin ".$package_name."\n";

        $line_mark = "/****** PLUGIN - ".$package_name." - PLUGIN ******/";

        $cfg = file(self::$extender_plugins_cfg, FILE_IGNORE_NEW_LINES);

        $found = false;

        foreach ($cfg as $position => $line) {
            
            if ( stristr($line, $line_mark) ) {

                unset($cfg[$position]);

                $found = !$found;

            }

            else {

                if ( $found ) unset($cfg[$position]);
                else continue;

            }

        }

        $action = file_put_contents(self::$extender_plugins_cfg, implode("\n", array_values($cfg)), LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot deactivate plugin");

    }

    public static function loadTasks($package_name, $package_loader) {

        $line_mark = "/****** TASKS - ".$package_name." - TASKS ******/";

        if ( is_array($package_loader) ) {

            $line_load = "";

            foreach ($package_loader as $loader) {

                $name = $loader['name'];

                $class = $loader['class'];

                $description = isset($loader['description']) ? $loader['description'] : null;

                echo "+ Enabling task ".$name."\n";

                $line_load .= '$extender->addTask("'.$name.'", "'.$class.'", "'.$description.'");'."\n";

            }

        }
        else {

            $name = $package_loader['name'];

            $class = $package_loader['class'];

            $description = isset($package_loader['description']) ? $package_loader['description'] : null;

            echo "+ Enabling task ".$name."\n";

            $line_load = '$extender->addTask("'.$name.'", "'.$class.'", "'.$description.'");'."\n";

        }
        
        $to_append = "\n".$line_mark."\n".$line_load.$line_mark."\n";

        $action = file_put_contents(self::$extender_tasks_cfg, $to_append, FILE_APPEND | LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot activate tasks");

    }

    public static function unloadTasks($package_name) {

        echo "- Disabling tasks of ".$package_name."\n";

        $line_mark = "/****** TASKS - ".$package_name." - TASKS ******/";

        $cfg = file(self::$extender_tasks_cfg, FILE_IGNORE_NEW_LINES);

        $found = false;

        foreach ($cfg as $position => $line) {
            
            if ( stristr($line, $line_mark) ) {

                unset($cfg[$position]);

                $found = !$found;

            }

            else {

                if ( $found ) unset($cfg[$position]);
                else continue;

            }

        }

        $action = file_put_contents(self::$extender_tasks_cfg, implode("\n", array_values($cfg)), LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot deactivate tasks");

    }

    public static function loadCommands($package_name, $package_loader) {

        $line_mark = "/****** COMMANDS - ".$package_name." - COMMANDS ******/";

        $line_load = "";

        if ( is_array($package_loader) ) {

            foreach ($package_loader as $command => $actions) {

                $description = isset($actions["description"]) ? $actions["description"] : "";

                $aliases = array();

                if ( isset($actions["aliases"]) AND @is_array($actions["aliases"]) ) {

                    foreach ($actions["aliases"] as $alias) array_push($aliases, $alias);

                }

                $options = array();

                if ( isset($actions["options"]) AND @is_array($actions["options"]) ) {

                    foreach ($actions["options"] as $option => $oparameters) $options[$option] = $oparameters;

                }

                $arguments = array();

                if ( isset($actions["arguments"]) AND @is_array($actions["arguments"]) ) {

                    foreach ($actions["arguments"] as $argument => $aparameters) $arguments[$argument] = $aparameters;

                }

                $parameters = array(
                    "description" => $description, 
                    "aliases"     => $aliases,
                    "options"     => $options,
                    "arguments"   => $arguments
                );
                
                echo "+ Enabling command ".$command." (".$package_name.")\n";

                $line_load .= '$extender->addCommand("' . $command . '", ' . var_export($parameters, true) . ');'."\n";

            }

        }
        else throw new Exception("Wrong service loader");
        
        $to_append = "\n".$line_mark."\n".$line_load.$line_mark."\n";

        $action = file_put_contents(self::$extender_commands_cfg, $to_append, FILE_APPEND | LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot activate commands bundle");

    }

    public static function unloadCommands($package_name) {

        echo "- Disabling commands of ".$package_name."\n";

        $line_mark = "/****** COMMANDS - ".$package_name." - COMMANDS ******/";

        $cfg = file(self::$extender_commands_cfg, FILE_IGNORE_NEW_LINES);

        $found = false;

        foreach ($cfg as $position => $line) {
            
            if ( stristr($line, $line_mark) ) {

                unset($cfg[$position]);

                $found = !$found;

            }

            else {

                if ( $found ) unset($cfg[$position]);
                else continue;

            }

        }

        $action = file_put_contents(self::$extender_commands_cfg, implode("\n", array_values($cfg)), LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot deactivate commands bundle");

    }

}
