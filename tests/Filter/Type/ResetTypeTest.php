<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FilterBundle\Tests\Filter\Type;

use Contao\CoreBundle\Config\ResourceFinder;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Mysqli\Driver;
use HeimrichHannot\FilterBundle\Choice\TypeChoice;
use HeimrichHannot\FilterBundle\Config\FilterConfig;
use HeimrichHannot\FilterBundle\Filter\Type\ResetType;
use HeimrichHannot\FilterBundle\Filter\Type\SubmitType;
use HeimrichHannot\FilterBundle\Model\FilterConfigElementModel;
use HeimrichHannot\FilterBundle\Session\FilterSession;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\Translator;

class ResetTypeTest extends ContaoTestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Kernel
     */
    private $kernel;


    protected function setUp()
    {
        parent::setUp();

        if (!defined('TL_ROOT')) {
            \define('TL_ROOT', $this->getFixturesDir());
        }

        $GLOBALS['TL_LANGUAGE']    = 'en';
        $GLOBALS['TL_LANG']['MSC'] = ['test' => 'bar'];

        $GLOBALS['TL_DCA']['tl_test'] = [
            'config' => [
                'dataContainer' => 'Table',
                'sql'           => [
                    'keys' => [
                    ],
                ],
            ],
            'fields' => [

            ]
        ];

        $GLOBALS['TL_DCA']['tl_filter_config_element'] = [
            'config' => [
                'dataContainer' => 'Table',
                'sql'           => [
                    'keys' => [
                    ],
                ],
            ],
            'fields' => [

            ]
        ];

        $finder = new ResourceFinder([
            $this->getFixturesDir() . '/vendor/contao/core-bundle/Resources/contao',
        ]);

        $this->container = $this->mockContainer();
        $this->container->set('contao.resource_finder', $finder);
        $this->container->setParameter('kernel.debug', true);
        $this->container->setParameter('kernel.default_locale', 'de');
        $this->container->set('translator', new Translator('en'));

        $this->kernel = $this->createMock(Kernel::class);
        $this->kernel->method('getContainer')->willReturn($this->container);

        $this->container->set('kernel', $this->kernel);
    }

    /**
     * Tests the object instantiation.
     */
    public function testCanBeInstantiated()
    {
        System::setContainer($this->container);

        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        $type = new ResetType($config);

        $this->assertInstanceOf('HeimrichHannot\FilterBundle\Filter\Type\ResetType', $type);
    }

    /**
     * Test getDefaultOperator()
     */
    public function testGetDefaultOperator()
    {
        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        /** @var FilterConfigElementModel $element */
        $element = $this->mockClassWithProperties(FilterConfigElementModel::class, []);

        $type = new ResetType($config);

        $this->assertNull($type->getDefaultOperator($element));
    }

    /**
     * Test getDefaultName()
     */
    public function testGetDefaultName()
    {
        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        $range       = new FilterConfigElementModel();
        $range->name = 'test';

        $type = new ResetType($config);

        $this->assertEquals('reset', $type->getDefaultName($range));
    }

    /**
     * Test buildForm() without name
     */
    public function testBuildFormWithoutName()
    {
        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        $this->container->setParameter('huh.filter', [
            'filter' => [
                'types' => [
                    [
                        'name'  => 'reset',
                        'class' => ResetType::class,
                        'type'  => 'button'
                    ]
                ]
            ]
        ]);

        $this->container->set('huh.filter.choice.type', new TypeChoice($framework));
        System::setContainer($this->container);

        $filter = ['name' => 'test', 'dataContainer' => 'tl_test'];

        $element       = new FilterConfigElementModel();
        $element->type = 'reset';

        $config->init('test', $filter, [$element]);
        $config->buildForm();

        $this->assertEquals(1, $config->getBuilder()->count()); // f_id element always exists
    }

    /**
     * Test buildForm() with name
     */
    public function testBuildFormWithName()
    {
        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        $this->container->setParameter('huh.filter', [
            'filter' => [
                'types' => [
                    [
                        'name'  => 'submit',
                        'class' => SubmitType::class,
                        'type'  => 'button'
                    ]
                ]
            ]
        ]);

        $this->container->set('huh.filter.choice.type', new TypeChoice($framework));
        System::setContainer($this->container);

        $filter = ['name' => 'test', 'dataContainer' => 'tl_test'];

        $element       = new FilterConfigElementModel();
        $element->type = 'submit';
        $element->name = 'test';

        $config->init('test', $filter, [$element]);
        $config->buildForm();

        $this->assertEquals(2, $config->getBuilder()->count()); // f_id element always exists
        $this->assertFalse($config->getBuilder()->has('test'));
        $this->assertInstanceOf(\Symfony\Component\Form\Extension\Core\Type\SubmitType::class, $config->getBuilder()->get('submit')->getType()->getInnerType());
    }

    /**
     * Test buildForm() with custom label
     */
    public function testBuildFormWithLabel()
    {
        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        $this->container->setParameter('huh.filter', [
            'filter' => [
                'types' => [
                    [
                        'name'  => 'submit',
                        'class' => SubmitType::class,
                        'type'  => 'button'
                    ]
                ]
            ]
        ]);

        $this->container->set('huh.filter.choice.type', new TypeChoice($framework));
        System::setContainer($this->container);

        $filter = ['name' => 'test', 'dataContainer' => 'tl_test'];

        $element              = new FilterConfigElementModel();
        $element->type        = 'submit';
        $element->name        = 'test';
        $element->customLabel = true;
        $element->label       = 'Button label';

        $config->init('test', $filter, [$element]);
        $config->buildForm();

        $this->assertEquals(2, $config->getBuilder()->count()); // f_id element always exists
        $this->assertFalse($config->getBuilder()->has('test'));
        $this->assertInstanceOf(\Symfony\Component\Form\Extension\Core\Type\SubmitType::class, $config->getBuilder()->get('submit')->getType()->getInnerType());
        $this->assertEquals('Button label', $config->getBuilder()->get('submit')->getForm()->getConfig()->getOption('label'));
    }

    /**
     * Test buildQuery()
     */
    public function testBuildQuery()
    {
        $framework = $this->mockContaoFramework();
        $session   = new MockArraySessionStorage();

        $queryBuilder = new FilterQueryBuilder($framework, new Connection([], new Driver()));
        $config       = new FilterConfig($framework, new FilterSession($framework, new Session($session)), $queryBuilder);

        $this->container->setParameter('huh.filter', [
            'filter' => [
                'types' => [
                    [
                        'name'  => 'submit',
                        'class' => SubmitType::class,
                        'type'  => 'button',
                    ]
                ]
            ]
        ]);

        $this->container->set('huh.filter.choice.type', new TypeChoice($framework));
        System::setContainer($this->container);

        $filter = ['name' => 'test', 'dataContainer' => 'tl_test'];

        $element       = new FilterConfigElementModel();
        $element->id   = 2;
        $element->type = 'submit';
        $element->name = 'start';

        $config->init('test', $filter, [$element]);
        $config->initQueryBuilder();

        $this->assertEmpty($config->getQueryBuilder()->getParameters());
        $this->assertEmpty($config->getQueryBuilder()->getQueryPart('where'));
    }

    /**
     * @return string
     */
    protected function getFixturesDir(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '../..' . DIRECTORY_SEPARATOR . 'Fixtures';
    }
}