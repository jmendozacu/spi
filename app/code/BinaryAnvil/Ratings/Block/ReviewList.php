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
 * @package     BinaryAnvil_Ratings
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Ratings\Block;

use Magento\Review\Block\Product\View\ListView;

class ReviewList extends ListView
{
    const DEFAULT_TEMPLATE = "Magento_Review::product/view/list/items.phtml";

    /**
     * @var int
     */
    protected $pageSize = 3;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return parent::getCustomerId();
    }

    /**
     * @return array
     */
    public function getCustomerHelpfulVotes()
    {
        return $this->customerSession->getData('helpful_votes');
    }

    /**
     * Get collection of reviews
     *
     * @param int $limit
     * @param int $page
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function getPageReviewsCollection()
    {
        $reviewCollection = parent::getReviewsCollection();
        $reviewCollection->clear()->setPageSize($this->pageSize)->setCurPage($this->currentPage)->load();

        return $reviewCollection;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if ($this->_template) {
            return $this->_template;
        }

        return self::DEFAULT_TEMPLATE;
    }

    /**
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }
}
