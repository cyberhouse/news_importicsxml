<?php

namespace Cyberhouse\NewsImporticsxml\Aspect;

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

class NewsImportAspect
{

    /**
     * @param array $importData
     * @param \GeorgRinger\News\Domain\Model\News $news
     */
    public function postHydrate(array $importData, $news)
    {
        /** @var \Cyberhouse\NewsImporticsxml\Domain\Model\News $news */
        if (is_array($importData['_dynamicData']) && is_array($importData['_dynamicData']['news_importicsxml'])) {
            $metaData = array();
            foreach ($importData['_dynamicData']['news_importicsxml'] as $key => $value) {
                $metaData[] = $key . ': ' . $value;
            }
            $news->setNewsImportData(implode(LF, $metaData));
        }

    }

}