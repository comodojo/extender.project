<?php

/**
 * This is the main extender configuration.
 *
 * @package     Comodojo extender
 * @author      Marco Giovinazzi <info@comodojo.org>
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

######## BEGIN EXTENDER INTERNAL PROPERTIES ########

/**
 * Local timezone, to not rely on the system's timezone settings
 *
 * It is STRONGLY reccomended to set this parameter properly
 *
 * @static  string
 */
define("EXTENDER_TIMEZONE", "Europe/Rome");

/**
 * Extender real path
 *
 * @static  string
 */
define("EXTENDER_REAL_PATH", realpath(dirname(__FILE__))."/../");

/**
 * Enable/disable multithread mode; this feaure REQUIRE PHP Process
 * Control extension (PCNTL)
 *
 * @static  bool
 */
define("EXTENDER_MULTITHREAD_ENABLED", true);

/**
 * Max bytes extender should read from completed child processes, if
 * multithread is enabled
 *
 * @static  integer
 */
define("EXTENDER_MAX_RESULT_BYTES", 2048);

/**
 * Child process max runtime, in seconds (default 10min)
 *
 * @static  integer
 */
define("EXTENDER_MAX_CHILDS_RUNTIME", 600);

/**
 * Parent process niceness (if in multithread mode)
 *
 * Values < 0 may require a privileged user!
 *
 * @static  integer
 */
define("EXTENDER_PARENT_NICENESS", 0);

/**
 * Child processes niceness (if in multithread mode)
 *
 * Values < 0 may require a privileged user!
 *
 * @static  integer
 */
define("EXTENDER_CHILDS_NICENESS", 0);

######## END EXTENDER INTERNAL PROPERTIES ########


######## BEGIN EXTENDER LOG PROPERTIES ########

/**
 * Enable/disable logger
 *
 * @static  bool
 */
define("EXTENDER_LOG_ENABLED", false);

/**
 * Logger name
 *
 * @static  string
 */
define("EXTENDER_LOG_NAME", "extender");

/**
 * Log target
 *
 * - if NULL, logger will log to standard output (use as an alternative of --verbose option)
 * - if string, it will be the filename to log to
 *
 * PLEASE NOTE: verify filesystem permissions on log folder BEFORE enabling file logging
 *
 * @static  string
 */
define("EXTENDER_LOG_TARGET", "extender.log");

/**
 * Log level, as in http://www.php-fig.org/psr/psr-3/
 *
 * @static  string
 */
define("EXTENDER_LOG_LEVEL", "ERROR");

######## END EXTENDER LOG PROPERTIES ########


######## BEGIN DISPATCHER FOLDERS ########

/**
 * Logs folder
 * 
 * @static  string
 */
define("EXTENDER_LOG_FOLDER", EXTENDER_REAL_PATH."logs/");

/**
 * Database folder (if sqlite3)
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_FOLDER", EXTENDER_REAL_PATH."database/");

/**
 * Tasks folder
 * 
 * @static  string
 */
define("EXTENDER_TASK_FOLDER", EXTENDER_REAL_PATH."tasks/");

/**
 * Plugins folder
 * 
 * @static  string
 */
define("EXTENDER_PLUGIN_FOLDER", EXTENDER_REAL_PATH."plugins/");

/**
 * Commands folder
 * 
 * @static  string
 */
define("EXTENDER_COMMAND_FOLDER", EXTENDER_REAL_PATH."commands/");

######### END DISPATCHER FOLDERS #########


######## BEGIN EXTENDER DATABASE PROPERTIES ########

/**
 * Database model
 *
 * Currently, extender is tested on MySQL and SQLite3 databases, but may work also
 * with models supported by comodojo/database lib.
 *
 * Safe choices: MYSQLI, MYSQL_PDO or SQLITE_PDO (default)
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_MODEL", "SQLITE_PDO");

/**
 * Database host
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_HOST", "localhost");

/**
 * Database port
 * 
 * @static  integer
 */
define("EXTENDER_DATABASE_PORT",1);

/**
 * Database name
 *
 * In case of SQLITE_PDO database model, name SHOULD contain full path to db file
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_NAME", EXTENDER_DATABASE_FOLDER."extender.sqlite");

/**
 * Database user
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_USER", "comodojo");

/**
 * Database password
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_PASS", "");

/**
 * Database tables' prefix
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_PREFIX", "extender_");

/**
 * Jobs table name
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_TABLE_JOBS", "jobs");

/**
 * Worklogs table name
 * 
 * @static  string
 */
define("EXTENDER_DATABASE_TABLE_WORKLOGS", "worklogs");

######### END EXTENDER DATABASE PROPERTIES #########
