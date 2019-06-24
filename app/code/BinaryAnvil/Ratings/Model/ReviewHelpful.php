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

namespace BinaryAnvil\Ratings\Model;

use BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface;
use Magento\Framework\Model\AbstractModel;
use BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful as ResourceModel;

class ReviewHelpful extends AbstractModel implements ReviewHelpfulInterface
{
    /**
     * {@inheritdoc}
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(ResourceModel::class);
        $this->setIdFieldName(self::HELPFUL_ID);
    }
    /**
     * @codingStandardsIgnoreEnd
     */

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::HELPFUL_ID);
    }

    /**
     * @param int $helpfulId
     * @return $this
     */
    public function setId($helpfulId)
    {
        $this->setData(self::HELPFUL_ID, $helpfulId);
        return $this;
    }

    /**
     * @return int
     */
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }

    /**
     * @param int $reviewId
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        $this->setData(self::REVIEW_ID, $reviewId);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId = null)
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
        return $this;
    }

    /**
     * @return bool
     */
    public function isHelpful()
    {
        return $this->getData(self::IS_HELPFUL);
    }

    /**
     * @param bool $isHelpful
     * @return $this
     */
    public function setIsHelpful($isHelpful)
    {
        $this->setData(self::IS_HELPFUL, $isHelpful);
        return $this;
    }
}
