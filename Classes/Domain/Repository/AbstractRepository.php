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

namespace DMK\Optin\Domain\Repository;

use DMK\Optin\Domain\Mapper\MapperInterface;
use DMK\Optin\Domain\Model\EntityInterface;
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

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    abstract protected function getTableName(): string;

    /**
     * @return string|MapperInterface
     */
    abstract protected function getMapperClass(): string;

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
        $result = $this->createSearchQueryBuilder()
            ->where('uid = :uid')
            ->setParameter('uid', $identifier)
            ->setMaxResults(1)
            ->execute();

        $record = $result->fetchAssociative();

        if (false === $record || !is_array($record)) {
            return null;
        }

        return $this->getMapperClass()::fromRecord($record)->getEntity();
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
        $values = $this->getMapperClass()::fromEntity($entity)->toArray();
        foreach ($values as $property => $value) {
            $queryBuilder->set($property, $value);
        }
        $queryBuilder->execute();
    }

    private function appendNew(EntityInterface $entity): void
    {
        $values = $this->getMapperClass()::fromEntity($entity)->toArray();
        $query = $this->createQueryBuilder()->insert($this->getTableName())->values($values);
        if ($query->execute()) {
            $entity->setUid(
                (int) $this->connection->lastInsertId($this->getTableName())
            );
        }
    }
}
