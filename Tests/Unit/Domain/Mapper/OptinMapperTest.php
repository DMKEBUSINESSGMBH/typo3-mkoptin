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

namespace DMK\Optin\Tests\Domain\Mapper;

use DMK\Optin\Domain\Mapper\OptinMapper;
use DMK\Optin\Domain\Model\Optin;
use Doctrine\DBAL\Driver\Result as QueryResult;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;

/**
 * OptinMapper test.
 *
 * @author Michael Wagner
 */
class OptinMapperTest extends NimutTestingFrameworkTestCase
{
    private ?OptinMapper $mapper = null;
    private string $defaultTimeZone = '';
    private array $defaultOptinRecord = [
        'uid' => 5,
        'pid' => 7,
        'deleted' => 0,
        'hidden' => 0,
        'email' => 'hulk@marvel.localhost',
        'validation_date' => '1922-12-28 10:00:30',
        'validation_hash' => '#ValidationHash',
        'is_validated' => '1',
    ];

    public function setUp()
    {
        $this->mapper = new OptinMapper();
        $this->defaultTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    public function tearDown()
    {
        date_default_timezone_set($this->defaultTimeZone);
    }

    /**
     * @test
     */
    public function fromRecord()
    {
        $entity = $this->mapper->fromRecord($this->defaultOptinRecord);

        $this->assertInstanceOf(Optin::class, $entity);
        $this->assertSame('hulk@marvel.localhost', $entity->getEmail());
        $this->assertSame('1922-12-28 10:00:30', $entity->getValidationDate()->format('Y-m-d H:i:s'));
        $this->assertSame('#ValidationHash', $entity->getValidationHash());
        $this->assertSame(true, $entity->isValidated());

        return $entity;
    }

    /**
     * @test
     */
    public function fromResults()
    {
        $results = $this->prophesize(QueryResult::class);
        $results->fetchAssociative()->willReturn(
            array_merge(
                $this->defaultOptinRecord,
                ['uid' => 14, 'is_validated' => 1]
            ),
            array_merge(
                $this->defaultOptinRecord,
                ['uid' => 80, 'is_validated' => 0]
            ),
            false
        );

        $items = $this->mapper->fromResults($results->reveal());

        $this->assertCount(2, $items);
        $this->assertSame(14, $items[0]->getUid());
        $this->assertSame('hulk@marvel.localhost', $items[0]->getEmail());
        $this->assertSame(true, $items[0]->isValidated());
        $this->assertSame(80, $items[1]->getUid());
        $this->assertSame('hulk@marvel.localhost', $items[1]->getEmail());
        $this->assertSame(false, $items[1]->isValidated());
    }

    /**
     * @test
     * @depends fromRecord
     */
    public function mapDefaultsFromRecordForAbstractEntity(Optin $entity)
    {
        $this->assertEquals(
            $this->defaultOptinRecord,
            $this->mapper->toArray($entity)
        );
    }
}
