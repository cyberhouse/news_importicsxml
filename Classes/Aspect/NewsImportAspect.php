<?php
declare(strict_types=1);
namespace GeorgRinger\NewsImporticsxml\Aspect;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use GeorgRinger\NewsImporticsxml\Domain\Model\News;

/**
 * Persist dynamic data of import
 */
class NewsImportAspect
{

    /**
     * @param array $importData
     * @param \GeorgRinger\News\Domain\Model\News $news
     */
    public function postHydrate(array $importData, $news)
    {
        /** @var News $news */
        if (is_array($importData['_dynamicData']) && is_array($importData['_dynamicData']['news_importicsxml'])) {
            $news->setNewsImportData(json_encode($importData['_dynamicData']['news_importicsxml']));
        }
    }
}
