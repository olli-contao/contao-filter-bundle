<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FilterBundle\ContentElement;

use Contao\ContentElement;
use Contao\Controller;
use Contao\System;
use HeimrichHannot\FilterBundle\Model\FilterPreselectModel;
use Patchwork\Utf8;

class ContentFilterPreselect extends ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_filter_initial';

    public function generate()
    {
        if (System::getContainer()->get('huh.utils.container')->isBackend()) {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = implode("\n", $this->getWildcard());
            $objTemplate->title = '### '.Utf8::strtoupper(($GLOBALS['TL_LANG']['CTE'][$this->type][0])).' ###';

            return $objTemplate->parse();
        }

        $this->preselect();

        return parent::generate();
    }

    /**
     * Get the wildcard from preselection.
     *
     * @return array
     */
    protected function getWildcard(): array
    {
        $wildcard = [];

        if (null === ($filterConfig = System::getContainer()->get('huh.filter.manager')->findById($this->filterConfig)) || null === ($elements = $filterConfig->getElements())) {
            return $wildcard;
        }

        /** @var FilterPreselectModel $preselections */
        $preselections = System::getContainer()->get('contao.framework')->createInstance(FilterPreselectModel::class);

        if (null === ($preselections = $preselections->findPublishedByPidAndTableAndField($this->id, 'tl_content', 'filterPreselect'))) {
            return $wildcard;
        }

        /** @var FilterPreselectModel $preselection */
        foreach ($preselections as $preselection) {
            $wildcard[] = System::getContainer()->get('huh.filter.backend.filter_preselect')->adjustLabel($preselection->row(), $preselection->id);
        }

        return $wildcard;
    }

    /**
     * Invoke preselection.
     */
    protected function preselect()
    {
        if (null === ($filterConfig = System::getContainer()->get('huh.filter.manager')->findById($this->filterConfig)) || null === ($elements = $filterConfig->getElements())) {
            return;
        }

        /** @var FilterPreselectModel $preselections */
        $preselections = System::getContainer()->get('contao.framework')->createInstance(FilterPreselectModel::class);

        if (null === ($preselections = $preselections->findPublishedByPidAndTableAndField($this->id, 'tl_content', 'filterPreselect'))) {
            $filterConfig->resetData(); // reset previous filters

            return;
        }

        $data = System::getContainer()->get('huh.filter.util.filter_preselect')->getPreselectData($this->filterConfig, $preselections->getModels());

        if (true === (bool) $this->filterReset) {
            $filterConfig->resetData();
        } else {
            $filterConfig->setData($data);
        }

        if (true !== (bool) $this->filterPreselectNoRedirect) {
            Controller::redirect($filterConfig->getUrl());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
    }
}