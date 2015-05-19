<?php
namespace Cyberhouse\NewsImporticsxml\Mapper;

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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\HttpRequest;

class IcsMapper extends AbstractMapper implements MapperInterface {

	/**
	 * @param TaskConfiguration $configuration
	 * @return array
	 */
	public function map(TaskConfiguration $configuration) {
		$data = array();
		$path = $this->getFileContent($configuration);

		GeneralUtility::requireOnce(ExtensionManagementUtility::extPath('news_importicsxml') . 'Resources/Private/Contrib/Ical.php');
		$iCalService = new \ICal($path);
		$events = $iCalService->events();

		foreach ($events as $event) {
			$data[] = array(
				'import_source' => $this->getImportSource(),
				'import_id' => md5($event['UID']),
				'crdate' => $GLOBALS['EXEC_TIME'],
				'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
				'pid' => $configuration->getPid(),
				'title' => $this->cleanup($event['SUMMARY']),
				'bodytext' => $this->cleanup($event['DESCRIPTION']),
				'datetime' => $iCalService->iCalDateToUnixTimestamp($event['DTSTART']),
				'_dynamicData' => array(
					'location' => $event['LOCATION'],
					'datetime_end' => $iCalService->iCalDateToUnixTimestamp($event['DTEND']),
					'news_importicsxml' => array(
						'LOCATION' => $event['LOCATION'],
						'DTEND' => $event['DTEND'],
						'SEQUENCE' => $event['SEQUENCE'],
						'STATUS' => $event['STATUS'],
						'TRANSP' => $event['TRANSP'],
					)
				),
			);
		}

		if ($configuration->getPath() !== $path) {
			unlink($path);
		}

		return $data;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function cleanup($content) {
		$search = array('\\,');
		$replace = array(',');

		return str_replace($search, $replace, $content);
	}

	/**
	 * @param TaskConfiguration $configuration
	 * @return array
	 */
	protected function getFileContent(TaskConfiguration $configuration) {
		$temporaryCopyPath = '';
		$path = $configuration->getPath();
		if (GeneralUtility::isFirstPartOfStr($path, 'http://') || GeneralUtility::isFirstPartOfStr($path, 'https://')) {
			$content = $this->apiCall($path);

			$temporaryCopyPath = PATH_site . 'typo3temp/' . md5($path . $GLOBALS['EXEC_TIME']);
			GeneralUtility::writeFileToTypo3tempDir($temporaryCopyPath, $content);
		}
		return $temporaryCopyPath;
	}


	protected function apiCall($url) {
		$config = array(
			'follow_redirects' => TRUE,
			'strict_redirects' => TRUE
		);

		/** @var $request HttpRequest */
		$request = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Http\\HttpRequest', $url, 'GET', $config);
		$response = $request->send();

		if ((int)$response->getStatus() !== 200) {
			$message = sprintf('URL "%s" is not reachable, got response status %s!', $url, $response->getStatus());
			$this->logger->alert($message);
			throw new \RuntimeException($message);
		}

		$body = $response->getBody();
		if (empty($body)) {
			$message = sprintf('URL "%s" returned an empty content!', $url);
			$this->logger->alert($message);
			throw new \RuntimeException($message);
		}
		return $body;
	}

	/**
	 * @return string
	 */
	public function getImportSource() {
		return 'newsimporticsxml_ics';
	}

}