<?php
declare(strict_types=1);

namespace GeorgRinger\NewsImporticsxml\Tasks;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Scheduler\Task\Enumeration\Action;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class ImportTaskAdditionalFieldProvider extends AbstractAdditionalFieldProvider
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
//            'email' => ['type' => 'input', 'default' => $GLOBALS['BE_USER']->user['email']],
            'persistAsExternalUrl' => ['type' => 'checkbox'],
            'cleanBeforeImport' => ['type' => 'checkbox'],
        ];
        $currentAction = $parentObject->getCurrentAction();
        foreach ($fields as $field => $configuration) {
            if (empty($taskInfo[$field])) {
                if ($currentAction->equals(Action::ADD) && isset($configuration['default'])) {
                    $taskInfo[$field] = $configuration['default'];
                } elseif ($currentAction->equals(Action::EDIT)) {
                    $taskInfo[$field] = $task->$field;
                } else {
                    $taskInfo[$field] = '';
                }
            }

            $value = htmlspecialchars((string)$taskInfo[$field]);
            $html = '';
            switch ($configuration['type']) {
                case 'input':
                    $html = '<input class="form-control" type="text" name="tx_scheduler[' . $field . ']" id="' . $field . '" value="' . $value . '" size="30" />';
                    break;
                case 'checkbox':
                    $checked = $value === '1' ? 'checked' : '';
                    $html = '<input class="checkbox" type="checkbox" name="tx_scheduler[' . $field . ']" id="' . $field . '" value="1" ' . $checked . ' />';
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
     * @param array $data
     * @param SchedulerModuleController $parentObject
     * @return bool
     */
    public function validateAdditionalFields(array &$data, SchedulerModuleController $parentObject)
    {
        $result = true;
        if (!empty($data['email']) && !GeneralUtility::validEmail($data['email'])) {
            $this->addMessage($this->translate('msg.noEmail'), FlashMessage::ERROR);
            $result = false;
        }
        if (empty($data['path'])) {
            $this->addMessage($this->translate('error.noValidPath'), FlashMessage::ERROR);
            $result = false;
        }
        if (empty($data['format'])) {
            $this->addMessage($this->translate('error.noFormat'), FlashMessage::ERROR);
            $result = false;
        }
        if ((int)($data['pid']) === 0) {
            $this->addMessage($this->translate('error.pid'), FlashMessage::ERROR);
            $result = false;
        }
        return $result;
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
        $task->persistAsExternalUrl = $submittedData['persistAsExternalUrl'];
        $task->cleanBeforeImport = $submittedData['cleanBeforeImport'];
    }

    /**
     * Helper method for translations
     *
     * @param string $key
     * @return string
     */
    protected function translate(string $key): string
    {
        /** @var LanguageService $languageService */
        $languageService = $GLOBALS['LANG'];
        return $languageService->sL('LLL:EXT:news_importicsxml/Resources/Private/Language/locallang.xlf:' . $key);
    }
}
