<?php
namespace Cyberhouse\NewsImporticsxml\Jobs;

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

use Cyberhouse\NewsImporticsxml\Domain\Model\Dto\TaskConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportJob
{

    /**
     * @var TaskConfiguration
     */
    protected $configuration;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var \Cyberhouse\NewsImporticsxml\Mapper\XmlMapper
     * @inject
     */
    protected $xmlMapper;

    /**
     * @var \Cyberhouse\NewsImporticsxml\Mapper\IcsMapper
     * @inject
     */
    protected $icsMapper;

    /**
     * @var \GeorgRinger\News\Domain\Service\NewsImportService
     * @inject
     */
    protected $newsImportService;

    /**
     * @param TaskConfiguration $configuration
     */
    public function __construct(TaskConfiguration $configuration)
    {
        $this->logger = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        $this->configuration = $configuration;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->logger->info(sprintf(
            'Starting import of "%s" (%s), reporting to "%s"',
            $this->configuration->getPath(),
            strtoupper($this->configuration->getFormat()),
            $this->configuration->getEmail()));

        switch (strtolower($this->configuration->getFormat())) {
            case 'xml':
                $data = $this->xmlMapper->map($this->configuration);
                break;
            case 'ics':
                $data = $this->icsMapper->map($this->configuration);
                break;
            default:
                $message = sprintf('Format "%s" is not supported!', $this->configuration->getFormat());
                $this->logger->critical($message);
                throw new \UnexpectedValueException($message);
        }

        $this->import($data);
    }

    /**
     * @param array $data
     * @return void
     */
    protected function import(array $data = null)
    {
        $this->logger->info(sprintf('Starting import of %s records', count($data)));

        $this->newsImportService->import($data);
    }
}
