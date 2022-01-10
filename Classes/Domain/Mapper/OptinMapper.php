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

namespace DMK\Optin\Domain\Mapper;

use DMK\Optin\Domain\Model\EntityInterface;
use DMK\Optin\Domain\Model\Optin;
use Doctrine\DBAL\Driver\Result as QueryResult;
use InvalidArgumentException;

/**
 * OptinMapper.
 *
 * @author Michael Wagner
 */
class OptinMapper extends AbstractMapper implements MapperInterface
{
    /**
     * @return array<int, Optin>
     */
    public function fromResults(QueryResult $result): array
    {
        $items = [];

        while (($record = $result->fetchAssociative()) !== false) {
            $items[] = $this->fromRecord($record);
        }

        return $items;
    }

    /**
     * @param array<string, string> $record
     */
    public function fromRecord(array $record): Optin
    {
        $optin = new Optin();
        $this->mapDefaultsFromRecord($record, $optin);

        $optin->setEmail($record['email']);
        $validationDate = $this->mapDateFromString($record['validation_date']);
        if (null !== $validationDate) {
            $optin->setValidationDate($validationDate);
        }
        $optin->setIsValidated($record['is_validated'] > 0);
        $optin->setValidationHash($record['validation_hash']);

        return $optin;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(EntityInterface $entity): array
    {
        if (!$entity instanceof Optin) {
            throw new InvalidArgumentException();
        }

        return array_merge(
            $this->mapDefaultsToArray($entity),
            [
                'email' => $entity->getEmail(),
                'is_validated' => $entity->isValidated() ? 1 : 0,
                'validation_hash' => $entity->getValidationHash(),
                'validation_date' => $this->mapDateToString($entity->getValidationDate()),
            ]
        );
    }
}
