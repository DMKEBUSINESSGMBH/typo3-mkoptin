<?php

declare(strict_types=1);

/*
 * Copyright notice
 *
 * (c) 2021 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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

namespace DMK\Optin\Tests\Mail;

use DMK\Optin\Domain\Model\Optin;
use DMK\Optin\Mail\MailProcessor;
use DMK\Optin\Mail\View\FluidView;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MailProcessor test.
 *
 * @author Michael Wagner
 */
class MailProcessorTest extends NimutTestingFrameworkTestCase
{
    public function setUp()
    {
        GeneralUtility::purgeInstances();
    }

    public function tearDown()
    {
        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     */
    public function sendOptin()
    {
        $processor = new MailProcessor();

        $optin = new Optin();
        $optin->setEmail('optin@mail');

        $view = $this->prophesize(FluidView::class);
        GeneralUtility::addInstance(FluidView::class, $view->reveal());
        $view->setTemplate('Mail/Optin')->shouldBeCalled();
        $view->assignMultiple([
            'optinkey' => '#validationKey',
            'optin' => $optin,
        ])->shouldBeCalled();
        $view->renderBodyText()->willReturn('#text');
        $view->renderBodyHtml()->willReturn('<html>');
        $view->renderSubject()->willReturn('>subject');
        $view->getMailFrom()->willReturn('');
        $view->getMailTo()->willReturn([]);

        $mail = $this->prophesize(MailMessage::class);
        GeneralUtility::addInstance(MailMessage::class, $mail->reveal());
        $mail->setFrom(Argument::any())->willReturn($mail->reveal())->shouldBeCalled();
        $mail->text('#text')->willReturn($mail->reveal())->shouldBeCalled();
        $mail->html('<html>')->willReturn($mail->reveal())->shouldBeCalled();
        $mail->subject('>subject')->willReturn($mail->reveal())->shouldBeCalled();
        $mail->setTo('optin@mail')->willReturn($mail->reveal())->shouldBeCalled();
        $mail->send()->willReturn(1)->shouldBeCalled();

        $this->assertTrue(
            $processor->sendOptin($optin, '#validationKey')
        );
    }
}
