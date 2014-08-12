<?php

define("EXTENDER_REAL_PATH",realpath(dirname(__FILE__))."/../");
define("EXTENDER_LOG_FOLDER",EXTENDER_REAL_PATH."logs/");
define("EXTENDER_DATABASE_FOLDER",EXTENDER_REAL_PATH."database/");
define("EXTENDER_TASK_FOLDER",EXTENDER_REAL_PATH."tasks/");

define("EXTENDER_DATABASE_MODEL","SQLITE_PDO");
define("EXTENDER_DATABASE_HOST","localhost");
define("EXTENDER_DATABASE_PORT",1);
define("EXTENDER_DATABASE_NAME",EXTENDER_DATABASE_FOLDER."extender.sqlite");
define("EXTENDER_DATABASE_USER","comodojo");
define("EXTENDER_DATABASE_PASS","");
define("EXTENDER_DATABASE_PREFIX","extender_");
define("EXTENDER_DATABASE_TABLE_JOBS","jobs");
define("EXTENDER_DATABASE_TABLE_WORKLOGS","worklogs");
//define("EXTENDER_MAX_RESULT_BYTES","");
//define("EXTENDER_MAX_CHILDS_RUNTIME","");
define("EXTENDER_MULTITHREAD_ENABLED",true);
//define("EXTENDER_LOG_ENABLED","");
//define("EXTENDER_LOG_NAME","");
//define("EXTENDER_LOG_LEVEL","");
//define("EXTENDER_LOG_TARGET","");
//define("EXTENDER_LOG_FOLDER","");

//EXTENDER_TIMEZONE