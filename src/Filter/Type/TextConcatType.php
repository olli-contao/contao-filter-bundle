<?php
/**
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FilterBundle\Filter\Type;


use Contao\StringUtil;
use HeimrichHannot\FilterBundle\Filter\AbstractType;
use HeimrichHannot\FilterBundle\Filter\TypeInterface;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;

class TextConcatType extends AbstractType implements TypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildQuery(FilterQueryBuilder $builder, array $element)
    {
        $data  = $this->config->getData();
        $name  = $this->getName($element, $element['name']);
        $value = $data[$name];

        if ($value === null) {
            return;
        }

        $fields = StringUtil::deserialize($element['fields'], true);
        $concat = 'CONCAT(' . implode('," ",', $fields) . ')';

        $builder->andWhere($builder->expr()->like($concat, $builder->expr()->literal('%' . $value . '%')));
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $element, FormBuilderInterface $builder)
    {
        $builder->add($this->getName($element, $element['name']), \Symfony\Component\Form\Extension\Core\Type\TextType::class, $this->getOptions($element, $builder));
    }
}