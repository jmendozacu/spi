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

namespace BinaryAnvil\SizeChart\Plugin\Magento\Catalog\Model\Category;

use BinaryAnvil\SizeChart\Helper\Image;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;

class DataProvider
{
    /**
     * @var \BinaryAnvil\SizeChart\Helper\Image $helper
     */
    protected $helper;
    
    /**
     * DataProvider constructor
     *
     * @param \BinaryAnvil\SizeChart\Helper\Image $image
     */
    public function __construct(Image $image)
    {
        $this->helper = $image;
    }

    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return array
     */
    public function afterGetData(CategoryDataProvider $subject, $result)
    {
        $category = $subject->getCurrentCategory();
        $categoryData = $result[$category->getId()];
        $imageAttribute = Image::SIZE_CHART_CATEGORY_IMAGE_ATTRIBUTE;

        if (isset($categoryData) && $category->getData($imageAttribute)) {
            unset($categoryData[$imageAttribute]);
            $categoryData[$imageAttribute][0]['name'] = $category->getData($imageAttribute);
            $categoryData[$imageAttribute][0]['url'] = $this->helper->getImageUrl($category->getData($imageAttribute));
        }

        $result[$category->getId()] = $categoryData;

        return $result;
    }
}
