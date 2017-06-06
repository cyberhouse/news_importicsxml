<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$fields = [
    'news_import_data' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:news_importicsxml/Resources/Private/Language/locallang.xlf:tx_news_domain_model_news.news_import_data',
        'config' => [
            'type' => 'text',
            'cols' => 60,
            'rows' => 50,
            'readOnly' => true
        ]
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $fields);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'news_import_data');
