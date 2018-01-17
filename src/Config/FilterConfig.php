<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\FilterBundle\Config;

use Contao\System;
use HeimrichHannot\FilterBundle\Filter\TypeInterface;
use HeimrichHannot\FilterBundle\Form\FilterType;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\PropertyAccess\PropertyAccess;

class FilterConfig
{
    /**
     * @var string
     */
    protected $sessionKey;

    /**
     * @var array
     */
    protected $resetNames;

    /**
     * @var array|null
     */
    protected $filter;

    /**
     * @var array|null
     */
    protected $elements;

    /**
     * @var FormBuilderInterface|null
     */
    protected $builder;

    /**
     * @var FilterQueryBuilder
     */
    protected $queryBuilder;

    /**
     * Init the filter based on its model.
     *
     * @param string     $sessionKey
     * @param array      $filter
     * @param array|null $elements
     */
    public function init(string $sessionKey, array $filter, $elements = null)
    {
        $this->sessionKey = $sessionKey;
        $this->filter = $filter;
        $this->elements = $elements;
    }

    /**
     * Build the form.
     *
     * @param array $data
     */
    public function buildForm(array $data = [])
    {
        if (null === $this->filter) {
            return;
        }

        $factory = Forms::createFormFactoryBuilder()->addExtensions([])->getFormFactory();

        $options = ['filter' => $this];

        $cssClass = [];

        if ('' !== $this->filter['cssClass']) {
            $cssClass[] = $this->filter['cssClass'];
        }

        if ($this->hasData()) {
            $cssClass[] = 'has-data';
        }

        if (!empty($cssClass)) {
            $options['attr']['class'] = implode(' ', $cssClass);
        }

        if (true === (bool) $this->filter['renderEmpty']) {
            $data = [];
        }

        $this->builder = $factory->createNamedBuilder($this->filter['name'], FilterType::class, $data, $options);

        $this->mapFormsToData();
    }

    public function initQueryBuilder()
    {
        $this->queryBuilder = System::getContainer()->get('huh.filter.query_builder');

        if (!is_array($this->getElements())) {
            return;
        }

        $types = \System::getContainer()->get('huh.filter.choice.type')->getCachedChoices();

        if (!is_array($types) || empty($types)) {
            return;
        }

        $this->queryBuilder->from($this->getFilter()['dataContainer']);

        foreach ($this->getElements() as $element) {
            if (!isset($types[$element['type']])) {
                continue;
            }

            $class = $types[$element['type']];

            if (!class_exists($class)) {
                continue;
            }

            /**
             * @var TypeInterface
             */
            $type = new $class($this);

            if (!is_subclass_of($type, \HeimrichHannot\FilterBundle\Filter\AbstractType::class) || !is_subclass_of($type, TypeInterface::class)) {
                continue;
            }

            $type->buildQuery($this->queryBuilder, $element);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->filter['id'];
    }

    /**
     * @return array|null
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return array|null
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @return null|FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    /**
     * @return FilterQueryBuilder|null
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Get the filter data (e.g. form submission data).
     *
     * @return array
     */
    public function getData(): array
    {
        return System::getContainer()->get('huh.filter.session')->getData($this->getSessionKey());
    }

    /**
     * Has the filter data (e.g. form submitted?).
     *
     * @return bool
     */
    public function hasData(): bool
    {
        return System::getContainer()->get('huh.filter.session')->hasData($this->getSessionKey());
    }

    /**
     * @return array
     */
    public function getResetNames(): array
    {
        return !is_array($this->resetNames) ? [$this->resetNames] : $this->resetNames;
    }

    /**
     * @param string $resetName
     */
    public function addResetName(string $resetName)
    {
        $this->resetNames[] = $resetName;
    }

    /**
     * @param array $resetName
     */
    public function setResetNames(array $resetNames)
    {
        $this->resetName = $resetNames;
    }

    /**
     * Maps the data of the current forms and update builder data.
     */
    protected function mapFormsToData()
    {
        $data = [];
        $forms = $this->builder->getForm();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        /**
         * @var FormInterface
         */
        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            // Write-back is disabled if the form is not synchronized (transformation failed),
            // if the form was not submitted and if the form is disabled (modification not allowed)
            if (null !== $propertyPath && $config->getMapped() && $form->isSynchronized() && !$form->isDisabled()) {
                // If the field is of type DateTime and the data is the same skip the update to
                // keep the original object hash
                if ($form->getData() instanceof \DateTime && $form->getData() === $propertyAccessor->getValue($data, $propertyPath)) {
                    continue;
                }

                // If the data is identical to the value in $data, we are
                // dealing with a reference
                if (!is_object($data) || !$config->getByReference() || $form->getData() !== $propertyAccessor->getValue($data, $propertyPath)) {
                    $propertyAccessor->setValue($data, $propertyPath, $form->getData());
                }
            }
        }

        $this->builder->setData($data);
    }
}
