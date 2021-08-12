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
use DMK\Optin\Domain\Mapper\OptinMapper;
use DMK\Optin\Domain\Model\Optin;

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

    /**
     * @return string|MapperInterface
     */
    protected function getMapperClass(): string
    {
        return OptinMapper::class;
    }

    public function findOneByEmail(string $email): ?Optin
    {
        $result = $this->createSearchQueryBuilder()
            ->where('email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->execute();

        $optin = $result->fetchAssociative();

        if (false === $optin || !is_array($optin)) {
            return null;
        }

        return $this->getMapperClass()::fromRecord($optin)->getEntity();
    }
}
