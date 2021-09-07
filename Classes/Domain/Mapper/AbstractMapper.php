<?php

declare(strict_types=1);

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

namespace DMK\Optin\Domain\Mapper;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use DMK\Optin\Domain\Model\AbstractEntity;
use DMK\Optin\Domain\Model\EntityInterface;

/**
 * AbstractMapper.
 *
 * @author Michael Wagner
 */
abstract class AbstractMapper
{
    /**
     * @param array<string, string> $record
     */
    protected function mapDefaultsFromRecord(array $record, EntityInterface $entity): EntityInterface
    {
        if ($entity instanceof AbstractEntity) {
            $entity->setUid((int) $record['uid']);
            $entity->setPid((int) $record['pid'] ?: 0);
            $entity->setDeleted($record['deleted'] > 0);
            $entity->setHidden($record['hidden'] > 0);
        }

        return $entity;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapDefaultsToArray(EntityInterface $entity): array
    {
        $record = [
            'uid' => $entity->getUid(),
        ];

        if ($entity instanceof AbstractEntity) {
            $record['pid'] = $entity->getPid();
            $record['deleted'] = (int) $entity->isDeleted();
            $record['hidden'] = (int) $entity->isHidden();
        }

        return $record;
    }

    protected function mapDateToString(?DateTimeInterface $date = null): ?string
    {
        if (!$date instanceof DateTimeInterface) {
            return null;
        }

        if (method_exists($date, 'setTimezone')) {
            $date = clone $date;
            $date->setTimezone(new DateTimeZone('UTC'));
        }

        return $date->format('Y-m-d H:i:s');
    }

    protected function mapDateFromString(?string $date): ?DateTimeInterface
    {
        if (empty($date) || '0000-00-00 00:00:00' === $date) {
            return null;
        }

        $utcTimeZone = new DateTimeZone('UTC');
        $utcDateTime = new DateTime($date, $utcTimeZone);
        $currentTimeZone = new DateTimeZone(date_default_timezone_get());

        return $utcDateTime->setTimezone($currentTimeZone);
    }
}
