<?php
namespace GeorgRinger\NewsImporticsxml\Tasks;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class ImportTaskAdditionalFieldProvider implements AdditionalFieldProviderInterface
{

    /**
     * @param array $taskInfo
     * @param AbstractTask $task
     * @param SchedulerModuleController $parentObject
     * @return array
     */
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $parentObject)
    {
        $additionalFields = [];
        $fields = [
            'format' => ['type' => 'select', 'options' => ['xml', 'ics']],
            'path' => ['type' => 'input'],
            'pid' => ['type' => 'input'],
            'mapping' => ['type' => 'textarea'],
            'email' => ['type' => 'input', 'default' => $GLOBALS['BE_USER']->user['email']],
        ];

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
                    $html = '<input class="form-control" type="text" name="tx_scheduler[' . $field . ']" id="' . $field . '" value="' . $value . '" size="30" />';
                    break;
                case 'textarea':
                    $html = '<textarea class="form-control" name="tx_scheduler[' . $field . ']" id="' . $field . '">' . $value . '</textarea>';
                    break;
                case 'select':
                    $options = [];
                    foreach ($configuration['options'] as $item) {
                        $options[] = sprintf(
                            '<option %s value="%s">%s</option>',
                            ($taskInfo[$field] === $item) ? 'selected="selected"' : '',
                            $item,
                            $this->translate($field . '.' . $item)
                        );
                    }
                    $html = '<select class="form-control" name="tx_scheduler[' . $field . ']" id="' . $field . '">' . implode(LF,
                            $options) . '</select>';
                    break;
            }
            $additionalFields[$field] = [
                'code' => $html,
                'label' => $this->translate($field)
            ];
        }
        return $additionalFields;
    }

    /**
     * @param array $submittedData
     * @param SchedulerModuleController $parentObject
     * @return bool
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject)
    {
        return $this->validate($submittedData, $parentObject);
    }

    /**
     * @param array $submittedData
     * @param AbstractTask $task
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        /** @var ImportTask $task */
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
    protected function translate($key)
    {
        /** @var LanguageService $languageService */
        $languageService = $GLOBALS['LANG'];
        return $languageService->sL('LLL:EXT:news_importicsxml/Resources/Private/Language/locallang.xlf:' . $key);
    }

    /**
     * @param array $data
     * @param SchedulerModuleController $parentObject
     * @return bool
     */
    protected function validate(array $data, SchedulerModuleController $parentObject)
    {
        $result = true;
        if (!empty($data['email']) && !GeneralUtility::validEmail($data['email'])) {
            $parentObject->addMessage($this->translate('msg.noEmail'), FlashMessage::ERROR);
            $result = false;
        }
        if (empty($data['path'])) {
            $parentObject->addMessage($this->translate('error.noValidPath'), FlashMessage::ERROR);
            $result = false;
        }
        if (empty($data['format'])) {
            $parentObject->addMessage($this->translate('error.noFormat'), FlashMessage::ERROR);
            $result = false;
        }
        if ((int)($data['pid']) === 0) {
            $parentObject->addMessage($this->translate('error.pid'), FlashMessage::ERROR);
            $result = false;
        }
        return $result;
    }
}
