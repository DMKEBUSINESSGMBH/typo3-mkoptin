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

use DMK\Optin\Domain\Model\Optin;

/**
 * OptinKeyCrypto.
 *
 * @author Michael Wagner
 */
class OptinKeyCrypto
{
    public function createConfirmString(): string
    {
        $crypto = new ConfirmStringCrypto();

        return $crypto->generate();
    }

    public function createByOptin(Optin $optin): string
    {
        $optinKey = implode(
            ':',
            [
                $optin->getUid(),
                $optin->getValidationHash(),
                md5($optin->getEmail()),
            ]
        );

        return $this->urlEncode($optinKey);
    }

    /**
     * @return array<int, string>
     */
    public function decode(string $optinKey): array
    {
        // the key loks like base64 and urlencoded
        if (2 !== substr_count($optinKey, ':')) {
            $optinKey = $this->urlDencode($optinKey);
        }

        [$identifier, $validationHash, $mailHash] = explode(':', $optinKey);

        return [$identifier, $validationHash, $mailHash];
    }

    public function validateOptin(Optin $optin, string $optinKey): bool
    {
        [
            $identifier,
            $validationHash,
            $mailHash,
        ] = $this->decode($optinKey);

        if ((
            null !== $optin &&
            $optin->getUid() == $identifier &&
            !empty($validationHash) &&
            $optin->getValidationHash() === $validationHash &&
            md5($optin->getEmail()) === $mailHash
        )) {
            return true;
        }

        return false;
    }

    protected function urlEncode(string $value): string
    {
        return urlencode(base64_encode($value));
    }

    protected function urlDencode(string $value): string
    {
        return base64_decode(urldecode($value));
    }
}
