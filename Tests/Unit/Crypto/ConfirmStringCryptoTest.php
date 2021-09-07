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

namespace DMK\Optin\Tests\Crypto;

use DMK\Optin\Crypto\ConfirmStringCrypto;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;

/**
 * ConfirmStringCrypto test.
 *
 * @author Michael Wagner
 */
class ConfirmStringCryptoTest extends NimutTestingFrameworkTestCase
{
    /**
     * @test
     */
    public function generate()
    {
        $util = new ConfirmStringCrypto();
        $confirmstring = $util->generate();
        $this->assertEquals(32, strlen($confirmstring));
    }
}
