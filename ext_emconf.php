<?php

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

$EM_CONF['mkoptin'] = [
    'title' => 'MK Optin Extension',
    'description' => 'Opt-In Extension.',
    'category' => 'be',
    'author' => 'Michael Wagner',
    'author_company' => 'DMK E-BUSINESS',
    'author_email' => 'dev@dmk-ebusiness.de',
    'shy' => '',
    'dependencies' => 'typo3',
    'conflicts' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '10.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'suggests' => [],
];
