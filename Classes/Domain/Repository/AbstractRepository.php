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

namespace DMK\Optin\Domain\Repository;

use DMK\Optin\Domain\Mapper\MapperInterface;
use DMK\Optin\Domain\Model\EntityInterface;
use Doctrine\DBAL\Driver\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * AbstractRepository.
 *
 * @author Michael Wagner
 */
abstract class AbstractRepository
{
    protected Connection $connection;
    protected MapperInterface $mapper;

    public function __construct(Connection $connection, MapperInterface $mapper)
    {
        $this->connection = $connection;
        $this->mapper = $mapper;
    }

    abstract protected function getTableName(): string;

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    protected function createSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName());
    }

    public function findByIdentifier(int $identifier): ?EntityInterface
    {
        if (0 === $identifier) {
            return null;
        }

        $result = $this->createSearchQueryBuilder()
            ->where('uid = :uid')
            ->setParameter('uid', $identifier)
            ->setMaxResults(1)
            ->execute();

        if (!$result instanceof Result) {
            return null;
        }

        $record = $result->fetchAssociative();

        if (false === $record || !is_array($record)) {
            return null;
        }

        return $this->mapper->fromRecord($record);
    }

    public function append(EntityInterface $entity): void
    {
        if (0 === $entity->getUid()) {
            $this->appendnew($entity);

            return;
        }

        $this->appendUpdate($entity);
    }

    private function appendUpdate(EntityInterface $entity): void
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder = $queryBuilder
            ->update($this->getTableName())
            ->where($queryBuilder->expr()->eq('uid', $entity->getUid()));
        $values = $this->mapper->toArray($entity);
        foreach ($values as $property => $value) {
            $queryBuilder->set($property, $value);
        }
        $queryBuilder->execute();
    }

    private function appendNew(EntityInterface $entity): void
    {
        $values = $this->mapper->toArray($entity);
        $query = $this->createQueryBuilder()->insert($this->getTableName())->values($values);
        if ($query->execute() && method_exists($entity, 'setUid')) {
            $entity->setUid(
                (int) $this->connection->lastInsertId($this->getTableName())
            );
        }
    }
}
