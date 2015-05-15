<?php
namespace Cyberhouse\NewsImporticsxml\Tests\Unit\Jobs;

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
use TYPO3\CMS\Core\Tests\UnitTestCase;

class ImportJobTest extends UnitTestCase {

	protected $mockedJob;

	public function setUp() {
		$logger = $this->getAccessibleMock('TYPO3\\CMS\\Core\\Log\\Logger', array('dummy'), array(), '', FALSE);

		$this->mockedJob = $this->getAccessibleMock('Cyberhouse\\NewsImporticsxml\\Jobs\\ImportJob', array('import'), array(), '', FALSE);
		$this->mockedJob->_set('logger', $logger);
	}

	/**
	 * @test
	 */
	public function xmlMapperIsCalledWithXmlConfiguration() {
		$configuration = new TaskConfiguration();
		$configuration->setFormat('xml');
		$this->mockedJob->_set('configuration', $configuration);

		$xmlMapper = $this->getAccessibleMock('Cyberhouse\NewsImporticsxml\Mapper\XmlMapper', array('map'));
		$this->mockedJob->_set('xmlMapper', $xmlMapper);

		$xmlMapper->expects($this->once())->method('map');

		$this->mockedJob->_call('run');
	}

	/**
	 * @test
	 */
	public function icsMapperIsCalledWithXmlConfiguration() {
		$configuration = new TaskConfiguration();
		$configuration->setFormat('ics');
		$this->mockedJob->_set('configuration', $configuration);

		$icsMapper = $this->getAccessibleMock('Cyberhouse\NewsImporticsxml\Mapper\IcsMapper', array('map'));
		$this->mockedJob->_set('icsMapper', $icsMapper);

		$icsMapper->expects($this->once())->method('map');

		$this->mockedJob->_call('run');
	}

	/**
	 * @test
	 * @expectedException \UnexpectedValueException
	 */
	public function nonSupportedMapperThrowsException() {
		$configuration = new TaskConfiguration();
		$configuration->setFormat('fo');
		$this->mockedJob->_set('configuration', $configuration);

		$this->mockedJob->_call('run');
	}
}
