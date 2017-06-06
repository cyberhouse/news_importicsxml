<?php
namespace GeorgRinger\NewsImporticsxml\Mapper;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use GeorgRinger\NewsImporticsxml\Domain\Model\Dto\TaskConfiguration;

interface MapperInterface
{

    /**
     * @param TaskConfiguration $configuration
     * @return array
     */
    public function map(TaskConfiguration $configuration);

    /**
     * Get the import source identifier
     *
     * @return string
     */
    public function getImportSource();
}
