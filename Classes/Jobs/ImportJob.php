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

class ImportJob {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/** @var TaskConfiguration */
	protected $configuration;

	/** @var $logger \TYPO3\CMS\Core\Log\Logger */
	protected $logger;

	/**
	 * @param TaskConfiguration $configuration
	 */
	public function __construct(TaskConfiguration $configuration) {
		$this->logger = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
		$this->configuration = $configuration;
	}

	/**
	 * @return void
	 */
	public function run() {
		$this->logger->info(sprintf(
			'Starting import of "%s" (%s), reporting to "%s"',
			$this->configuration->getPath(),
			strtoupper($this->configuration->getFormat()),
			$this->configuration->getEmail()));

		switch ($this->configuration->getFormat()) {
			case 'xml':
				/** @var \Cyberhouse\NewsImporticsxml\Mapper\XmlMapper $xmlMapper */
				$xmlMapper = $this->objectManager->get('Cyberhouse\\NewsImporticsxml\\Mapper\\XmlMapper');
				$data = $xmlMapper->map($this->configuration);
				break;
			case 'ics':
				/** @var \Cyberhouse\NewsImporticsxml\Mapper\IcsMapper $icsMapper */
				$icsMapper = $this->objectManager->get('Cyberhouse\\NewsImporticsxml\\Mapper\\IcsMapper');
				$data = $icsMapper->map($this->configuration);
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
	protected function import(array $data) {
		/** @var \GeorgRinger\News\Domain\Service\NewsImportService $newsImportService */
		$newsImportService = $this->objectManager->get('GeorgRinger\\News\\Domain\\Service\\NewsImportService');
		$newsImportService->import($data);
	}
}