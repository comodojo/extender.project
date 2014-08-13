<?php namespace Comodojo\ExtenderInstaller;

/**
 * Dispatcher installer - a simple class (static methods) to manage plugin installations
 *
 * It currently supports:
 * - dispatcher-plugin - generic plugins such as tracer, database, ...
 * - dispatcher-service-bundle - service bundles
 * 
 * @package     Comodojo dispatcher
 * @author      comodojo <info@comodojo.org>
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

use Composer\Script\Event;
use \Exception;

class ExtenderInstallerActions {

    private static $vendor = 'vendor/';

    private static $plugins_cfg = 'configs/plugins-config.php';

    private static $commands_cfg = 'configs/commands-config.php';

    private static $tasks_cfg = 'configs/tasks-config.php';

    private static $known_types = array('extender-plugin', 'extender-tasks-bundle', 'extender-commands-bundle')

    private static $reserved_folders = Array('ExtenderInstaller','configs','commands','plugins','database','logs','tasks','vendor');

    private static $mask = 0644;

    public static function postPackageInstall(Event $event) {

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

    public static function postPackageUninstall(Event $event) {

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

    public static function postPackageUpdate(Event $event) {

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

            if ( $type == "extender-plugin" ) self::loadPlugin($name, $plugins_actions);

            if ( $type == "extender-tasks-bundle" ) self::loadTasks($name, $commands_actions);

            if ( $type == "extender-commands-bundle" ) self::loadCommands($name, $tasks_actions);

            self::create_folders($folders_actions);

        } catch (Exception $e) {
            
            throw $e;
            
        }

    }

    private static function packageUninstall($type, $name, $extra) {
        
        $folders_actions = isset($extra["comodojo-folders-create"]) ? $extra["comodojo-folders-create"] : Array();

        try {
            
            if ( $type == "extender-plugin" ) self::unloadPlugin($name);

            if ( $type == "extender-tasks-bundle" ) self::unloadTasks($name);

            if ( $type == "extender-commands-bundle" ) self::unloadCommands($name);

            self::delete_folders($folders_actions);

        } catch (Exception $e) {
            
            throw $e;
            
        }

    }

    private static function loadPlugin($package_name, $package_loader) {

        $line_mark = "/****** PLUGIN - ".$package_name." - PLUGIN ******/";

        list($author,$name) = explode("/", $package_name);

        $plugin_path = self::$vendor.$author."/".$name."/src/";

        if ( is_array($package_loader) ) {

            $line_load = "";

            foreach ($package_loader as $loader) {

                echo "+ Enabling plugin ".$loader."\n";

                $line_load .= '$extender->loadPlugin("'.$loader.'", "'.$plugin_path.'");'."\n";

            }

        }
        else {

            echo "+ Enabling plugin ".$package_loader."\n";

            $line_load = '$extender->loadPlugin("'.$package_loader.'", "'.$plugin_path.'");'."\n";

        }
        
        $to_append = "\n".$line_mark."\n".$line_load.$line_mark."\n";

        $action = file_put_contents(self::$plugins_cfg, $to_append, FILE_APPEND | LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot activate plugin");

    }

    private static function unloadPlugin($package_name) {

        echo "- Disabling plugin ".$package_name."\n";

        $line_mark = "/****** PLUGIN - ".$package_name." - PLUGIN ******/";

        $cfg = file(self::$plugins_cfg, FILE_IGNORE_NEW_LINES);

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

        $action = file_put_contents(self::$plugins_cfg, implode("\n", array_values($cfg)), LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot deactivate plugin");

    }

    private static function loadTasks($package_name, $package_loader) {

        $line_mark = "/****** TASKS - ".$package_name." - TASKS ******/";

        list($author,$name) = explode("/", $package_name);

        $tasks_path = self::$vendor.$author."/".$name."/tasks/";

        if ( is_array($package_loader) ) {

            $line_load = "";

            foreach ($package_loader as $loader) {

                $name = $loader['name'];

                $target = $tasks_path.$loader['target'];

                $description = isset($loader['description']) ? $loader['description'] : null;

                $class = isset($loader['class']) ? '"'.$loader['class'].'"' : 'null';

                echo "+ Enabling task ".$name."\n";

                $line_load .= '$extender->addTask("'.$name.'", "'.$target.'", "'.$description.'", '.$class.', false);'."\n";

            }

        }
        else {

            $name = $package_loader['name'];

            $target = $tasks_path.$package_loader['target'];

            $description = isset($package_loader['description']) ? $package_loader['description'] : null;

            $class = isset($package_loader['class']) ? '"'.$package_loader['class'].'"' : 'null';

            echo "+ Enabling task ".$name."\n";

            $line_load = '$extender->addTask("'.$name.'", "'.$target.'", "'.$description.'", '.$class.', false);'."\n";

        }
        
        $to_append = "\n".$line_mark."\n".$line_load.$line_mark."\n";

        $action = file_put_contents(self::$tasks_cfg, $to_append, FILE_APPEND | LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot activate tasks");

    }

    private static function unloadTasks($package_name) {

        echo "- Disabling tasks of ".$package_name."\n";

        $line_mark = "/****** TASKS - ".$package_name." - TASKS ******/";

        $cfg = file(self::$tasks_cfg, FILE_IGNORE_NEW_LINES);

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

        $action = file_put_contents(self::$tasks_cfg, implode("\n", array_values($cfg)), LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot deactivate tasks");

    }

    private static function loadCommands($package_name, $package_loader) {

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

                $parameters = array($description, $aliases, $options, $arguments);
                
                echo "+ Enabling command ".$command." (".$package_name.")\n";

                $line_load .= '$extender->addCommand(' . var_export($parameters, true) . ');'."\n";

            }

        }
        else throw new Exception("Wrong service loader");
        
        $to_append = "\n".$line_mark."\n".$line_load.$line_mark."\n";

        $action = file_put_contents(self::$commands_cfg, $to_append, FILE_APPEND | LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot activate commands bundle");

    }

    private static function unloadCommands($package_name) {

        echo "- Disabling commands of ".$package_name."\n";

        $line_mark = "/****** COMMANDS - ".$package_name." - COMMANDS ******/";

        $cfg = file(self::$commands_cfg, FILE_IGNORE_NEW_LINES);

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

        $action = file_put_contents(self::$commands_cfg, implode("\n", array_values($cfg)), LOCK_EX);

        if ( $action === false ) throw new Exception("Cannot deactivate commands bundle");

    }

    private static function create_folders($folders) {

        if ( is_array($folders) ) {

            foreach ($folders as $folder) {
                
                if ( in_array($folder, self::$reserved_folders) ) throw new Exception("Cannot overwrite reserved folder!");

                echo "+ Creating folder ".$folder."\n";

                $action = mkdir($folder, self::$mask, true);

                if ( $action === false ) throw new Exception("Error creating folder ".$folder);

            }

        }

        else {

            if ( in_array($folders, self::$reserved_folders) ) throw new Exception("Cannot overwrite reserved folder!");

            echo "+ Creating folder ".$folders."\n";

            $action = mkdir($folders, self::$mask, true);

            if ( $action === false ) throw new Exception("Error creating folder ".$folders);

        }

        echo "+ PLEASE REMEMBER to chmod and/or chown created folders according to your needs.\n";

    }

    private static function delete_folders($folders) {
        
        if ( is_array($folders) ) {

            foreach ($folders as $folder) {
                
                if ( in_array($folder, self::$reserved_folders) ) throw new Exception("Cannot delete reserved folder!");

                echo "- deleting folder ".$folder."\n";

                try {

                    self::recursive_unlink($folder);
                    
                } catch (Exception $e) {
                    
                    throw $e;

                }

            }

        }

        else {

            if ( in_array($folders, self::$reserved_folders) ) throw new Exception("Cannot overwrite reserved folder!");

            echo "- deleting folder ".$folders."\n";

            try {

                self::recursive_unlink($folders);
                
            } catch (Exception $e) {
                
                throw $e;

            }

        }

    }

    private static function recursive_unlink($folder) {

        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            
            $pathname = $path->getPathname();

            if ( $path->isDir() ) {

                $action = rmdir($pathname);

            } 
            else {

                $action = unlink($pathname);

            }

            if ( $action === false ) throw new Exception("Error deleting ".$pathname." during recursive unlink of folder ".$folder);

        }

        $action = rmdir($folder);

        if ( $action === false ) throw new Exception("Error deleting folder ".$folder);

    }

    private static function ascii() {

        $ascii = "\n   ______                                 __              __        \r\n";
        $ascii .= "  / ____/  ____    ____ ___   ____   ____/ /  ____       / /  ____ \r\n";
        $ascii .= " / /      / __ \  / __ `__ \ / __ \ / __  /  / __ \     / /  / __ \ \r\n";
        $ascii .= "/ /___   / /_/ / / / / / / // /_/ // /_/ /  / /_/ /    / /  / /_/ /\r\n";
        $ascii .= "\____/   \____/ /_/ /_/ /_/ \____/ \__,_/   \____/  __/ /   \____/ \r\n";
        $ascii .= "-------------------------------------------------  /___/  ---------\r\n";
        $ascii .= "                 __                      __                        \r\n";
        $ascii .= "  ___    _  __  / /_  ___    ____   ____/ /  ___    _____          \r\n";
        $ascii .= " / _ \  | |/_/ / __/ / _ \  / __ \ / __  /  / _ \  / ___/          \r\n";
        $ascii .= "/  __/ _>  <  / /_  /  __/ / / / // /_/ /  /  __/ / /              \r\n";
        $ascii .= "\___/ /_/|_|  \__/  \___/ /_/ /_/ \__,_/   \___/ /_/               \r\n";
        $ascii .= "--------------------------------------------------------           \r\n\n";
        
        echo $ascii;

    }

}