<?php
namespace Cyberhouse\NewsImporticsxml\Tasks;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class ImportTaskAdditionalFieldProvider implements AdditionalFieldProviderInterface {

	/**
	 * @param array $taskInfo
	 * @param AbstractTask $task
	 * @param SchedulerModuleController $parentObject
	 * @return array
	 */
	public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $parentObject) {
		$additionalFields = array();
		$fields = array(
			'format' => array('type' => 'select', 'options' => array('xml', 'ics')),
			'path' => array('type' => 'input'),
			'pid' => array('type' => 'input'),
			'mapping' => array('type' => 'textarea'),
			'email' => array('type' => 'input', 'default' => $GLOBALS['BE_USER']->user['email']),
		);

		foreach ($fields as $field => $configuration) {
			if (empty($taskInfo[$field])) {
				if ($parentObject->CMD === 'add' && isset($configuration['default'])) {
					$taskInfo[$field] = $configuration['default'];
				} elseif ($parentObject->CMD === 'edit') {
					$taskInfo[$field] = $task->$field;
				} else {
					$taskInfo[$field] = '';
				}
			}

			$value = htmlspecialchars($taskInfo[$field]);
			$html = '';
			switch ($configuration['type']) {
				case 'input':
					$html = '<input type="text" name="tx_scheduler[' . $field . ']" id="' . $field . '" value="' . $value . '" size="30" />';
					break;
				case 'textarea':
					$html = '<textarea name="tx_scheduler[' . $field . ']" id="' . $field . '">' . $value . '</textarea>';
					break;
				case 'select':
					$options = array();
					foreach ($configuration['options'] as $item) {
						$options[] = sprintf(
							'<option %s value="%s">%s</option>',
							($taskInfo[$field] === $item) ? 'selected="selected"' : '',
							$item,
							$this->translate($field . '.' . $item)
						);
					}
					$html = '<select name="tx_scheduler[' . $field . ']" id="' . $field . '">' . implode(LF, $options) . '</select>';
					break;
			}
			$additionalFields[$field] = array(
				'code' => $html,
				'label' => $this->translate($field)
			);
		}
		return $additionalFields;
	}

	/**
	 * @param array $submittedData
	 * @param SchedulerModuleController $parentObject
	 * @return bool
	 */
	public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject) {
		return $this->validate($submittedData, $parentObject);
	}

	/**
	 * @param array $submittedData
	 * @param AbstractTask $task
	 */
	public function saveAdditionalFields(array $submittedData, AbstractTask $task) {
		/** @var \Cyberhouse\NewsImporticsxml\Tasks\ImportTask $task */
		$task->email = $submittedData['email'];
		$task->path = $submittedData['path'];
		$task->mapping = $submittedData['mapping'];
		$task->format = $submittedData['format'];
		$task->pid = $submittedData['pid'];
	}

	/**
	 * Helper method for translations
	 *
	 * @param string $key
	 * @return string
	 */
	protected function translate($key) {
		/** @var \TYPO3\CMS\Lang\LanguageService $languageService */
		$languageService = $GLOBALS['LANG'];
		return $languageService->sL('LLL:EXT:news_importicsxml/Resources/Private/Language/locallang.xlf:' . $key);
	}

	/**
	 * @param array $data
	 * @param SchedulerModuleController $parentObject
	 * @return array
	 */
	protected function validate(array $data, SchedulerModuleController $parentObject) {
		$result = TRUE;
		if (!empty($data['email']) && !GeneralUtility::validEmail($data['email'])) {
			$parentObject->addMessage($this->translate('msg.noEmail'), FlashMessage::ERROR);
			$result = FALSE;
		}
		if (empty($data['path'])) {
			$parentObject->addMessage($this->translate('error.noValidPath'), FlashMessage::ERROR);
			$result = FALSE;
		}
		if (empty($data['format'])) {
			$parentObject->addMessage($this->translate('error.noFormat'), FlashMessage::ERROR);
			$result = FALSE;
		}
		if ((int)($data['pid']) === 0) {
			$parentObject->addMessage($this->translate('error.pid'), FlashMessage::ERROR);
			$result = FALSE;
		}
		return $result;
	}

}