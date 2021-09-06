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

namespace DMK\Optin\Crypto;

/**
 * ConfirmStringCrypto.
 *
 * @author Michael Wagner
 */
class ConfirmStringCrypto
{
    /**
     * Creates a new 32 sign confirm string.
     */
    public function generate(): string
    {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(16));
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $binary = openssl_random_pseudo_bytes(16);
            if ($binary) {
                return bin2hex($binary);
            }
        }

        return md5(uniqid('cs', true));
    }
}
