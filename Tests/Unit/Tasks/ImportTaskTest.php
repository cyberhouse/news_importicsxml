<?php
namespace Cyberhouse\NewsImporticsxml\Tests\Unit\Tasks;

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

class ImportTaskTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function configurationIsCreatedByProperties() {
		$expectedConfiguration = new TaskConfiguration();
		$props = array(
			'email' => 'fo@bar.com',
			'pid' => '123',
			'path' => 'fileadmin/fo.xml',
			'mapping' => 'map:mapper',
			'format' => 'xml'
		);
		$task = $this->getAccessibleMock('Cyberhouse\NewsImporticsxml\Tasks\ImportTask', array('dummy'), array(), '', FALSE);
		foreach ($props as $key => $value) {
			$setter = 'set' . ucfirst($key);
			$expectedConfiguration->$setter($value);
			$task->_set($key, $value);
		}

		$this->assertEquals($expectedConfiguration, $task->_call('createConfiguration'));
	}
}