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

namespace DMK\Optin\Domain\Mapper;

use DMK\Optin\Domain\Model\EntityInterface;
use Doctrine\DBAL\Driver\Result as QueryResult;

/**
 * MapperInterface.
 *
 * @author Michael Wagner
 */
interface MapperInterface
{
    /**
     * @return array<int, EntityInterface>
     */
    public function fromResults(QueryResult $result): array;

    /**
     * @param array<string, string> $record
     */
    public function fromRecord(array $record): EntityInterface;

    /**
     * @return array<string, mixed>
     */
    public function toArray(EntityInterface $entity): array;
}
