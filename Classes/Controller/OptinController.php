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

namespace DMK\Optin\Controller;

use DMK\Optin\Domain\Manager\OptinManager;
use DMK\Optin\Event\OptinValidationSuccessEvent;
use LogicException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * OptinController.
 *
 * @author Michael Wagner
 */
class OptinController extends ActionController
{
    private OptinManager $optinManager;

    public function __construct(
        OptinManager $optinManager
    ) {
        $this->optinManager = $optinManager;
    }

    public function validationAction(): void
    {
        $success = false;
        $optinKey = null;
        if ($this->request->hasArgument('key')) {
            $optinKey = $this->request->getArgument('key');
        }
        if (null !== $optinKey && is_scalar($optinKey)) {
            $optin = null;
            try {
                $optin = $this->optinManager->getOptinByKey((string) $optinKey);
                $this->optinManager->validateByKey((string) $optinKey);
                $success = true;
            } catch (LogicException $exception) {
                // if the kay can't be resolved or something else, show failure in template.
            }

            if (null !== $optin) {
                $this->view->assign(
                    'optin',
                    $this->eventDispatcher->dispatch(
                        new OptinValidationSuccessEvent($optin)
                    )->getOptin()
                );
            }
        }

        $this->view->assign('success', $success);
    }
}
