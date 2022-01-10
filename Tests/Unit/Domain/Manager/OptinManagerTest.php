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

namespace DMK\Optin\Tests\Domain\Manager;

use DateTime;
use DateTimeImmutable;
use DMK\Optin\Crypto\OptinKeyCrypto;
use DMK\Optin\Domain\Manager\OptinManager;
use DMK\Optin\Domain\Model\Optin;
use DMK\Optin\Domain\Repository\OptinRepository;
use DMK\Optin\Mail\MailProcessor;
use LogicException;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * OptinManager test.
 *
 * @author Michael Wagner
 */
class OptinManagerTest extends NimutTestingFrameworkTestCase
{
    private ?Optin $optin = null;
    /**
     * @var OptinManager|ObjectProphecy|null
     */
    private ?OptinManager $optinManager = null;
    /**
     * @var OptinRepository|ObjectProphecy|null
     */
    private ?ObjectProphecy $optinRepository = null;
    /**
     * @var OptinKeyCrypto|ObjectProphecy|null
     */
    private ?ObjectProphecy $optinKeyCrypto = null;
    /**
     * @var MailProcessor|ObjectProphecy|null
     */
    private ?ObjectProphecy $mailProcessor = null;

    public function setUp()
    {
        $this->optinRepository = $this->prophesize(OptinRepository::class);
        $this->optinKeyCrypto = $this->prophesize(OptinKeyCrypto::class);
        $this->mailProcessor = $this->prophesize(MailProcessor::class);
        $this->optinManager = new OptinManager(
            $this->optinRepository->reveal(),
            $this->optinKeyCrypto->reveal(),
            $this->mailProcessor->reveal()
        );
        $this->optin = new Optin();
        $this->optin->setUid(5);
        $this->optin->setValidationHash('#ValidationHash');
        $this->optin->setEmail('hulk@marvel.localhost');
    }

    /**
     * @test
     */
    public function findOptinByEmailThrowsException()
    {
        $this->expectException(LogicException::class);

        $this->optinManager->findOptinByEmail('');
    }

    /**
     * @test
     */
    public function findOptinByEmailCallsfindOneByEmail()
    {
        $this->optinRepository->findOneByEmail('mail')->willReturn($this->optin);

        $this->assertSame(
            $this->optin,
            $this->optinManager->findOptinByEmail('mail')
        );
    }

    /**
     * @test
     */
    public function isEmailValidatedThrowsException()
    {
        $this->expectException(LogicException::class);

        $this->optinManager->isEmailValidated('');
    }

    /**
     * @test
     */
    public function isEmailValidatedReturnsFalse()
    {
        $this->optin->setIsValidated(false);
        $this->optinRepository->findOneByEmail('mail')->willReturn($this->optin);

        $this->assertFalse($this->optinManager->isEmailValidated('mail'));
    }

    /**
     * @test
     */
    public function isEmailValidatedReturnsTrue()
    {
        $this->optin->setIsValidated(true);
        $this->optinRepository->findOneByEmail('mail')->willReturn($this->optin);

        $this->assertTrue($this->optinManager->isEmailValidated('mail'));
    }

    /**
     * @test
     */
    public function createOptinForEmailReturnsExistingOptin()
    {
        $this->optinRepository->findOneByEmail('mail')->willReturn($this->optin);
        $this->optinRepository->append()->shouldNotBeCalled();

        $this->assertSame(
            $this->optin,
            $this->optinManager->createOptinForEmail('mail')
        );
    }

    /**
     * @test
     */
    public function createOptinForEmailReturnsNewOptin()
    {
        $this->optinRepository->findOneByEmail('mail')->willReturn(null)->shouldBeCalledOnce();
        $this->optinRepository->append(Argument::type(Optin::class))->shouldBeCalledOnce();
        $this->optinKeyCrypto->createConfirmString()->willReturn('cs');

        $optin = $this->optinManager->createOptinForEmail('mail');

        $this->assertSame(0, $optin->getPid());
        $this->assertSame('mail', $optin->getEmail());
        $this->assertSame(false, $optin->getIsValidated());
        $this->assertSame('cs', $optin->getValidationHash());
    }

    /**
     * @test
     */
    public function getOptinByKeyThrowsExceptionForInvalidKey()
    {
        $this->expectException(LogicException::class);
        $this->optinKeyCrypto->decode('oik')->willReturn([7]);
        $this->optinRepository->findByIdentifier(7)->willReturn(null);
        $this->optinKeyCrypto->validateOptin()->shouldNotBeCalled();

        $this->optinManager->getOptinByKey('oik');
    }

    /**
     * @test
     */
    public function getOptinByKeyForValidKey()
    {
        $this->optinKeyCrypto->decode('oik')->willReturn([7]);
        $this->optinRepository->findByIdentifier(7)->willReturn($this->optin);
        $this->optinKeyCrypto->validateOptin($this->optin, 'oik')->willReturn(true);

        $this->assertSame(
            $this->optin,
            $this->optinManager->getOptinByKey('oik')
        );
    }

    /**
     * @test
     */
    public function sendValidationMailForOptin()
    {
        $this->optin->setValidationHash('');

        $this->optinKeyCrypto->createConfirmString()->willReturn('#Hash');
        $this->optinRepository->append($this->optin)->shouldBeCalled();
        $this->optinKeyCrypto->createByOptin($this->optin)->willReturn('#vkey');
        $this->mailProcessor->sendOptin($this->optin, '#vkey')->shouldBeCalled();

        $this->optinManager->sendValidationMailForOptin($this->optin);
    }

    /**
     * @test
     */
    public function validateByKey()
    {
        // getOptinByKey
        $this->optinKeyCrypto->decode('oik')->willReturn([7]);
        $this->optinRepository->findByIdentifier(7)->willReturn($this->optin);
        $this->optinKeyCrypto->validateOptin($this->optin, 'oik')->willReturn(true);

        // reset optin to invalidated
        $this->optin->setIsValidated(false);
        $this->optin->setValidationDate(new DateTime());
        $this->optin->setValidationHash('#ValidationHash');

        // validateByKey
        $this->optinRepository->append($this->optin)->shouldBeCalled();

        // call unit
        $this->optinManager->validateByKey('oik');

        // test optin
        $this->assertTrue($this->optin->isValidated());
        $this->assertInstanceOf(DateTimeImmutable::class, $this->optin->getValidationDate());
        $this->assertSame('', $this->optin->getValidationHash());
    }
}
