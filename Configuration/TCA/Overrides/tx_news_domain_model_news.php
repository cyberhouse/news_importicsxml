<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$fields = array(
	'news_import_data' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:news_importicsxml/Resources/Private/Language/locallang.xlf:tx_news_domain_model_news.news_import_data',
		'config' => array(
			'type' => 'text',
			'cols' => 60,
			'rows' => 50,
			'readOnly' => TRUE
		)
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $fields);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'news_import_data');
