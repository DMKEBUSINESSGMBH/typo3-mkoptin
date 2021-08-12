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
    protected function findOptinByEmail(string $email): ?Optin
    {
        if (empty($email)) {
            throw new LogicException('Contact has no mail to find optin.');
        }

        return $this->optinRepository->findOneByEmail($email);
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

        if ($this->optinKeyCrypto->validateOptin($optin, $optinKey)) {
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
