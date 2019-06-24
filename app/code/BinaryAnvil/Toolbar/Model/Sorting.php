<?php
/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     BinaryAnvil_Toolbar
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Toolbar\Model;

use BinaryAnvil\Toolbar\Helper\Config;
use BinaryAnvil\Toolbar\Model\Sorting\Handler\SortOrder;
use BinaryAnvil\Toolbar\Model\Config\Source\SortBy as SortBySource;

class Sorting
{
    /**
     * @var \BinaryAnvil\Toolbar\Helper\Config
     */
    private $config;

    /**
     * @var \BinaryAnvil\Toolbar\Model\Config\Source\SortBy
     */
    private $sortBySource;

    /**
     * Custom sorting options
     *
     * @see \BinaryAnvil\Toolbar\Model\Config\Source\SortBy::toArray()
     * @var array
     */
    private $sortByOptionsArr;

    /**
     * @var \BinaryAnvil\Toolbar\Model\Sorting\Handler\SortOrder
     */
    private $defaultCollectionHandler;

    /**
     * @var \BinaryAnvil\Toolbar\Model\Sorting\HandlerInterface[]
     */
    private $customCollectionHandlers;

    /**
     * Sorting constructor
     *
     * @param \BinaryAnvil\Toolbar\Helper\Config  $config
     * @param \BinaryAnvil\Toolbar\Model\Config\Source\SortBy  $sortBySource
     * @param \BinaryAnvil\Toolbar\Model\Sorting\Handler\SortOrder $defaultCollectionHandler
     * @param \BinaryAnvil\Toolbar\Model\Sorting\HandlerInterface[] $customCollectionHandlers
     */
    public function __construct(
        Config $config,
        SortBySource $sortBySource,
        SortOrder $defaultCollectionHandler,
        array $customCollectionHandlers = []
    ) {
        $this->config = $config;
        $this->sortBySource = $sortBySource;
        $this->defaultCollectionHandler = $defaultCollectionHandler;
        $this->customCollectionHandlers = $customCollectionHandlers;
    }

    /**
     * Retrieve configurations helper
     *
     * @return \BinaryAnvil\Toolbar\Helper\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Retrieve SortBy options (all values)
     *
     * @return array|mixed
     */
    public function getSortByOptionsArr()
    {
        if ($this->sortByOptionsArr == null) {
            $this->sortByOptionsArr = $this->sortBySource->toArray();
        }

        return $this->sortByOptionsArr;
    }

    /**
     * Set new value to sorting option array
     *
     * @param string $option
     * @param string $label
     * @return \BinaryAnvil\Toolbar\Model\Sorting
     */
    public function extendSortByOption($option, $label)
    {
        $this->sortByOptionsArr[$option] = __($label);

        return $this;
    }

    /**
     * Retrieve available sorting options
     *
     * @return array
     */
    public function getAvailableSortByOptions()
    {
        $availableOptions = [];
        $configValueArr = $this->getAvailableSortArray();

        if (!empty($configValueArr)) {
            $sortByOptionsArr = $this->getSortByOptionsArr();

            foreach ($configValueArr as $value) {
                $availableOptions[$value] = $sortByOptionsArr[$value];
            }
        }

        return $availableOptions;
    }

    /**
     * Retrieve available sort option list
     *
     * @return array
     */
    public function getAvailableSortArray()
    {
        $configVal = $this->getConfig()->getSortByOptions();

        return $configVal ? explode(',', $configVal) : [];
    }

    /**
     * Run handler for sort option
     *
     * @param  string $sortOption
     * @param  \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function processProductCollection($sortOption, $collection)
    {
        $handler = $this->defaultCollectionHandler;

        if (isset($this->customCollectionHandlers[$sortOption])) {
            $handler = $this->customCollectionHandlers[$sortOption];
        }

        return $handler->process($sortOption, $collection);
    }
}
