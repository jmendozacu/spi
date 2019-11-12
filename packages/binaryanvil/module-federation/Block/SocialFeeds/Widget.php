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
 * @package     BinaryAnvil_Federation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Federation\Block\SocialFeeds;

use Magento\Framework\Module\Manager;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use BinaryAnvil\Federation\Block\SocialFeeds;
use Magento\Framework\Api\SearchCriteriaBuilder;
use BinaryAnvil\Federation\Model\SocialFeedRepository;

/**
 * Class Widget
 * @package BinaryAnvil\Federation\Block\SocialFeeds
 *
 * Social Feeds Widget block
 */
class Widget extends Template implements BlockInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedRepository
     */
    protected $feedInstanceRepository;

    /**
     * @codingStandardsIgnoreStart
     */
    /**
     * @var string Default template
     */
    protected $_template = "social-feeds.phtml";
    /**
     * @codingStandardsIgnoreEnd
     */

    /**
     * Social Feeds Widget constructor
     *
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \BinaryAnvil\Federation\Model\SocialFeedRepository $feedInstanceRepository
     * @param array $data
     */
    public function __construct(
        Manager $moduleManager,
        Template\Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SocialFeedRepository $feedInstanceRepository,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->feedInstanceRepository = $feedInstanceRepository;

        parent::__construct($context, $data);
    }

    /**
     * @codingStandardsIgnoreStart
     */
    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if (!$this->hasData('feed_instance_list')) {
            $this->setData('feed_instance_list', '');
        }

        $selectedFeeds = explode(',', $this->getData('feed_instance_list'));
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $feedInstanceList = $this->feedInstanceRepository->getList($searchCriteria);

        foreach ($feedInstanceList->getItems() as $feedInstance) {
            /** @var $feedInstance \BinaryAnvil\Federation\Model\SocialFeed */
            if (
                in_array($feedInstance->getKey(), $selectedFeeds)
                && $this->moduleManager->isEnabled($feedInstance->getPackage())
            ) {
                $this->addChild($feedInstance->getKey(), $feedInstance->getBlockClass());
            }
        }

        return $this;
    }
    /**
     * @codingStandardsIgnoreEnd
     */

    /**
     * Retrieve CSS class name
     * for main container
     *
     * @return string
     */
    public function getCssClass()
    {
        if ($this->hasData('container_class')) {
            return $this->getData('container_class');
        }

        return SocialFeeds::DEFAULT_FEEDS_CONTAINER_CSS_CLASS;
    }
}
