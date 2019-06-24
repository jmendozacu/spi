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

namespace BinaryAnvil\Ratings\Api\Data;

interface ReviewHelpfulInterface
{
    const HELPFUL_ID = 'helpful_id';
    const REVIEW_ID = 'review_id';
    const CUSTOMER_ID = 'customer_id';
    const IS_HELPFUL = 'is_helpful';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $helpfulId
     * @return $this
     */
    public function setId($helpfulId);

    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId = null);

    /**
     * @return bool
     */
    public function isHelpful();

    /**
     * @param bool $isHelpful
     * @return $this
     */
    public function setIsHelpful($isHelpful);
}
