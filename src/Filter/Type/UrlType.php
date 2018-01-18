<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\FilterBundle\Filter\Type;

use HeimrichHannot\FilterBundle\Filter\AbstractType;
use HeimrichHannot\FilterBundle\Filter\TypeInterface;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;

class UrlType extends AbstractType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildQuery(FilterQueryBuilder $builder, array $element)
    {
        $builder->whereElement($element, $this->getName($element), $this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $element, FormBuilderInterface $builder)
    {
        $builder->add($this->getName($element), \Symfony\Component\Form\Extension\Core\Type\UrlType::class, $this->getOptions($element, $builder));
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(array $element, FormBuilderInterface $builder)
    {
        $options = parent::getOptions($element, $builder);

        $options['default_protocol'] = (string) $element['defaultProtocol'] ?: 'http';

        return $options;
    }
}