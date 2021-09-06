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

namespace DMK\Optin\Mail;

use DMK\Optin\Domain\Model\Optin;
use DMK\Optin\Mail\View\FluidView;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;

/**
 * MailProcessor.
 *
 * @author Michael Wagner
 */
class MailProcessor
{
    protected function createView(
        string $templateName,
        array $viewVariables
    ): FluidView {
        /* @var FluidView $view */
        $view = GeneralUtility::makeInstance(FluidView::class);

        $view->setTemplate($templateName);
        $view->assignMultiple($viewVariables);

        return $view;
    }

    /**
     * @param array<string, mixed> $viewVariables
     */
    protected function createMailJob(
        string $templateName,
        array $viewVariables
    ): MailMessage {
        $view = $this->createView($templateName, $viewVariables);

        /* @var MailMessage $mail */
        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail->setFrom(MailUtility::getSystemFrom());
        $mail->text($view->renderBodyText());
        $mail->html($view->renderBodyHtml());
        $mail->subject($view->renderSubject());

        if ($view->getMailFrom()) {
            $mail->setFrom($view->getMailFrom());
        }
        if ($view->getMailTo()) {
            $mail->setTo($view->getMailTo());
        }

        return $mail;
    }

    public function sendOptin(
        Optin $optin,
        string $validationKey
    ): bool {
        $mail = $this->createMailJob(
            'Mail/Optin',
            [
                'optinkey' => $validationKey,
                'optin' => $optin,
            ]
        );

        $mail->setTo($optin->getEmail());

        return $mail->send() > 0;
    }
}
