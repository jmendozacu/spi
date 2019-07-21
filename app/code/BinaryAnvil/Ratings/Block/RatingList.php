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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Block\Product\View\ListView;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use BinaryAnvil\Ratings\Helper\Data as DataHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RatingList extends ListView
{
    /**
     * @var \BinaryAnvil\Ratings\Helper\Data
     */
    protected $dataHelper;

    /**
     * RatingList constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param \BinaryAnvil\Ratings\Helper\Data $dataHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        ProductHelper $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        ReviewCollectionFactory $collectionFactory,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $collectionFactory,
            $data
        );
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStarsCountList()
    {
        $ratingCollection = $this->getRatingCollection(1)->getItems();
        $values = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($this->_reviewsCollection as $review) {
            foreach ($review->getRatingVotes() as $vote) {
                if (array_key_exists($vote->getRatingId(), $ratingCollection)) {
                    ++$values[$vote->getValue()];
                }
            }
        }

        $values['summary'] = array_sum($values);

        return $values;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSplitSummaryRating()
    {
        $ratingCollection = $this->getRatingCollection(0);

        $values = [];

        foreach ($this->_reviewsCollection as $review) {
            foreach ($review->getRatingVotes() as $vote) {
                $values[$vote->getRatingId()][] = $vote->getValue();
            }
        }

        foreach ($ratingCollection as $rating) {
            if (array_key_exists($rating->getId(), $values)) {
                $rating->setValue(array_sum($values[$rating->getId()]) / count($values[$rating->getId()]));
            }
            if (!$rating->getLabelMax() && $rating->getLabelPerfect()) {
                $rating->setLabelMax($rating->getLabelPerfect())->setLabelPerfect('');
            }
        }

        return $ratingCollection;
    }

    /**
     * @return int
     */
    public function getRecommendPercent()
    {
        $recommendCount = 0;
        foreach ($this->_reviewsCollection as $review) {
            if ($review->isRecommendProduct()) {
                $recommendCount ++;
            }
        }
        return $this->_reviewsCollection->getSize() ?
            round($recommendCount / $this->_reviewsCollection->getSize() * 100) : 0;
    }

    /**
     * @param int $isUsedInSummary
     * @return \Magento\Review\Model\ResourceModel\Rating\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRatingCollection($isUsedInSummary)
    {
        return $this->dataHelper->getRatingCollection($isUsedInSummary);
    }

    /**
     * Check reviews statistics count
     *
     * @return int|false
     */
    public function checkCountReviews()
    {
        return count($this->getReviewsCollection()->getItems());
    }
}
