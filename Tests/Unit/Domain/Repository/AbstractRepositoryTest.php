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

namespace DMK\Optin\Tests\Domain\Repository;

use DMK\Optin\Domain\Mapper\MapperInterface;
use DMK\Optin\Domain\Model\EntityInterface;
use DMK\Optin\Domain\Repository\AbstractRepository;
use Doctrine\DBAL\Driver\Result;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * AbstractRepository test.
 *
 * @author Michael Wagner
 */
class AbstractRepositoryTest extends NimutTestingFrameworkTestCase
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
     * @var ObjectProphecy|ExpressionBuilder|null
     */
    private ?ObjectProphecy $expressionBuilder = null;
    /**
     * @var ObjectProphecy|MapperInterface|null
     */
    private ?ObjectProphecy $mapper = null;
    /**
     * @var MockObject|AbstractRepository|null
     */
    private ?MockObject $repository = null;

    public function setUp()
    {
        $this->connection = $this->prophesize(Connection::class);
        $this->queryBuilder = $this->prophesize(QueryBuilder::class);
        $this->expressionBuilder = $this->prophesize(ExpressionBuilder::class);
        $this->queryBuilder->expr()->willReturn($this->expressionBuilder->reveal());
        $this->connection->createQueryBuilder()->willReturn($this->queryBuilder->reveal());
        $this->mapper = $this->prophesize(MapperInterface::class);
        $this->repository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [$this->connection->reveal(), $this->mapper->reveal()]
        );
        $this->repository->method('getTableName')->willReturn('tx_table');
    }

    /**
     * @test
     */
    public function createQueryBuilder()
    {
        $this->assertSame(
            $this->queryBuilder->reveal(),
            $this->callInaccessibleMethod(
                $this->repository,
                'createQueryBuilder'
            )
        );
    }

    /**
     * @test
     */
    public function createSearchQueryBuilder()
    {
        $this->queryBuilder->select('*')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->from('tx_table')->willReturn($this->queryBuilder->reveal());

        $this->assertSame(
            $this->queryBuilder->reveal(),
            $this->callInaccessibleMethod(
                $this->repository,
                'createSearchQueryBuilder'
            )
        );
    }

    /**
     * @test
     */
    public function findByIdentifierReturnsNullForZeroIdentifier()
    {
        $this->assertNull($this->repository->findByIdentifier(0));
    }

    /**
     * @test
     */
    public function findByIdentifier()
    {
        $result = $this->prophesize(Result::class);
        $result->fetchAssociative()->willReturn(['uid' => 7]);

        $entity = $this->prophesize(EntityInterface::class);

        $this->queryBuilder->select('*')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->from('tx_table')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->where('uid = :uid')->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->setParameter('uid', 5)->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->setMaxResults(1)->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->execute()->willReturn($result->reveal());

        $this->mapper->fromRecord(['uid' => 7])->willReturn($entity->reveal());

        $this->assertSame(
            $entity->reveal(),
            $this->repository->findByIdentifier(5)
        );
    }

    /**
     * @test
     */
    public function appendForNew()
    {
        $entity = $this->prophesize(EntityInterface::class);
        $entity->getUid()->willReturn(0);

        $this->mapper->toArray($entity->reveal())->willReturn(['foo' => 'bar']);
        $this->queryBuilder
            ->insert('tx_table')
            ->willReturn($this->queryBuilder->reveal())
            ->shouldBeCalled();
        $this->queryBuilder
            ->values(['foo' => 'bar'])
            ->willReturn($this->queryBuilder->reveal())
            ->shouldBeCalled();
        $this->queryBuilder->execute()->shouldBeCalled();

        $this->repository->append($entity->reveal());
    }

    /**
     * @test
     */
    public function appendForExisting()
    {
        $entity = $this->prophesize(EntityInterface::class);
        $entity->getUid()->willReturn(5);

        $this->mapper->toArray($entity->reveal())->willReturn(['foo' => 'bar']);
        $this->queryBuilder
            ->update('tx_table')
            ->willReturn($this->queryBuilder->reveal())
            ->shouldBeCalled();
        $this->expressionBuilder->eq('uid', 5)->willReturn('uid=5');
        $this->queryBuilder
            ->where('uid=5')
            ->willReturn($this->queryBuilder->reveal())
            ->shouldBeCalled();
        $this->queryBuilder->set('foo', 'bar')->shouldBeCalled();
        $this->queryBuilder->execute()->shouldBeCalled();

        $this->repository->append($entity->reveal());
    }
}
