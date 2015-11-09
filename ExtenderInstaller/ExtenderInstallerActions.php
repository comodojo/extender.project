<?php namespace Comodojo\ExtenderInstaller;

use \Comodojo\ExtenderInstaller\AbstractInstaller;
use \Comodojo\ExtenderInstaller\ExtenderInstaller;
use \Comodojo\ExtenderInstaller\FileInstaller;
use \Composer\Script\Event;
use \Composer\Installer\PackageEvent;
use \Exception;

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

        require_once "vendor/mustangostang/spyc/Spyc.php";

        $plugins_actions = self::parseExtenderPluginExtra($extra);

        $commands_actions = self::parseExtenderCommandExtra($extra);

        $tasks_actions = self::parseExtenderTaskExtra($extra);

        $folders_actions = self::parseFolderExtra($extra);

        try {

            if ( !empty($plugins_actions) ) ExtenderInstaller::loadPlugin($name, $plugins_actions);

            if ( !empty($tasks_actions) ) ExtenderInstaller::loadTasks($name, $tasks_actions);

            if ( !empty($commands_actions) ) ExtenderInstaller::loadCommands($name, $commands_actions);

            if ( !empty($folders_actions) ) FileInstaller::createFolders($folders_actions);

        } catch (Exception $e) {
            
            throw $e;
            
        }

    }

    private static function packageUninstall($type, $name, $extra) {

        require_once "vendor/mustangostang/spyc/Spyc.php";

        $plugins_actions = self::parseExtenderPluginExtra($extra);

        $commands_actions = self::parseExtenderCommandExtra($extra);

        $tasks_actions = self::parseExtenderTaskExtra($extra);

        $folders_actions = self::parseFolderExtra($extra);

        try {

            if ( !empty($plugins_actions) ) ExtenderInstaller::unloadPlugin($name);

            if ( !empty($tasks_actions) ) ExtenderInstaller::unloadTasks($name);

            if ( !empty($commands_actions) ) ExtenderInstaller::unloadCommands($name);

            if ( !empty($folders_actions) ) FileInstaller::deleteFolders($folders_actions);

        } catch (Exception $e) {
            
            throw $e;
            
        }

    }

    private static function parseExtenderPluginExtra($extra) {

        if ( isset($extra["comodojo-plugins-load"]) ) {

            $return = $extra["comodojo-plugins-load"];

        } else if ( isset($extra["extender-plugin-load"]) ) {

            $return = $extra["extender-plugin-load"];

        } else {

            $return = array();

        }

        return $return;

    }

    private static function parseExtenderCommandExtra($extra) {

        if ( isset($extra["comodojo-commands-register"]) ) {

            $return = $extra["comodojo-commands-register"];

        } else if ( isset($extra["extender-command-register"]) ) {

            $return = $extra["extender-command-register"];

        } else {

            $return = array();

        }

        return $return;

    }

    private static function parseExtenderTaskExtra($extra) {

        if ( isset($extra["comodojo-tasks-register"]) ) {

            $return = $extra["comodojo-tasks-register"];

        } else if ( isset($extra["extender-task-register"]) ) {

            $return = $extra["extender-task-register"];

        } else {

            $return = array();

        }

        return $return;

    }

    private static function parseFolderExtra($extra) {

        if ( isset($extra["comodojo-folders-create"]) ) {

            $return = $extra["comodojo-folders-create"];

        } else if ( isset($extra["folder-create"]) ) {

            $return = $extra["folder-create"];

        } else {

            $return = array();

        }

        return $return;

    }

    private static function ascii() {

        echo file_get_contents("ExtenderInstaller/logo.ascii")."\n";

    }

}