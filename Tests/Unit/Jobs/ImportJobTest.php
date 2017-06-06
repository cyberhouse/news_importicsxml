<?php

namespace GeorgRinger\NewsImporticsxml\Tests\Unit\Jobs;

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

use GeorgRinger\NewsImporticsxml\Domain\Model\Dto\TaskConfiguration;
use GeorgRinger\NewsImporticsxml\Jobs\ImportJob;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Tests\UnitTestCase;

class ImportJobTest extends UnitTestCase
{
    protected $mockedJob;

    public function setUp()
    {
        $logger = $this->getAccessibleMock(Logger::class, ['dummy'], [], '', false);

        $this->mockedJob = $this->getAccessibleMock(ImportJob::class, ['import'],
            [], '', false);
        $this->mockedJob->_set('logger', $logger);
    }

    /**
     * @test
     */
    public function xmlMapperIsCalledWithXmlConfiguration()
    {
        $configuration = new TaskConfiguration();
        $configuration->setFormat('xml');
        $this->mockedJob->_set('configuration', $configuration);

        $xmlMapper = $this->getAccessibleMock('GeorgRinger\NewsImporticsxml\Mapper\XmlMapper', ['map']);
        $this->mockedJob->_set('xmlMapper', $xmlMapper);

        $xmlMapper->expects($this->once())->method('map');

        $this->mockedJob->_call('run');
    }

    /**
     * @test
     */
    public function icsMapperIsCalledWithXmlConfiguration()
    {
        $configuration = new TaskConfiguration();
        $configuration->setFormat('ics');
        $this->mockedJob->_set('configuration', $configuration);

        $icsMapper = $this->getAccessibleMock('GeorgRinger\NewsImporticsxml\Mapper\IcsMapper', ['map']);
        $this->mockedJob->_set('icsMapper', $icsMapper);

        $icsMapper->expects($this->once())->method('map');

        $this->mockedJob->_call('run');
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function nonSupportedMapperThrowsException()
    {
        $configuration = new TaskConfiguration();
        $configuration->setFormat('fo');
        $this->mockedJob->_set('configuration', $configuration);

        $this->mockedJob->_call('run');
    }
}
