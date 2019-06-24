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
 * @package     LayeredNavigation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\LayeredNavigation\Model\Url;

use Magento\Framework\Url;

class Builder extends Url
{
    /**
     * Get filter url for current attribute
     *
     * @param $code
     * @param $value
     * @param array $query
     * @return string
     */
    public function getFilterUrl($code, $value, $query = [])
    {
        $params = ['_current' => true, '_use_rewrite' => true, '_query' => $query];
        $values = array_unique(
            array_merge(
                $this->getValuesFromUrl($code),
                [$value]
            )
        );
        $params['_query'][$code] = implode(',', $values);
        return urldecode($this->getUrl('*/*/*', $params));
    }

    /**
     * Get remove filter url for current url
     *
     * @param $code
     * @param $value
     * @param array $query
     * @return string
     */
    public function getRemoveFilterUrl($code, $value, $query = [])
    {
        $params = ['_current' => true, '_use_rewrite' => true, '_query' => $query, '_escape' => true];
        $values = $this->getValuesFromUrl($code);
        $key = array_search($value, $values);
        unset($values[$key]);
        $params['_query'][$code] = $values ? implode(',', $values) : null;
        return urldecode($this->getUrl('*/*/*', $params));
    }

    /**
     * Get all values from url by attribute code
     *
     * @param $code
     * @return array
     */
    public function getValuesFromUrl($code)
    {
        return array_filter(explode(',', $this->_getRequest()->getParam($code)));
    }
}
