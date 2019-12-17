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
 * @category BinaryAnvil
 * @package SizeChart
 * @copyright Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\SizeChart\Plugin\Magento\Catalog\Controller\Adminhtml\Category;

use Magento\Catalog\Controller\Adminhtml\Category\Save as CategorySave;
use BinaryAnvil\SizeChart\Helper\Image;

class Save
{
    /**
     * @var string
     */
    protected $imageAttribute = Image::SIZE_CHART_CATEGORY_IMAGE_ATTRIBUTE;

    /**
     * Image data preprocessing
     *
     * @param \Magento\Catalog\Controller\Adminhtml\Category\Save $subject
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImagePreprocessing(CategorySave $subject, $data)
    {
        $image = $data[$this->imageAttribute];
        if (isset($image) && is_array($image)) {
            if (!empty($image['delete'])) {
                $data[$this->imageAttribute] = null;
            } else {
                if (isset($image[0]['name']) && isset($image[0]['tmp_name'])) {
                    $data[$this->imageAttribute] = $image[0]['name'];
                } else {
                    unset($data[$this->imageAttribute]);
                }
            }
        } else {
            $data[$this->imageAttribute] = null;
        }

        return $data;
    }
}
