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

namespace DMK\Optin\Domain\Repository;

use DMK\Optin\Domain\Model\Optin;
use Doctrine\DBAL\Driver\Result;

/**
 * OptinRepository.
 *
 * @author Michael Wagner
 */
class OptinRepository extends AbstractRepository
{
    protected function getTableName(): string
    {
        return 'tx_mkoptin_domain_model_optin';
    }

    public function findOneByEmail(string $email): ?Optin
    {
        $result = $this->createSearchQueryBuilder()
            ->where('email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->execute();

        if (!$result instanceof Result) {
            return null;
        }

        $optin = $result->fetchAssociative();

        if (false === $optin || !is_array($optin)) {
            return null;
        }

        $optin = $this->mapper->fromRecord($optin);

        if (!$optin instanceof Optin) {
            return null;
        }

        return $optin;
    }
}
