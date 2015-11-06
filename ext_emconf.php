<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Import of ICS & XML to EXT:news',
	'description' => 'Import ICS & XML files via scheduler',
	'category' => 'backend',
	'author' => 'Georg Ringer',
	'author_email' => 'georg.ringer@cyberhouse.at',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 1,
	'author_company' => 'Cyberhouse GmbH',
	'version' => '1.0.1',
	'constraints' =>
		array(
			'depends' => array(
				'typo3' => '6.2.0-7.6.99',
				'news' => '3.2.0-3.3.99'
			),
			'conflicts' => array(),
			'suggests' => array(),
		)
);

