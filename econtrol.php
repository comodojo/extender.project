#!/usr/bin/env php
<?php

use \Comodojo\Extender\ExtenderCommandlineController;

/**
 * Comodojo extender
 *
 * Database driven, multiprocess, pseudo-cron tasks executor.
 *
 * This is the commandline control center
 * 
 * @package     Comodojo extender
 * @author		Marco Giovinazzi <info@comodojo.org>
 * @license		GPL-3.0+
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

/*
 |--------------------------------
 | Load extender configuration
 |--------------------------------
 |
 | Load defined constants via extender-config
 |
 */
require "configs/extender-config.php";

/*
 |--------------------------------
 | Autoloader
 |--------------------------------
 |
 | Register the autoloader, located in vendor
 | directory. In a composer installation, this
 | will be handled directly with composer.
 |
 */
require 'vendor/autoload.php';

/*
 |--------------------------------
 | Init command line controller 
 |--------------------------------
 |
 | Create an instance of command line
 | controller
 |
 */
$extender = new ExtenderCommandlineController();

/*
 |--------------------------------
 | Load tasks
 |--------------------------------
 |
 | Load installed/declared tasks
 |
 */
require 'configs/tasks-config.php';

/*
 |--------------------------------
 | Load commands
 |--------------------------------
 |
 | Load installed/declared commands
 |
 */
require 'configs/commands-config.php';

/*
 |--------------------------------
 | Process command
 |--------------------------------
 |
 | Run commands to manage extender
 |
 */
$extender->process();
