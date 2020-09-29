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
    'version' => '4.0.0',
    'constraints' =>
        [
            'depends' => [
                'typo3' => '9.5.99-10.4.99',
                'news' => '8.0.0-8.99.99'
            ],
            'conflicts' => [],
            'suggests' => [],
        ]
];
