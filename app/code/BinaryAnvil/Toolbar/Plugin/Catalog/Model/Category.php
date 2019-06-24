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

namespace BinaryAnvil\Toolbar\Plugin\Catalog\Model;

use BinaryAnvil\Toolbar\Model\Sorting;
use Magento\Catalog\Model\Category as OriginalClass;
use BinaryAnvil\Toolbar\Model\Config\Source\Action;
use BinaryAnvil\Toolbar\Model\Config\Source\Insert;

class Category
{
    /**
     * @var \BinaryAnvil\Toolbar\Model\Sorting
     */
    private $toolbarSorting;

    /**
     * Config plugin constructor
     *
     * @param Sorting $toolbarSorting
     */
    public function __construct(Sorting $toolbarSorting)
    {
        $this->toolbarSorting = $toolbarSorting;
    }

    /**
     * Native options extending|overriding
     *
     * @see   \Magento\Catalog\Model\Category::getAvailableSortBy()
     * @param \Magento\Catalog\Model\Category $subject
     * @param  array $options
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAvailableSortBy(OriginalClass $subject, $options)
    {
        if (empty($options)) {
            return $options;
        }

        if ($action = (int) $this->toolbarSorting->getConfig()->getSortByAction()) {
            $availableOptions = $this->toolbarSorting->getAvailableSortArray();

            if ($action == Action::CUSTOM_SORTING_ACTION_EXTEND_CODE) {
                $insertTo = (int) $this->toolbarSorting->getConfig()->getSortByInsertTo();

                if ($insertTo == Insert::NATIVE_SORTING_PLACE_FIRST) {
                    $options = array_merge($options, $availableOptions);
                } elseif ($insertTo == Insert::NATIVE_SORTING_PLACE_END) {
                    $options = array_merge($availableOptions, $options);
                }
            }
        }

        return $options;
    }
}
