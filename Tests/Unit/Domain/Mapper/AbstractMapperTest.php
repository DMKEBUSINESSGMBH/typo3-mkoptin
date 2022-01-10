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

namespace DMK\Optin\Tests\Domain\Mapper;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use DMK\Optin\Domain\Mapper\AbstractMapper;
use DMK\Optin\Domain\Model\AbstractEntity;
use DMK\Optin\Domain\Model\EntityInterface;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;

/**
 * AbstractMapper test.
 *
 * @author Michael Wagner
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class AbstractMapperTest extends NimutTestingFrameworkTestCase
{
    private ?AbstractMapper $mapper = null;
    private string $defaultTimeZone = '';

    public function setUp()
    {
        $this->mapper = new class() extends AbstractMapper {};
        $this->defaultTimeZone = date_default_timezone_get();
    }

    public function tearDown()
    {
        date_default_timezone_set($this->defaultTimeZone);
    }

    /**
     * @test
     */
    public function mapDefaultsFromRecordDoesNothing()
    {
        $record = [];

        $entity = $this->prophesize(EntityInterface::class);

        $this->callInaccessibleMethod(
            $this->mapper,
            'mapDefaultsFromRecord',
            $record,
            $entity->reveal()
        );

        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     * @dataProvider mapDefaultsFromRecordForAbstractEntityData
     */
    public function mapDefaultsFromRecordForAbstractEntity(array $record, array $expects)
    {
        $entity = $this->prophesize(AbstractEntity::class);

        foreach ($expects as $getter => $value) {
            $entity->__call($getter, [$value])->shouldBeCalled();
        }

        $this->callInaccessibleMethod(
            $this->mapper,
            'mapDefaultsFromRecord',
            $record,
            $entity->reveal()
        );
    }

    public function mapDefaultsFromRecordForAbstractEntityData(): array
    {
        return [
            __LINE__ => [
                'record' => [
                    'uid' => 5,
                    'pid' => 7,
                    'deleted' => 0,
                    'hidden' => 1,
                ],
                'expects' => [
                    'setUid' => 5,
                    'setPid' => 7,
                    'setDeleted' => false,
                    'setHidden' => true,
                ],
            ],
            __LINE__ => [
                'record' => [
                    'uid' => 14,
                    'pid' => 80,
                    'deleted' => 1,
                    'hidden' => 0,
                ],
                'expects' => [
                    'setUid' => 14,
                    'setPid' => 80,
                    'setDeleted' => true,
                    'setHidden' => false,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function mapDefaultsToArrayForEntityInterface()
    {
        $entity = $this->prophesize(EntityInterface::class);
        $entity->getUid()->willReturn(7);

        $record = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDefaultsToArray',
            $entity->reveal()
        );

        $this->assertSame(7, $record['uid']);
    }

    /**
     * @test
     * @dataProvider mapDefaultsToArrayForAbstractEntityData
     */
    public function mapDefaultsToArrayForAbstractEntity(array $entityData, array $expectedRecord)
    {
        $entity = $this->prophesize(AbstractEntity::class);

        foreach ($entityData as $getter => $value) {
            $entity->__call($getter, [])->willReturn($value)->shouldBeCalled();
        }

        $record = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDefaultsToArray',
            $entity->reveal()
        );

        $this->assertSame($expectedRecord, $record);
    }

    public function mapDefaultsToArrayForAbstractEntityData(): array
    {
        return [
            __LINE__ => [
                '$entityData' => [
                    'getUid' => 5,
                    'getPid' => 7,
                    'isDeleted' => false,
                    'isHidden' => true,
                ],
                '$expectedRecord' => [
                    'uid' => 5,
                    'pid' => 7,
                    'deleted' => 0,
                    'hidden' => 1,
                ],
            ],
            __LINE__ => [
                '$entityData' => [
                    'getUid' => 14,
                    'getPid' => 80,
                    'isDeleted' => true,
                    'isHidden' => false,
                ],
                '$expectedRecord' => [
                    'uid' => 14,
                    'pid' => 80,
                    'deleted' => 1,
                    'hidden' => 0,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function mapDateToStringDoesNothing()
    {
        $dateString = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateToString',
            null
        );

        $this->assertNull($dateString);
    }

    /**
     * @test
     */
    public function mapDateToStringForUTC()
    {
        $dateString = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateToString',
            new DateTime('1922-12-28 12:00:30', new DateTimeZone('UTC'))
        );

        $this->assertEquals('1922-12-28 12:00:30', $dateString);
    }

    /**
     * @test
     */
    public function mapDateToStringForGMT2()
    {
        $dateString = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateToString',
            new DateTime('1922-12-28 12:00:30', new DateTimeZone('Etc/GMT-2'))
        );

        $this->assertEquals('1922-12-28 10:00:30', $dateString);
    }

    /**
     * @test
     */
    public function mapDateFromStringReturnsNullForEmptyString()
    {
        $dateObject = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateFromString',
            ''
        );

        $this->assertNull($dateObject);
    }

    /**
     * @test
     */
    public function mapDateFromStringReturnsNullForZeroDate()
    {
        $dateObject = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateFromString',
            '0000-00-00 00:00:00'
        );

        $this->assertNull($dateObject);
    }

    /**
     * @test
     */
    public function mapDateFromStringReturnsDatetimeObjectForUTC()
    {
        date_default_timezone_set('Etc/UTC');
        $dateObject = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateFromString',
            '1922-12-28 12:00:30'
        );

        $this->assertInstanceOf(DateTimeInterface::class, $dateObject);
        $this->assertSame(
            '1922-12-28 12:00:30',
            $dateObject->format('Y-m-d H:i:s')
        );
    }

    /**
     * @test
     */
    public function mapDateFromStringReturnsDatetimeObjectForGMT2()
    {
        date_default_timezone_set('Etc/GMT-2');

        $dateObject = $this->callInaccessibleMethod(
            $this->mapper,
            'mapDateFromString',
            '1922-12-28 10:00:30'
        );

        $this->assertInstanceOf(DateTimeInterface::class, $dateObject);
        $this->assertSame(
            '1922-12-28 12:00:30',
            $dateObject->format('Y-m-d H:i:s')
        );
    }
}
