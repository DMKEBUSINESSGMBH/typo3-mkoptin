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

namespace DMK\Optin\Backend\Form\Element;

use DateTime;
use DMK\Optin\Domain\Manager\OptinManager;
use DMK\Optin\Domain\Model\Optin;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Optin state element for tca.
 *
 * @author Michael Wagner
 */
class OptinStateElement extends AbstractFormElement
{
    /**
     * @var string
     */
    private static $llFile = 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf';

    /**
     * Handler for single nodes.
     *
     * @return array<string, mixed> As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $result = $this->initializeResultArray();
        $result['html'] = $this->renderOptInHtml();

        return $result;
    }

    private function renderOptInHtml(): string
    {
        $email = $this->findOptinEmailFromTce();
        if (empty($email)) {
            return '<div style="color:#999;">'.LocalizationUtility::translate(
                    static::$llFile.':tx_mkoptin_domain_model_optin.nomail'
                ).'</div>';
        }
        $optin = $this->getOptinManager()->findOptinByEmail($email);

        if (null === $optin) {
            return '<div style="color:#C99;">'.LocalizationUtility::translate(
                    static::$llFile.':tx_mkoptin_domain_model_optin.nooptin'
                ).'</div>';
        }

        return $this->renderOptinInformations($optin);
    }

    private function getCurrentTableName(): string
    {
        return $this->data['tableName'];
    }

    /**
     * @return array<string,mixed>
     */
    private function getCurrentRecord(): array
    {
        return $this->data['databaseRow'];
    }

    private function getOptinManager(): OptinManager
    {
        return GeneralUtility::makeInstance(OptinManager::class);
    }

    private function findOptinEmailFromTce(): string
    {
        $tableName = $this->getCurrentTableName();
        $record = $this->getCurrentRecord();

        if ((
            'tx_mkoptin_domain_model_optin' === $tableName ||
            empty($record['email'])
        )) {
            return '';
        }

        return $record['email'];
    }

    private function renderOptinInformations(Optin $optin): string
    {
        $content = '<!-- optin ['.$optin->getUid().'] -->';
        $content .= LocalizationUtility::translate(
                static::$llFile.':tx_mkoptin_domain_model_optin.email'
            ).': '.$optin->getEmail();
        $content .= '<br />';
        $content .= LocalizationUtility::translate(
                static::$llFile.':tx_mkoptin_domain_model_optin.is_validated'
            ).': '.($optin->getIsValidated() ? '&#x2714;' : '&#x2716;');
        $content .= '<br />';
        if ($optin->getIsValidated() && $optin->getValidationDate() instanceof DateTime) {
            $content .= LocalizationUtility::translate(
                    static::$llFile.':tx_mkoptin_domain_model_optin.validation_date'
                ).': '.$optin->getValidationDate()->format(DATE_RSS);
        }

        return '<div style="color:'.($optin->getIsValidated() ? '#060;' : '#C00').';">'.$content.'</div>';
    }
}
