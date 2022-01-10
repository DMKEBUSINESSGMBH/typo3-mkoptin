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
    /**
     * @param array<string, mixed> $viewVariables
     */
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
