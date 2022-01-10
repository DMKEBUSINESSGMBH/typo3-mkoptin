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

namespace DMK\Optin\Domain\Manager;

use DateTimeImmutable;
use DMK\Optin\Crypto\OptinKeyCrypto;
use DMK\Optin\Domain\Model\Optin;
use DMK\Optin\Domain\Repository\OptinRepository;
use DMK\Optin\Mail\MailProcessor;
use LogicException;

/**
 * OptinManager.
 *
 * @author Michael Wagner
 */
class OptinManager
{
    private OptinRepository $optinRepository;
    private OptinKeyCrypto $optinKeyCrypto;
    private MailProcessor $mailProcessor;

    public function __construct(
        OptinRepository $optinRepository,
        OptinKeyCrypto $optinKeyCrypto,
        MailProcessor $mailProcessor
    ) {
        $this->optinRepository = $optinRepository;
        $this->optinKeyCrypto = $optinKeyCrypto;
        $this->mailProcessor = $mailProcessor;
    }

    /**
     * Find a optin entity by mail address.
     */
    public function findOptinByEmail(string $email): ?Optin
    {
        if (empty($email)) {
            throw new LogicException('Email is required to find optin.');
        }

        return $this->optinRepository->findOneByEmail($email);
    }

    public function isEmailValidated(string $email): bool
    {
        $optin = $this->findOptinByEmail($email);

        return null !== $optin && $optin->isValidated();
    }

    /**
     * Creates a new optin entry for a contact.
     * If there is already an database entry for mail address, the existing will be returned.
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createOptinForEmail(string $email): Optin
    {
        $optin = $this->findOptinByEmail($email);
        if ($optin) {
            return $optin;
        }

        $optin = new Optin();
        $optin->setPid(0);
        $optin->setEmail($email);
        $optin->setIsValidated(false);
        $optin->setValidationHash(
            $this->optinKeyCrypto->createConfirmString()
        );

        $this->optinRepository->append($optin);

        return $optin;
    }

    /**
     * try to find an optin by a key.
     */
    public function getOptinByKey(string $optinKey): Optin
    {
        [$identifier] = $this->optinKeyCrypto->decode($optinKey);

        $optin = $this->optinRepository->findByIdentifier((int) $identifier);

        if ((
            $optin instanceof Optin &&
            $this->optinKeyCrypto->validateOptin($optin, $optinKey)
        )) {
            return $optin;
        }

        throw new LogicException('Invalid key, can not resolve optin');
    }

    /**
     * Sends an validation mail to user.
     *
     * Should be called in extbase controller context!
     * Otherwise there will be configuration anomalies.     *
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function sendValidationMailForOptin(Optin $optin): void
    {
        if ('' === $optin->getValidationHash()) {
            $optin->setValidationHash(
                $this->optinKeyCrypto->createConfirmString()
            );
            $this->optinRepository->append($optin);
        }
        $this->mailProcessor->sendOptin(
            $optin,
            $this->optinKeyCrypto->createByOptin($optin)
        );
    }

    /**
     * Validates an opt in by key.
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function validateByKey(string $optinKey): void
    {
        $optin = $this->getOptinByKey($optinKey);
        $optin->setIsValidated(true);
        $optin->setValidationDate(new DateTimeImmutable());
        $optin->setValidationHash('');
        $this->optinRepository->append($optin);
    }
}
