<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Cyberhouse\\NewsImporticsxml\\Tasks\\ImportTask'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:task.name',
	'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:task.description',
	'additionalFields' => \Cyberhouse\NewsImporticsxml\Tasks\ImportTaskAdditionalFieldProvider::class
);

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->connect(
	'GeorgRinger\\News\\Domain\\Service\\NewsImportService',
	'postHydrate',
	'Cyberhouse\\NewsImporticsxml\\Aspect\\NewsImportAspect',
	'postHydrate'
);
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = $_EXTKEY;