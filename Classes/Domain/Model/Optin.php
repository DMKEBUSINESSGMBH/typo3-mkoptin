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

namespace DMK\Optin\Domain\Model;

use DateTimeInterface;

/**
 * Optin model.
 *
 * @author Michael Wagner
 */
class Optin extends AbstractEntity
{
    protected string $email = '';
    protected bool $isValidated = false;
    protected string $validationHash = '';
    protected ?DateTimeInterface $validationDate = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function getIsValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }

    public function getValidationHash(): string
    {
        return $this->validationHash;
    }

    public function setValidationHash(string $validationHash): void
    {
        $this->validationHash = $validationHash;
    }

    public function getValidationDate(): ?DateTimeInterface
    {
        return $this->validationDate;
    }

    public function setValidationDate(DateTimeInterface $validationDate): void
    {
        $this->validationDate = $validationDate;
    }
}
