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
 * @package     SizeChart
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\SizeChart\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Category\Attribute\Backend\Image;
use BinaryAnvil\SizeChart\Helper\Image as ImageHelper;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * InstallData constructor.
     *
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup = $this->categorySetupFactory->create(['setup' => $setup]);
        $setup->addAttribute(
            Category::ENTITY,
            ImageHelper::SIZE_CHART_CATEGORY_IMAGE_ATTRIBUTE,
            [
                'type' => 'varchar',
                'label' => 'Size Chart',
                'input' => 'image',
                'backend' => Image::class,
                'required' => false,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
    }
}
