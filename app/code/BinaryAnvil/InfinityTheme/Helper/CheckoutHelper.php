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
 * @package     BinaryAnvil_InfinityTheme
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\InfinityTheme\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Helper\Image;
use Magento\Checkout\Model\Session;

class CheckoutHelper extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    protected $priceHelper;

    /**
     * @var \Magento\Catalog\Helper\Image $imageHelper
     */
    protected $imageHelper;

    /**
     * @var \Magento\Checkout\Model\Session $session
     */
    protected $session;

    /**
     * CheckoutHelper constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        Context $context,
        PriceHelper $priceHelper,
        Image $imageHelper,
        Session $session
    ) {
        parent::__construct($context);
        $this->priceHelper = $priceHelper;
        $this->imageHelper = $imageHelper;
        $this->session = $session;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->session->getLastRealOrder();
    }

    /**
     * @param $price
     *
     * @return float|string
     */
    public function getFormatedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @param $product
     *
     * @return string
     */
    public function getProductImage($product)
    {
        return $this->imageHelper->init($product, 'product_page_image_small')->getUrl();
    }
}
