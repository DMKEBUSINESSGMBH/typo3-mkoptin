<?php

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

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'MkOptin',
    'Optin',
    [
        \DMK\Optin\Controller\OptinController::class => 'validation',
    ],
    [
        \DMK\Optin\Controller\OptinController::class => 'validation',
    ]
);
