<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FilterBundle\Filter\Type;

use HeimrichHannot\FilterBundle\Filter\AbstractType;
use HeimrichHannot\FilterBundle\Model\FilterConfigElementModel;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use HeimrichHannot\UtilsBundle\Database\DatabaseUtil;
use Symfony\Component\Form\FormBuilderInterface;

class CheckboxType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildQuery(FilterQueryBuilder $builder, FilterConfigElementModel $element)
    {
        $builder->whereElement($element, $this->getName($element), $this->config, $this->getDefaultOperator($element));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FilterConfigElementModel $element, FormBuilderInterface $builder)
    {
        $builder->add($this->getName($element), \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, $this->getOptions($element, $builder));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultName(FilterConfigElementModel $element)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOperator(FilterConfigElementModel $element)
    {
        return DatabaseUtil::OPERATOR_EQUAL;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(FilterConfigElementModel $element, FormBuilderInterface $builder)
    {
        $options = parent::getOptions($element, $builder);

        if (true === (bool) $element->customValue) {
            $options['value'] = $element->value;
        }

        if ($element->submitOnChange) {
            $options['attr']['onchange'] = 'this.form.submit()';
        }

        return $options;
    }
}
