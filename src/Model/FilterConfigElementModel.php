<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FilterBundle\Model;

/**
 * Reads and writes filter.
 *
 * @property int $id
 * @property int $pid
 * @property int $sorting
 * @property int $tstamp
 * @property int $dateAdded
 * @property string $type
 * @property string $title
 * @property string $field
 * @property array $fields
 * @property bool $customOptions
 * @property array $options
 * @property bool $customName
 * @property string $name
 * @property bool $addPlaceholder
 * @property string $placeholder
 * @property bool $customLabel
 * @property string $label
 * @property bool $expanded
 * @property bool $multiple
 * @property bool $grouping
 * @property int $scale
 * @property int $roundingMode
 * @property int $currency
 * @property int $divisor
 * @property bool $alwaysEmpty
 * @property string $percentType
 * @property string $defaultProtocol
 * @property string $min
 * @property string $max
 * @property string $step
 * @property bool $customCountries
 * @property array $countries
 * @property bool $customLanguages
 * @property array $languages
 * @property bool $customLocales
 * @property array $locales
 * @property bool $customValue
 * @property string $value
 * @property string $minTime
 * @property string $maxTime
 * @property string $timeFormat
 * @property string $minDate
 * @property string $maxDate
 * @property string $dateFormat
 * @property string $minDateTime
 * @property string $maxDateTime
 * @property string $dateTimeFormat
 * @property bool $html5
 * @property string $timeWidget
 * @property string $dateWidget
 * @property int $startElement
 * @property int $stopElement
 * @property bool $hideLabel
 * @property bool $inputGroup
 * @property string $inputGroupAppend
 * @property string $inputGroupPrepend
 * @property string $operator
 * @property bool $customOperator
 * @property bool $addDefaultValue
 * @property array $defaultValueArray
 * @property string $defaultValue
 * @property string $defaultValueType
 * @property string $cssClass
 * @property bool $isInitial
 * @property array $initialValueArray
 * @property string $initialValue
 * @property string $initialValueType
 * @property string $startField
 * @property string $stopField
 * @property bool $addStartAndStop
 * @property bool $ignoreFePreview
 * @property bool $invertField
 * @property bool $published
 * @property string $start
 * @property string $stop
 *
 * @method FilterConfigElementModel|null                                                     findById($id, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findByPk($id, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneBy($col, $val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByTstamp($val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByTitle($val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByType($val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByDataContainer($val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByPublished($val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByStart($val, array $opt = [])
 * @method FilterConfigElementModel|null                                                     findOneByStop($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByPid($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByTstamp($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByTitle($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByType($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByDataContainer($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByPublished($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByStart($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findByStop($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findMultipleByIds($val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findBy($col, $val, array $opt = [])
 * @method \Contao\Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null findAll(array $opt = [])
 * @method int                                                                               countById($id, array $opt = [])
 * @method int                                                                               countByTstamp($val, array $opt = [])
 * @method int                                                                               countByType($val, array $opt = [])
 * @method int                                                                               countByTitle($val, array $opt = [])
 * @method int                                                                               countByDataContainer($val, array $opt = [])
 * @method int                                                                               countByPublished($val, array $opt = [])
 * @method int                                                                               countByStart($val, array $opt = [])
 * @method int                                                                               countByStop($val, array $opt = [])
 */
class FilterConfigElementModel extends \Model implements \JsonSerializable
{
    protected static $strTable = 'tl_filter_config_element';

    /**
     * @var string
     */
    protected $formName;

    /**
     * Find published filte elements items by their parent ID.
     *
     * @param int $intId The filter ID
     * @param int $intLimit An optional limit
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null A collection of models or null if there are no filter elements
     */
    public function findPublishedByPid($intId, $intLimit = 0, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.pid=?"];

        if (isset($arrOptions['ignoreFePreview']) || !defined('BE_USER_LOGGED_IN') || !BE_USER_LOGGED_IN) {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.sorting ASC";
        }

        if ($intLimit > 0) {
            $arrOptions['limit'] = $intLimit;
        }

        return static::findBy($arrColumns, $intId, $arrOptions);
    }

    /**
     * Find published filter elements items by their parent ID and optional types.
     *
     * @param int $intId The filter ID
     * @param array $types The list of element types
     * @param int $intLimit An optional limit
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|FilterConfigElementModel[]|FilterConfigElementModel|null A collection of models or null if there are no filter elements
     */
    public function findPublishedByPidAndTypes($intId, array $types = [], $intLimit = 0, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.pid=?"];

        if (isset($arrOptions['ignoreFePreview']) || !defined('BE_USER_LOGGED_IN') || !BE_USER_LOGGED_IN) {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.sorting ASC";
        }

        if ($intLimit > 0) {
            $arrOptions['limit'] = $intLimit;
        }

        if (!empty($types)) {
            $arrColumns[] = \Database::getInstance()->findInSet("$t.type", $types);
        }

        return static::findBy($arrColumns, $intId, $arrOptions);
    }

    /**
     * Set the form element name for the current model.
     *
     * @param string $name
     */
    public function setElementFormName(string $name)
    {
        $this->formName = $name;
    }

    /**
     * @return string|null
     */
    public function getFormName()
    {
        return $this->formName;
    }

    /**
     * Required by HeimrichHannot\UtilsBundle\Choice\AbstractChoice to create custom cache key
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
