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

namespace DMK\Optin\Tests\Domain\Model;

use DateTimeImmutable;
use DMK\Optin\Domain\Model\Optin;

/**
 * Optin model test.
 *
 * @author Michael Wagner
 */
class OptinTest extends AbstractModelTestCase
{
    protected function getModelClass(): string
    {
        return Optin::class;
    }

    protected function getModelProperties(): array
    {
        return [
            'uid' => 5,
            'pid' => 7,
            'deleted' => true,
            'hidden' => true,
            'email' => 'hulk@marvel.localhost',
            'validationDate' => new DateTimeImmutable(),
            'validationHash' => '#ValidationHash',
            'isValidated' => true,
        ];
    }
}
