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

    public function validationAction()
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
                // if the kay cant be resolved or something else, show failure in template.
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
