<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Import of ICS & XML to EXT:news',
    'description' => 'Versatile news import from ICS + XML (local files or remote URLs) including images and category mapping',
    'category' => 'backend',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'author_company' => 'ringer.it',
    'version' => '3.0.0',
    'constraints' =>
        [
            'depends' => [
                'typo3' => '8.7.0-9.9.99',
                'news' => '7.0.0-7.99.99'
            ],
            'conflicts' => [],
            'suggests' => [],
        ]
];
