<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Import of ICS & XML to EXT:news',
    'description' => 'Import ICS & XML files via scheduler',
    'category' => 'backend',
    'author' => 'Georg Ringer',
    'author_email' => 'georg.ringer@GeorgRinger.at',
    'state' => 'beta',
    'uploadfolder' => true,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author_company' => 'ringer.it',
    'version' => '2.0.0',
    'constraints' =>
        [
            'depends' => [
                'typo3' => '7.6.13-8.7.99',
                'news' => '6.0.0-6.5.99'
            ],
            'conflicts' => [],
            'suggests' => [],
        ]
];
