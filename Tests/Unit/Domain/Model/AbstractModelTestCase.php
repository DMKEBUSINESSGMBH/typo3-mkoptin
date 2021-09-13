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

namespace DMK\Optin\Tests\Domain\Model;

use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use PHPUnit\Framework\Assert;

/**
 * Company model test.
 *
 * @author Michael Wagner
 */
abstract class AbstractModelTestCase extends NimutTestingFrameworkTestCase
{
    private $model = null;

    public function setUp()
    {
        $modelClass = $this->getModelClass();
        $this->model = new $modelClass();
    }

    abstract protected function getModelClass(): string;

    abstract protected function getModelProperties(): array;

    /**
     * @test
     * @dataProvider getGetterAndSetterData
     */
    public function testGetterAndSetter(string $property, $value): void
    {
        $this->assertGetterSetter($property, $value);
    }

    public function getGetterAndSetterData(): array
    {
        $values = [];
        $dataSet = 0;

        foreach ($this->getModelProperties() as $property => $value) {
            $values[$dataSet++.'_'.$property] = [$property, $value];
        }

        return $values;
    }

    protected function assertGetterSetter(string $property, $value): void
    {
        $getter = 'get'.ucfirst($property);
        if (is_bool($value) && method_exists($this->model, 'is'.ucfirst($property))) {
            $getter = 'is'.ucfirst($property);
        }
        $setter = 'set'.ucfirst($property);
        // object properties are initially null,
        // all others should be initialized with empty value
        $this->assertThat(
            $this->model->$getter(),
            is_object($value)
                ? Assert::isNull()
                : Assert::isType(gettype($value))
        );
        $this->assertEmpty($this->model->$getter());
        $this->assertNull($this->model->$setter($value));
        $this->assertSame($value, $this->model->$getter());
    }
}
