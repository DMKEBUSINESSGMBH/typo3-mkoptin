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

namespace DMK\Optin\Tests\Crypto;

use DMK\Optin\Crypto\OptinKeyCrypto;
use DMK\Optin\Domain\Model\Optin;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;

/**
 * OptinKeyCrypto test.
 *
 * @author Michael Wagner
 */
class OptinKeyCryptoTest extends NimutTestingFrameworkTestCase
{
    private ?Optin $optin = null;

    public function setUp()
    {
        $this->optin = new Optin();
        $this->optin->setUid(5);
        $this->optin->setValidationHash('foo');
        $this->optin->setEmail('bar');
    }

    /**
     * @test
     */
    public function createConfirmString()
    {
        $util = new OptinKeyCrypto();
        $confirmstring = $util->createConfirmString();
        $this->assertEquals(32, strlen($confirmstring));
    }

    /**
     * @test
     */
    public function createByOptin()
    {
        $util = new OptinKeyCrypto();
        $encodedKey = $util->createByOptin($this->optin);
        $decodedKey = base64_decode(urldecode($encodedKey));
        $this->assertEquals(
            $this->optin->getUid().':'.$this->optin->getValidationHash().':'.md5($this->optin->getEmail()),
            $decodedKey
        );

        return $encodedKey;
    }

    /**
     * @test
     * @depends createByOptin
     */
    public function decode(string $encodedKey)
    {
        $util = new OptinKeyCrypto();
        [$identifier, $validationHash, $mailHash] = $util->decode($encodedKey);

        $this->assertEquals($this->optin->getUid(), $identifier);
        $this->assertEquals($this->optin->getValidationHash(), $validationHash);
        $this->assertEquals(md5($this->optin->getEmail()), $mailHash);
    }

    /**
     * @test
     * @depends createByOptin
     */
    public function validateOptinReturnsTrue(string $encodedKey)
    {
        $util = new OptinKeyCrypto();
        $this->assertTrue(
            $util->validateOptin($this->optin, $encodedKey)
        );
    }

    /**
     * @test
     * @depends createByOptin
     */
    public function validateOptinReturnsFalse(string $encodedKey)
    {
        $util = new OptinKeyCrypto();
        $this->optin->setUid(7);
        $this->assertFalse(
            $util->validateOptin($this->optin, $encodedKey)
        );
    }
}
