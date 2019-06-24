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
namespace BinaryAnvil\LayeredNavigation\Plugin\Magento\LayeredNavigation\Block\Navigation;

use Magento\Framework\View\LayoutInterface;
use Magento\Swatches\Helper\Data;
use Magento\LayeredNavigation\Block\Navigation\FilterRenderer as OriginClass;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterRenderer
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Path to RenderLayered Block
     *
     * @var string
     */
    protected $block = \BinaryAnvil\LayeredNavigation\Block\LayeredNavigation\RenderLayered::class;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * FilterRenderer constructor.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     */
    public function __construct(
        LayoutInterface $layout,
        Data $swatchHelper
    ) {
        $this->layout = $layout;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        OriginClass $subject,
        \Closure $proceed,
        FilterInterface $filter
    ) {
        if ($filter->hasAttributeModel()) {
            if ($this->swatchHelper->isSwatchAttribute($filter->getAttributeModel())) {
                return $this->layout
                    ->createBlock($this->block)
                    ->setSwatchFilter($filter)
                    ->toHtml();
            }
        }
        return $proceed($filter);
    }
}
