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

class ImportTaskAdditionalFieldProviderTest extends UnitTestCase
{

    /**
     * @test
     * @dataProvider propertyValidationDataProvider
     */
    public function propertyValidation($data, $result)
    {
        $mockedSchedulerController = $this->getAccessibleMock('TYPO3\\CMS\\Scheduler\\Controller\\SchedulerModuleController',
            array('dummy'), array(), '', false);

        $fieldProvider = $this->getAccessibleMock('Cyberhouse\NewsImporticsxml\Tasks\\ImportTaskAdditionalFieldProvider',
            array('translate'), array(), '', false);
        $this->assertEquals($result, $fieldProvider->_call('validate', $data, $mockedSchedulerController));
    }

    public function propertyValidationDataProvider()
    {
        return array(
            'works' => array(
                array(
                    'email' => 'fo@bar.com',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => '123',
                    'format' => 'xml',
                ),
                true
            ),
            'wrongEmail' => array(
                array(
                    'email' => 'lorem ipsum',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => '123',
                    'format' => 'xml',
                ),
                false
            ),
            'optionalEmailIsOk' => array(
                array(
                    'email' => '',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => '123',
                    'format' => 'xml',
                ),
                true
            ),
            'wrongPid' => array(
                array(
                    'email' => '',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => 'text',
                    'format' => 'xml',
                ),
                false
            ),
            'noFormat' => array(
                array(
                    'email' => '',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => 'text',
                    'format' => '',
                ),
                false
            ),
            'noPath' => array(
                array(
                    'email' => '',
                    'path' => '',
                    'pid' => 'text',
                    'format' => 'xml',
                ),
                false
            )
        );
    }
}