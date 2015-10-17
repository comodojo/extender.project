<?php namespace Comodojo\ExtenderInstaller;

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

abstract class AbstractInstaller {

	protected static $known_types = array('extender-plugins-bundle', 'extender-tasks-bundle', 'extender-commands-bundle', 'comodojo-bundle');

    protected static $reserved_folders = array('ExtenderInstaller','configs','commands','plugins','database','logs','tasks','vendor');

    protected static $vendor = 'vendor/';

    protected static $extender_plugins_cfg = 'configs/extender-plugins-config.php';

    protected static $extender_commands_cfg = 'configs/extender-commands-config.php';

    protected static $extender_tasks_cfg = 'configs/extender-tasks-config.php';

    protected static $mask = 0644;

}