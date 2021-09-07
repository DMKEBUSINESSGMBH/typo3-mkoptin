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

namespace DMK\Optin\Tests\Fluid\View;

use DMK\Optin\Mail\View\FluidView;
use Nimut\TestingFramework\TestCase\AbstractTestCase as NimutTestingFrameworkTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

/**
 * FluidView test.
 *
 * @author Michael Wagner
 */
class FluidViewTest extends NimutTestingFrameworkTestCase
{
    public function tearDown()
    {
        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     */
    public function assignSettings()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['assign'])
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = $this->prophesize(ObjectManager::class);
        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManager->reveal());
        $configurationManager = $this->prophesize(ConfigurationManagerInterface::class);
        $objectManager->get(ConfigurationManagerInterface::class)->willReturn($configurationManager->reveal());
        $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)->willReturn(['tsconfig']);

        $view->expects($this->once())->method('assign')->with('settings', ['tsconfig']);

        $this->callInaccessibleMethod($view, 'assignSettings');
    }

    /**
     * @test
     */
    public function renderBodyHtml()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['setFormat', 'render'])
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects($this->once())->method('setFormat')->with('html');
        $view->expects($this->once())->method('render')->willReturn('<html>');

        $this->assertSame('<html>', $view->renderBodyHtml());
    }

    /**
     * @test
     */
    public function renderBodyText()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['setFormat', 'render'])
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects($this->once())->method('setFormat')->with('txt');
        $view->expects($this->once())->method('render')->willReturn('#text');

        $this->assertSame('#text', $view->renderBodyText());
    }

    /**
     * @test
     */
    public function renderSubject()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['getRenderingContextVariable'])
            ->disableOriginalConstructor()
            ->getMock();
        $view
            ->expects($this->once())
            ->method('getRenderingContextVariable')
            ->with('mailSubject')
            ->willReturn('hulk@marvel.localhost');

        $this->assertSame('hulk@marvel.localhost', $view->renderSubject());
    }

    /**
     * @test
     */
    public function getMailTo()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['getRenderingContextVariable'])
            ->disableOriginalConstructor()
            ->getMock();
        $view
            ->expects($this->once())
            ->method('getRenderingContextVariable')
            ->with('mailTo')
            ->willReturn('hulk@marvel.localhost, ironman@marvel.localhost');

        $this->assertSame(
            ['hulk@marvel.localhost', 'ironman@marvel.localhost'],
            $view->getMailTo()
        );
    }

    /**
     * @test
     */
    public function getMailFrom()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['getRenderingContextVariable'])
            ->disableOriginalConstructor()
            ->getMock();
        $view
            ->expects($this->once())
            ->method('getRenderingContextVariable')
            ->with('mailFrom')
            ->willReturn('hulk@marvel.localhost');

        $this->assertSame('hulk@marvel.localhost', $view->getMailFrom());
    }

    /**
     * @test
     */
    public function getRenderingContextVariable()
    {
        $view = $this->getMockBuilder(FluidView::class)
            ->setMethods(['getRenderingContext'])
            ->disableOriginalConstructor()
            ->getMock();

        $renderingContext = $this->prophesize(RenderingContextInterface::class);
        $variableProvider = $this->prophesize(VariableProviderInterface::class);
        $variableProvider->get('foo')->willReturn('bar')->shouldBeCalled();
        $renderingContext->getVariableProvider()->willReturn($variableProvider->reveal());

        $view
            ->expects($this->once())
            ->method('getRenderingContext')
            ->willReturn($renderingContext->reveal());

        $this->assertSame(
            'bar',
            $this->callInaccessibleMethod(
                $view,
                'getRenderingContextVariable',
                'foo'
            )
        );
    }
}
