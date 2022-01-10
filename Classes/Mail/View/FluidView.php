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

namespace DMK\Optin\Mail\View;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\View\TemplatePaths;

/**
 * FluidView.
 *
 * @author Michael Wagner
 */
class FluidView extends StandaloneView
{
    public function __construct(string $packageName = 'mkoptin')
    {
        parent::__construct();

        $this->getRenderingContext()->setTemplatePaths(new TemplatePaths($packageName));
        $this->assignSettings();
    }

    protected function assignSettings(): void
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
        $this->assign('settings', $settings);
    }

    public function renderBodyHtml(): string
    {
        $this->setFormat('html');

        return $this->render();
    }

    public function renderBodyText(): string
    {
        $this->setFormat('txt');

        return $this->render();
    }

    /**
     * Returns the subject variable, which was set in template during rendering.
     */
    public function renderSubject(): string
    {
        return $this->getRenderingContextVariable('mailSubject');
    }

    /**
     * Returns the mail to variable, which was set in template during rendering.
     *
     * @return array<int, string>
     */
    public function getMailTo(): array
    {
        return GeneralUtility::trimExplode(
            ',',
            $this->getRenderingContextVariable('mailTo'),
            true
        );
    }

    /**
     * Returns the mail from variable, which was set in template during rendering.
     */
    public function getMailFrom(): string
    {
        return $this->getRenderingContextVariable('mailFrom');
    }

    /**
     * Returns a variable from the rendering context variable provider.
     *
     * If the variable is set in template, body should be rendered first!
     */
    protected function getRenderingContextVariable(string $identifier): string
    {
        $value = $this->getRenderingContext()->getVariableProvider()->get($identifier);

        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }
}
