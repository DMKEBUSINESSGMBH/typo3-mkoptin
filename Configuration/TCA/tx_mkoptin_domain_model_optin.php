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

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tx_mkoptin_domain_model_optin',
        'label' => 'email',
        'rootLevel' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'email',
        'iconfile' => 'EXT:mkoptin/Resources/Public/Icons/tx_mkoptin_domain_model_optin.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, email, is_validated, validation_hash, validation_date',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, email, is_validated, validation_hash, validation_date'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tx_mkoptin_domain_model_optin.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,email,required',
                'readOnly' => true,
            ],
        ],
        'is_validated' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tx_mkoptin_domain_model_optin.is_validated',
            'config' => [
                'type' => 'check',
                'readOnly' => true,
            ],
        ],
        'validation_hash' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tx_mkoptin_domain_model_optin.validation_hash',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
            ],
        ],
        'validation_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tx_mkoptin_domain_model_optin.validation_date',
            'config' => [
                'dbType' => 'datetime',
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date',
                'default' => null,
                'readOnly' => true,
            ],
        ],
    ],
];
