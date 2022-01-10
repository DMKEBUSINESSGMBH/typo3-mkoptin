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

namespace DMK\Optin\Tests\Controller;

use DMK\Optin\Controller\OptinController;
use DMK\Optin\Domain\Manager\OptinManager;
use DMK\Optin\Domain\Model\Optin;
use DMK\Optin\Event\OptinValidationSuccessEvent;
use LogicException;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Extbase\Mvc\Request as ExtbaseRequest;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * OptinController test.
 *
 * @author Michael Wagner
 */
class OptinControllerTest extends NimutTestingFrameworkTestCase
{
    private ?OptinController $controller = null;
    private ?ObjectProphecy $optinManager = null;
    private ?ObjectProphecy $request = null;
    private ?ObjectProphecy $eventDispatcher = null;
    private ?ObjectProphecy $view = null;

    public function setUp()
    {
        $this->optinManager = $this->prophesize(OptinManager::class);
        $this->request = $this->prophesize(ExtbaseRequest::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcher::class);
        $this->view = $this->prophesize(ViewInterface::class);
        $this->controller = new OptinController($this->optinManager->reveal());
        $this->inject($this->controller, 'request', $this->request->reveal());
        $this->inject($this->controller, 'eventDispatcher', $this->eventDispatcher->reveal());
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     */
    public function validationActionWithoutKey()
    {
        $this->request->hasArgument('key')->willReturn(false)->shouldBeCalledOnce();
        $this->view->assign('success', false)->shouldBeCalledOnce();
        $this->controller->validationAction();
    }

    /**
     * @test
     */
    public function validationActionWithInvalidKey()
    {
        $this->request->hasArgument('key')->willReturn(true)->shouldBeCalledOnce();
        $this->request->getArgument('key')->willReturn('optinkey')->shouldBeCalledOnce();

        $this->optinManager->getOptinByKey('optinkey')->willThrow(new LogicException())->shouldBeCalledOnce();

        $this->view->assign('success', false)->shouldBeCalledOnce();
        $this->controller->validationAction();
    }

    /**
     * @test
     */
    public function validationActionWithValidKey()
    {
        $this->request->hasArgument('key')->willReturn(true)->shouldBeCalledOnce();
        $this->request->getArgument('key')->willReturn('optinkey')->shouldBeCalledOnce();

        $optin = new Optin();
        $this->optinManager->getOptinByKey('optinkey')->willReturn($optin)->shouldBeCalledOnce();
        $this->optinManager->validateByKey('optinkey')->shouldBeCalledOnce();

        $this->eventDispatcher->dispatch(Argument::type(OptinValidationSuccessEvent::class))->willReturnArgument()->shouldBeCalledOnce();
        $this->view->assign('optin', $optin)->shouldBeCalledOnce();

        $this->view->assign('success', true)->shouldBeCalledOnce();
        $this->controller->validationAction();
    }
}
