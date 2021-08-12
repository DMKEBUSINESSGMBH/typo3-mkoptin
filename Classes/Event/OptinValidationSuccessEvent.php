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

namespace DMK\Optin\Event;

use DMK\Optin\Domain\Model\Optin;

/**
 * OptinValidationSuccessEvent.
 *
 * @author Michael Wagner
 */
final class OptinValidationSuccessEvent
{
    private Optin $optin;

    public function __construct(Optin $optin)
    {
        $this->optin = $optin;
    }

    public function getOptin(): Optin
    {
        return $this->optin;
    }
}
