<?php
namespace Cyberhouse\NewsImporticsxml\Domain\Model\Dto;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class TaskConfiguration {

	/** @var string */
	protected $email;

	/** @var string */
	protected $path;

	/** @var string */
	protected $mapping;

	/** @var string */
	protected $format;

	/** @var string */
	protected $pid;

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getMapping() {
		return $this->mapping;
	}

	/**
	 * @param string $mapping
	 */
	public function setMapping($mapping) {
		$this->mapping = $mapping;
	}

	/**
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format) {
		$this->format = $format;
	}

	/**
	 * @return string
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * @param string $pid
	 */
	public function setPid($pid) {
		$this->pid = $pid;
	}

	/**
	 * Split the configuration from multiline to array
	 * 123:This is a category title
	 * 345:And another one
	 *
	 * @return array
	 */
	public function getMappingConfigured() {
		$out = array();
		$lines = GeneralUtility::trimExplode(LF, $this->mapping, TRUE);
		foreach ($lines as $line) {
			$split = GeneralUtility::trimExplode(':', $line, TRUE, 2);
			$out[$split[1]] = $split[0];
		}

		return $out;
	}

}