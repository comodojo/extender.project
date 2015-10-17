<?php namespace Comodojo\ExtenderInstaller;

/**
 * Extender installer - a simple class (static methods) to manage plugin/bundles installations
 *
 * It currently supports:
 * - extender-plugin - generic plugins
 * - extender-tasks-bundle - tasks bundles
 * - extender-commands-bundle - commands bundles
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

use \Comodojo\ExtenderInstaller\AbstractInstaller;
use \Comodojo\ExtenderInstaller\ExtenderInstaller;
use \Comodojo\ExtenderInstaller\FileInstaller;
use \Composer\Script\Event;
use \Composer\Installer\PackageEvent;
use \Exception;

class ExtenderInstallerActions extends AbstractInstaller {

    public static function postPackageInstall(PackageEvent $event) {

        $type = $event->getOperation()->getPackage()->getType();

        $name = $event->getOperation()->getPackage()->getName();

        $extra = $event->getOperation()->getPackage()->getExtra();

        if ( !in_array($type, self::$known_types) ) return;

        self::ascii();

        try {
            
            self::packageInstall($type, $name, $extra);

        } catch (Exception $e) {

            throw $e;
            
        }

        echo "* ExtenderInstaller install task completed\n\n";

    }

    public static function postPackageUninstall(PackageEvent $event) {

        $type = $event->getOperation()->getPackage()->getType();

        $name = $event->getOperation()->getPackage()->getName();

        $extra = $event->getOperation()->getPackage()->getExtra();

        if ( !in_array($type, self::$known_types) ) return;

        self::ascii();

        try {
            
            self::packageUninstall($type, $name, $extra);

        } catch (Exception $e) {

            throw $e;
            
        }

        echo "* ExtenderInstaller uninstall task completed\n\n";

    }

    public static function postPackageUpdate(PackageEvent $event) {

        $initial_package = $event->getOperation()->getInitialPackage();

        $initial_package_type = $initial_package->getType();

        $initial_package_name = $initial_package->getName();

        $initial_package_extra = $initial_package->getExtra();

        $target_package  = $event->getOperation()->getTargetPackage(); 

        $target_package_type = $target_package->getType();

        $target_package_name = $target_package->getName();

        $target_package_extra = $target_package->getExtra();

        if ( !in_array($initial_package_type, self::$known_types) AND !in_array($target_package_type, self::$known_types) ) return;

        self::ascii();

        try {
            
            self::packageUninstall($initial_package_type, $initial_package_name, $initial_package_extra);

            self::packageInstall($target_package_type, $target_package_name, $target_package_extra);

        } catch (Exception $e) {
            
            throw $e;

        }

        echo "* ExtenderInstaller update task completed\n\n";

    }

    private static function packageInstall($type, $name, $extra) {

        $plugins_actions = isset($extra["comodojo-plugins-load"]) ? $extra["comodojo-plugins-load"] : Array();

        $commands_actions = isset($extra["comodojo-commands-register"]) ? $extra["comodojo-commands-register"] : Array();

        $tasks_actions = isset($extra["comodojo-tasks-register"]) ? $extra["comodojo-tasks-register"] : Array();

        $folders_actions = isset($extra["comodojo-folders-create"]) ? $extra["comodojo-folders-create"] : Array();

        try {

            if ( !empty($plugins_actions) ) ExtenderInstaller::loadPlugin($name, $plugins_actions);

            if ( !empty($tasks_actions) ) ExtenderInstaller::loadTasks($name, $tasks_actions);

            if ( !empty($commands_actions) ) ExtenderInstaller::loadCommands($name, $commands_actions);

            if ( !empty($folders_actions) ) FileInstaller::create_folders($folders_actions);

        } catch (Exception $e) {
            
            throw $e;
            
        }

    }

    private static function packageUninstall($type, $name, $extra) {

        $plugins_actions = isset($extra["comodojo-plugins-load"]) ? $extra["comodojo-plugins-load"] : Array();

        $commands_actions = isset($extra["comodojo-commands-register"]) ? $extra["comodojo-commands-register"] : Array();

        $tasks_actions = isset($extra["comodojo-tasks-register"]) ? $extra["comodojo-tasks-register"] : Array();
        
        $folders_actions = isset($extra["comodojo-folders-create"]) ? $extra["comodojo-folders-create"] : Array();

        try {

            if ( !empty($plugins_actions) ) ExtenderInstaller::unloadPlugin($name);

            if ( !empty($tasks_actions) ) ExtenderInstaller::unloadTasks($name);

            if ( !empty($commands_actions) ) ExtenderInstaller::unloadCommands($name);

            if ( !empty($folders_actions) ) FileInstaller::delete_folders($folders_actions);

        } catch (Exception $e) {
            
            throw $e;
            
        }

    }

    private static function ascii() {

        echo file_get_contents("ExtenderInstaller/logo.ascii")."\n";

    }

}