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

call_user_func(
    function () {
        // Register a node in ext_localconf.php
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['MkOptinStateElement'] = [
            'nodeName' => 'optInStateElement',
            'priority' => 40,
            'class' => \DMK\Optin\Backend\Form\Element\OptinStateElement::class,
        ];
    }
);
