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

abstract class AbstractInstaller {

	protected static $known_types = array('extender-plugins-bundle', 'extender-tasks-bundle', 'extender-commands-bundle');

    protected static $reserved_folders = Array('ExtenderInstaller','configs','commands','plugins','database','logs','tasks','vendor');

    protected static $vendor = 'vendor/';

    protected static $plugins_cfg = 'configs/plugins-config.php';

    protected static $commands_cfg = 'configs/commands-config.php';

    protected static $tasks_cfg = 'configs/tasks-config.php';

    protected static $mask = 0644;

}