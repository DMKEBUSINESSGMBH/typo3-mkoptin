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

namespace DMK\Optin\Domain\Mapper;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DMK\Optin\Domain\Model\EntityInterface;

/**
 * AbstractMapper.
 *
 * @author Michael Wagner
 */
abstract class AbstractMapper
{
    /**
     * @param array<string, string> $result
     */
    public static function mapDefaultsFromRecord(array $record, EntityInterface $entity): EntityInterface
    {
        $entity->setUid((int) $record['uid']);
        $entity->setPid((int) $record['pid'] ?: 0);
        $entity->setDeleted($record['deleted'] > 0);
        $entity->setHidden($record['hidden'] > 0);

        return $entity;
    }

    protected static function mapDefaultsToArray(EntityInterface $entity): array
    {
        return [
            'uid' => $entity->getUid(),
            'pid' => $entity->getPid(),
            'deleted' => (int) $entity->isDeleted(),
            'hidden' => (int) $entity->isHidden(),
        ];
    }

    protected static function mapDateToString(?DateTimeInterface $date = null): ?string
    {
        if (!$date instanceof DateTimeInterface) {
            return null;
        }

        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));

        return $date->format('Y-m-d H:i:s');
    }

    protected static function mapDateFromString(?string $date): ?DateTimeInterface
    {
        if (empty($date) || '0000-00-00 00:00:00' === $date) {
            return null;
        }

        $utcTimeZone = new DateTimeZone('UTC');
        $utcDateTime = new DateTimeImmutable($date, $utcTimeZone);
        $currentTimeZone = new DateTimeZone(date_default_timezone_get());

        return $utcDateTime->setTimezone($currentTimeZone);
    }
}
