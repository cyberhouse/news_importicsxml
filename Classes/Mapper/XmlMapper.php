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
use PicoFeed\Reader\Reader;

class XmlMapper extends AbstractMapper implements MapperInterface {

	/**
	 * @param TaskConfiguration $configuration
	 * @return array
	 */
	public function map(TaskConfiguration $configuration) {
		$data = array();

		$reader = new Reader();
		$resource = $reader->discover($configuration->getPath());

		$parser = $reader->getParser(
			$resource->getUrl(),
			$resource->getContent(),
			$resource->getEncoding()
		);

		$items = $parser->execute()->getItems();

		foreach ($items as $item) {
			/** @var \PicoFeed\Parser\Item $item */
			$data[] = array(
				'import_source' => $this->getImportSource(),
				'import_id' => $item->getId(),
				'crdate' => $GLOBALS['EXEC_TIME'],
				'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
				'pid' => $configuration->getPid(),
				'title' => $item->getTitle(),
				'bodytext' => $this->cleanup($item->getContent()),
				'author' => $item->getAuthor(),
				'datetime' => $item->getDate()->getTimestamp()
			);
		}

		return $data;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function cleanup($content) {
		$search = array('<br />', '<br>', '<br/>', LF . LF);
		$replace = array(LF, LF, LF, LF);
		$out = str_replace($search, $replace, $content);
		return $out;
	}

	/**
	 * @return string
	 */
	public function getImportSource() {
		return 'newsimporticsxml_xml';
	}

}