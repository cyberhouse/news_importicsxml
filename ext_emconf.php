<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Import of ICS & XML to EXT:news',
	'description' => 'Import ICS & XML files via scheduler',
	'category' => 'backend',
	'author' => 'Georg Ringer',
	'author_email' => 'georg.ringer@cyberhouse.at',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'author_company' => 'Cyberhouse GmbH',
	'version' => '1.0.0',
	'constraints' =>
		array(
			'depends' => array(
				'news' => '3.2.0-3.3.99'
			),
			'conflicts' => array(),
			'suggests' => array(),
		),
	'_md5_values_when_last_written' => '',
);

