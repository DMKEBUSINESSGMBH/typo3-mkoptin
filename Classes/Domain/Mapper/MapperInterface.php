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

use DMK\Optin\Domain\Model\EntityInterface;
use Doctrine\DBAL\ForwardCompatibility\Result as QueryResult;

/**
 * MapperInterface.
 *
 * @author Michael Wagner
 */
interface MapperInterface
{
    public function getEntity(): EntityInterface;

    public static function fromEntity(EntityInterface $entity): MapperInterface;

    /**
     * @param array<string, string> $result
     *
     * @return array<int, EntityInterface>
     */
    public static function fromResults(QueryResult $result): array;

    /**
     * @param array<string, string> $result
     */
    public static function fromRecord(array $record): MapperInterface;

    /**
     * @param array<string, string> $result
     */
    public function toArray(): array;
}
