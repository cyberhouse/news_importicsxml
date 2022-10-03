<?php
declare(strict_types=1);

namespace GeorgRinger\NewsImporticsxml\Mapper;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractMapper
{

    /** @var $logger Logger */
    protected $logger;

    /** @var SlugHelper */
    protected $slugHelper;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $fieldConfig = $GLOBALS['TCA']['tx_news_domain_model_news']['columns']['path_segment']['config'];
        $this->slugHelper = GeneralUtility::makeInstance(SlugHelper::class, 'tx_news_domain_model_news', 'path_segment', $fieldConfig);
    }

    protected function generateSlug(array $fullRecord, int $pid)
    {
        $value = $this->slugHelper->generate($fullRecord, $pid);

        $state = RecordStateFactory::forName('tx_news_domain_model_news')
            ->fromArray($fullRecord, $pid, 0);
        $tcaFieldConf = $GLOBALS['TCA']['tx_news_domain_model_news']['columns']['path_segment']['config'];
        $evalCodesArray = GeneralUtility::trimExplode(',', $tcaFieldConf['eval'], true);
        if (in_array('unique', $evalCodesArray, true)) {
            $value = $this->slugHelper->buildSlugForUniqueInTable($value, $state);
        }
        if (in_array('uniqueInSite', $evalCodesArray, true)) {
            $value = $this->slugHelper->buildSlugForUniqueInSite($value, $state);
        }
        if (in_array('uniqueInPid', $evalCodesArray, true)) {
            $value = $this->slugHelper->buildSlugForUniqueInPid($value, $state);
        }

        return $value;
    }

    protected function removeImportedRecordsFromPid(int $pid, string $importSource): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_news_domain_model_news');
        $connection->delete(
            'tx_news_domain_model_news',
            [
                'deleted' => 0,
                'pid' => $pid,
                'import_source' => $importSource,
            ]
        );
    }
}
