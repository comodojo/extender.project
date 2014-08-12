<?php

$extender->addCommand("install", array(
	'description' => 'Perform first installation task',
	'aliases'     => array('inst','in'),
	'options'	  => array(
		'force'	=>	array(
		   'short_name'  => '-f',
		   'long_name'   => '--force',
		   'action'      => 'StoreTrue',
		   'description' => 'Force installation re-creating database'
		),
		'clean'	=>	array(
		   'short_name'  => '-c',
		   'long_name'   => '--clean',
		   'action'      => 'StoreTrue',
		   'description' => 'Drain database tables only'
		)
	),
	'arguments'	  => array()
));

$extender->addCommand("tasks", array(
	'description' => 'List available tasks',
	'aliases'     => array('task','tk'),
	'options'	  => array(),
	'arguments'	  => array()
));

$extender->addCommand("execute", array(
	'description' => 'Exec a tasks',
	'aliases'     => array('ex','exec'),
	'options'	  => array(),
	'arguments'	  => array(
		"task"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Task to execute'
		)
	)
));

$extender->addCommand("jobs", array(
	'description' => 'List scheduled jobs',
	'aliases'     => array('job','jb'),
	'options'	  => array(),
	'arguments'	  => array()
));

$extender->addCommand("add", array(
	'description' => 'Add a job to scheduler',
	'aliases'     => array('ad'),
	'options'	  => array(
		'enable'	=>	array(
		   'short_name'  => '-e',
		   'long_name'   => '--enable',
		   'action'      => 'StoreTrue',
		   'description' => 'Enable job too'
		)
	),
	'arguments'	  => array(
		"expression"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Cron expression'
		),
		"name"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Job name'
		),
		"task"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Task to execute'
		),
		"description"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> true,
			"description"	=> 'A brief description of job'
		)
	)
));

$extender->addCommand("del", array(
	'description' => 'Delete a job',
	'aliases'     => array('de'),
	'options'	  => array(),
	'arguments'	  => array(
		"name"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Job name'
		)
	)
));

$extender->addCommand("enable", array(
	'description' => 'Enable a job',
	'aliases'     => array('en'),
	'options'	  => array(),
	'arguments'	  => array(
		"name"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Job name'
		)
	)
));

$extender->addCommand("disable", array(
	'description' => 'Disable a job',
	'aliases'     => array('di'),
	'options'	  => array(),
	'arguments'	  => array(
		"name"	=>	array(
			"choices"		=> array(),
			"multiple"		=> false,
			"optional"		=> false,
			"description"	=> 'Job name'
		)
	)
));