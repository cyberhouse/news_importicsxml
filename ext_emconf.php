<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Import of ICS & XML to EXT:news',
    'description' => 'Import ICS & XML files via scheduler',
    'category' => 'backend',
    'author' => 'Georg Ringer',
    'author_email' => 'georg.ringer@cyberhouse.at',
    'state' => 'beta',
    'uploadfolder' => true,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author_company' => 'ringer.it',
    'version' => '1.1.0',
    'constraints' =>
        [
            'depends' => [
                'typo3' => '6.2.0-8.5.99',
                'news' => '3.2.0-5.5.99'
            ],
            'conflicts' => [],
            'suggests' => [],
        ]
];
