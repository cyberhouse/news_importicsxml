<?php

namespace Cyberhouse\NewsImporticsxml\Domain\Model;

class News extends \GeorgRinger\News\Domain\Model\News {

	/** @var string */
	protected $newsImportData;

	/**
	 * @return string
	 */
	public function getNewsImportData() {
		return $this->newsImportData;
	}

	/**
	 * @param string $newsImportData
	 */
	public function setNewsImportData($newsImportData) {
		$this->newsImportData = $newsImportData;
	}

}