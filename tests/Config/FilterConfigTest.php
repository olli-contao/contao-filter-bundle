<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FilterBundle\Tests\Config;

use Contao\InsertTags;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\FilterBundle\Config\FilterConfig;
use HeimrichHannot\FilterBundle\Entity\FilterSession;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class FilterConfigTest extends ContaoTestCase
{
    /**
     * Tests the object instantiation.
     */
    public function testCanBeInstantiated()
    {
        $framework = $this->mockContaoFramework([]);
        $session = new MockArraySessionStorage();

        $config = new FilterConfig($framework, new FilterSession($framework, new Session($session)));

        $this->assertInstanceOf('HeimrichHannot\FilterBundle\Config\FilterConfig', $config);
    }

    /**
     * Tests init.
     */
    public function testInit()
    {
        $framework = $this->mockContaoFramework([]);
        $session = new MockArraySessionStorage();

        $config = new FilterConfig($framework, new FilterSession($framework, new Session($session)));
        $config->init('test', []);

        $this->assertSame('test', $config->getSessionKey());
        $this->assertSame([], $config->getFilter());
        $this->assertNull($config->getElements());
    }

    /**
     * Test form builder.
     */
    public function testBuildFormWithNotFilter()
    {
        $framework = $this->mockContaoFramework([]);
        $session = new MockArraySessionStorage();

        $config = new FilterConfig($framework, new FilterSession($framework, new Session($session)));
        $config->buildForm();

        $this->assertNull($config->getBuilder(), $config);
    }

    /**
     * Test form builder.
     */
    public function testBuildFormWithoutElementsAndAction()
    {
        $filter = [
            'id' => 1,
            'dataContainer' => 'tl_member',
            'name' => 'test_form',
            'method' => 'GET',
        ];

        $framework = $this->mockContaoFramework([]);
        $session = new MockArraySessionStorage();

        $config = new FilterConfig($framework, new FilterSession($framework, new Session($session)));
        $config->init('test', $filter);
        $config->buildForm();

        $this->assertNotNull($config->getBuilder(), $config);
        $this->assertInstanceOf(FormBuilder::class, $config->getBuilder());
    }

    /**
     * Test form builder.
     */
    public function testBuildFormWithInsertTagActionAndWithoutElements()
    {
        $filter = [
            'id' => 1,
            'dataContainer' => 'tl_member',
            'name' => 'test_form',
            'method' => 'GET',
            'action' => '{{env::path}}',
        ];

        $insertTagAdapter = $this->mockConfiguredAdapter(['replace' => '/test']);
        $adapters[InsertTags::class] = $insertTagAdapter;

        $framework = $this->mockContaoFramework($adapters);
        $session = new MockArraySessionStorage();

        $config = new FilterConfig($framework, new FilterSession($framework, new Session($session)));
        $config->init('test', $filter);
        $config->buildForm();

        $this->assertNotNull($config->getBuilder(), $config);
        $this->assertInstanceOf(FormBuilder::class, $config->getBuilder());
        $this->assertSame('/test', $config->getBuilder()->getAction());
    }
}
