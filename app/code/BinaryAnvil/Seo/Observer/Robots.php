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
 * @package     BinaryAnvil_Seo
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Seo\Observer;

use Magento\Framework\UrlInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Robots implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $layoutFactory;

    /**
     * Robots constructor.
     *
     * @param UrlInterface $urlInterface
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\View\Page\Config $layoutFactory
     */
    public function __construct(
        UrlInterface $urlInterface,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Page\Config $layoutFactory
    ) {
        $this->urlInterface = $urlInterface;
        $this->request = $request;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @return array
     */
    protected function getActionNameToValidate()
    {
        $result = ['catalog_category_view', 'catalog_product_view', 'catalogsearch_result_index'];
        return $result;
    }

    /**
     * @param Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $fullActionName = $this->request->getFullActionName();

        if (in_array($fullActionName, $this->getActionNameToValidate())) {
            $url = $this->urlInterface->getCurrentUrl();
            $query = parse_url($url, PHP_URL_QUERY);

            if ($query) {
                $this->layoutFactory->setRobots('NOINDEX,FOLLOW');
            }
        }
    }
}
