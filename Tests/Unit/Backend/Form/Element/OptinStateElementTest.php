<?php

declare(strict_types=1);

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mkoptin" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * www.gnu.org/copyleft/gpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace DMK\Optin\Tests\Backend\Form\Element;

use DateTime;
use DateTimeZone;
use DMK\Optin\Backend\Form\Element\OptinStateElement;
use DMK\Optin\Domain\Manager\OptinManager;
use DMK\Optin\Domain\Model\Optin;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * OptinStateElement test.
 *
 * @author Michael Wagner
 */
class OptinStateElementTest extends NimutTestingFrameworkTestCase
{
    private ?OptinStateElement $optinStateElement = null;
    /**
     * @var ObjectProphecy|OptinManager|null
     */
    private ?ObjectProphecy $optinManager = null;
    private ?Optin $optin = null;

    public function setUp()
    {
        $this->optinManager = $this->prophesize(OptinManager::class);
        $this->optinStateElement = $this->getMockBuilder(OptinStateElement::class)
            ->setMethods(['initializeResultArray', 'getOptinManager', 'translate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->optinStateElement->method('initializeResultArray')->willReturn([]);
        $this->optinStateElement->method('getOptinManager')->willReturn($this->optinManager->reveal());
        $this->optinStateElement->method('translate')->willReturnArgument(0);

        $this->optin = new Optin();
        $this->optin->setUid(5);
        $this->optin->setIsValidated(false);
        $this->optin->setValidationDate(new DateTime('1922-12-28 12:00:30', new DateTimeZone('UTC')));
        $this->optin->setEmail('hulk@marvel.localhost');
    }

    /**
     * @test
     */
    public function renderForOptinTable()
    {
        $this->inject(
            $this->optinStateElement,
            'data',
            [
                'tableName' => 'tx_mkoptin_domain_model_optin',
                'databaseRow' => ['email' => $this->optin->getEmail()],
            ]
        );
        $result = $this->optinStateElement->render();
        $html = $result['html'];

        $this->assertEquals(
            '<div style="color:#999;">tx_mkoptin_domain_model_optin.nomail</div>',
            $html
        );
    }

    /**
     * @test
     */
    public function renderForUnknownEmail()
    {
        $this->inject(
            $this->optinStateElement,
            'data',
            [
                'tableName' => 'tx_myoptinext_entry',
                'databaseRow' => ['email' => ''],
            ]
        );
        $this->optinManager->findOptinByEmail($this->optin->getEmail())->willReturn(null);
        $result = $this->optinStateElement->render();
        $html = $result['html'];

        $this->assertEquals(
            '<div style="color:#999;">tx_mkoptin_domain_model_optin.nomail</div>',
            $html
        );
    }

    /**
     * @test
     */
    public function renderForNotFoundOptin()
    {
        $this->inject(
            $this->optinStateElement,
            'data',
            [
                'tableName' => 'tx_myoptinext_entry',
                'databaseRow' => ['email' => $this->optin->getEmail()],
            ]
        );
        $this->optinManager->findOptinByEmail($this->optin->getEmail())->willReturn(null);
        $result = $this->optinStateElement->render();
        $html = $result['html'];

        $this->assertEquals(
            '<div style="color:#C99;">tx_mkoptin_domain_model_optin.nooptin</div>',
            $html
        );
    }

    /**
     * @test
     */
    public function renderForUnvalidatedEmail()
    {
        $this->inject(
            $this->optinStateElement,
            'data',
            [
                'tableName' => 'tx_myoptinext_entry',
                'databaseRow' => ['email' => $this->optin->getEmail()],
            ]
        );
        $this->optin->setIsValidated(false);
        $this->optinManager->findOptinByEmail($this->optin->getEmail())->willReturn($this->optin);
        $result = $this->optinStateElement->render();
        $html = $result['html'];

        // <div style="color:#C00;">
        //  <!-- optin [5] -->
        //  tx_mkoptin_domain_model_optin.email: hulk@marvel.localhost
        //  <br />
        //  tx_mkoptin_domain_model_optin.is_validated: &#x2716;
        //  <br />
        // </div>

        $this->assertContains(
            '<div style="color:#C00;">',
            $html
        );
        $this->assertContains(
            '<!-- optin ['.$this->optin->getUid().'] -->',
            $html
        );
        $this->assertContains(
            'tx_mkoptin_domain_model_optin.email: '.$this->optin->getEmail(),
            $html
        );
        $this->assertContains(
            'tx_mkoptin_domain_model_optin.is_validated: &#x2716;',
            $html
        );
    }

    /**
     * @test
     */
    public function renderForValidatedEmail()
    {
        $this->inject(
            $this->optinStateElement,
            'data',
            [
                'tableName' => 'tx_myoptinext_entry',
                'databaseRow' => ['email' => $this->optin->getEmail()],
            ]
        );
        $this->optin->setIsValidated(true);
        $this->optinManager->findOptinByEmail($this->optin->getEmail())->willReturn($this->optin);
        $result = $this->optinStateElement->render();
        $html = $result['html'];

        // <div style="color:#060;">
        //  <!-- optin [5] -->
        //  tx_mkoptin_domain_model_optin.email: hulk@marvel.localhost
        //  <br />
        //  tx_mkoptin_domain_model_optin.is_validated: &#x2714;
        //  <br />
        //  tx_mkoptin_domain_model_optin.validation_date: Thu, 28 Dec 1922 12:00:30 +0000
        // </div>

        $this->assertContains(
            '<div style="color:#060;">',
            $html
        );
        $this->assertContains(
            '<!-- optin ['.$this->optin->getUid().'] -->',
            $html
        );
        $this->assertContains(
            'tx_mkoptin_domain_model_optin.email: '.$this->optin->getEmail(),
            $html
        );
        $this->assertContains(
            'tx_mkoptin_domain_model_optin.is_validated: &#x2714;',
            $html
        );
        $this->assertContains(
            'tx_mkoptin_domain_model_optin.validation_date: Thu, 28 Dec 1922 12:00:30 +0000',
            $html
        );
    }
}
