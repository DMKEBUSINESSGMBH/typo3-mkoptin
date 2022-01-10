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

namespace DMK\Optin\Tests\Domain\Repository;

use DMK\Optin\Domain\Mapper\MapperInterface;
use DMK\Optin\Domain\Model\Optin;
use DMK\Optin\Domain\Repository\OptinRepository;
use Doctrine\DBAL\Driver\Result;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * OptinRepository test.
 *
 * @author Michael Wagner
 */
class OptinRepositoryTest extends NimutTestingFrameworkTestCase
{
    /**
     * @var ObjectProphecy|Connection|null
     */
    private ?ObjectProphecy $connection = null;
    /**
     * @var ObjectProphecy|QueryBuilder|null
     */
    private ?ObjectProphecy $queryBuilder = null;
    /**
     * @var ObjectProphecy|MapperInterface|null
     */
    private ?ObjectProphecy $mapper = null;

    private ?OptinRepository $repository = null;

    public function setUp()
    {
        $this->connection = $this->prophesize(Connection::class);
        $this->queryBuilder = $this->prophesize(QueryBuilder::class);
        $this->connection->createQueryBuilder()->willReturn($this->queryBuilder->reveal());
        $this->mapper = $this->prophesize(MapperInterface::class);
        $this->repository = new OptinRepository(
            $this->connection->reveal(),
            $this->mapper->reveal()
        );
    }

    /**
     * @test
     */
    public function getTableName()
    {
        $this->assertSame(
            'tx_mkoptin_domain_model_optin',
            $this->callInaccessibleMethod(
                $this->repository,
                'getTableName'
            )
        );
    }

    /**
     * @test
     */
    public function findOneByEmail()
    {
        $result = $this->prophesize(Result::class);
        $result->fetchAssociative()->willReturn(['uid' => 7]);

        $entity = $this->prophesize(Optin::class);

        $this->queryBuilder->select('*')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->from('tx_mkoptin_domain_model_optin')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->where('email = :email')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->setParameter('email', 'hulk@marvel.localhost')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->setMaxResults(1)->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->execute()->willReturn($result->reveal());

        $this->mapper->fromRecord(['uid' => 7])->willReturn($entity->reveal());

        $this->assertSame(
            $entity->reveal(),
            $this->repository->findOneByEmail('hulk@marvel.localhost')
        );
    }
}
