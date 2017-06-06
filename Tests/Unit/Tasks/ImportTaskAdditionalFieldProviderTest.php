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
            ['dummy'], [], '', false);

        $fieldProvider = $this->getAccessibleMock('Cyberhouse\NewsImporticsxml\Tasks\\ImportTaskAdditionalFieldProvider',
            ['translate'], [], '', false);
        $this->assertEquals($result, $fieldProvider->_call('validate', $data, $mockedSchedulerController));
    }

    public function propertyValidationDataProvider()
    {
        return [
            'works' => [
                [
                    'email' => 'fo@bar.com',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => '123',
                    'format' => 'xml',
                ],
                true
            ],
            'wrongEmail' => [
                [
                    'email' => 'lorem ipsum',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => '123',
                    'format' => 'xml',
                ],
                false
            ],
            'optionalEmailIsOk' => [
                [
                    'email' => '',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => '123',
                    'format' => 'xml',
                ],
                true
            ],
            'wrongPid' => [
                [
                    'email' => '',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => 'text',
                    'format' => 'xml',
                ],
                false
            ],
            'noFormat' => [
                [
                    'email' => '',
                    'path' => 'fileadmin/xy.xml',
                    'pid' => 'text',
                    'format' => '',
                ],
                false
            ],
            'noPath' => [
                [
                    'email' => '',
                    'path' => '',
                    'pid' => 'text',
                    'format' => 'xml',
                ],
                false
            ]
        ];
    }
}
