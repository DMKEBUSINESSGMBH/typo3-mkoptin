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
use DMK\Optin\Domain\Model\Optin;
use Doctrine\DBAL\ForwardCompatibility\Result as QueryResult;
use InvalidArgumentException;

/**
 * OptinMapper.
 *
 * @author Michael Wagner
 */
class OptinMapper extends AbstractMapper implements MapperInterface
{
    protected Optin $optin;

    public function __construct(Optin $optin)
    {
        $this->optin = $optin;
    }

    public function getEntity(): Optin
    {
        return $this->optin;
    }

    public static function fromEntity(EntityInterface $entity): OptinMapper
    {
        if (!$entity instanceof Optin) {
            throw new InvalidArgumentException();
        }

        return new OptinMapper($entity);
    }

    /**
     * @param array<string, string> $result
     *
     * @return array<int, Optin>
     */
    public static function fromResults(QueryResult $result): array
    {
        $items = [];

        while (($record = $result->fetchAssociative()) !== false) {
            $items[] = static::fromRecord($record)->getEntity();
        }

        return $items;
    }

    /**
     * @param array<string, string> $result
     */
    public static function fromRecord(array $record): OptinMapper
    {
        $optin = new Optin();
        static::mapDefaultsFromRecord($record, $optin);

        $optin->setEmail($record['email']);
        $validationDate = static::mapDateFromString($record['validation_date']);
        if (null !== $validationDate) {
            $optin->setValidationDate($validationDate);
        }
        $optin->setIsValidated($record['is_validated'] > 0);
        $optin->setValidationHash($record['validation_hash']);

        return new OptinMapper($optin);
    }

    public function toArray(): array
    {
        return array_merge(
            static::mapDefaultsToArray($this->optin),
            [
                'email' => $this->optin->getEmail(),
                'is_validated' => $this->optin->isValidated(),
                'validation_hash' => $this->optin->getValidationHash(),
                'validation_date' => static::mapDateToString($this->optin->getValidationDate()),
            ]
        );
    }
}
