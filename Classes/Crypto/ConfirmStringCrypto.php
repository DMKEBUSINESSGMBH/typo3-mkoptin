<?php

declare(strict_types=1);

/*
 * Copyright notice
 *
 * (c) 2021 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the PWRK Jobs in Town TYPO3 Project.
 *
 * It is proprietary, do not copy this script!
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
