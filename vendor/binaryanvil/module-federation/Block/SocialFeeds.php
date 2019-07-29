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

namespace BinaryAnvil\Federation\Block;

use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use BinaryAnvil\Federation\Model\SocialFeedRepository;

/**
 * Class SocialFeeds
 * @package BinaryAnvil\Federation\Block
 */
class SocialFeeds extends Template
{
    /**
     * Default feed qty
     */
    const DEFAULT_FEEDS_LIMIT = '4';

    /**
     * Default CSS class name
     */
    const DEFAULT_FEEDS_CONTAINER_CSS_CLASS = 'social-feed-container';

    /**
     * @var string CSS class name
     */
    protected $cssClass;

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
     * SocialFeeds constructor
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
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $feedInstanceList = $this->feedInstanceRepository->getList($searchCriteria);

        foreach ($feedInstanceList->getItems() as $feedInstance) {
            /** @var $feedInstance \BinaryAnvil\Federation\Model\SocialFeed */
            if ($this->moduleManager->isEnabled($feedInstance->getPackage())) {
                $this->addChild($feedInstance->getKey(), $feedInstance->getBlockClass());
            }
        }

        return $this;
    }
    /**
     * @codingStandardsIgnoreEnd
     */

    /**
     * Set class name for
     * main container
     *
     * @param $class
     * @return void
     */
    public function setCssClass($class)
    {
        $this->cssClass = $class;
    }

    /**
     * Retrieve CSS class name
     * for main container
     *
     * @return string
     */
    public function getCssClass()
    {
        if ($this->cssClass) {
            return $this->cssClass;
        }

        return self::DEFAULT_FEEDS_CONTAINER_CSS_CLASS;
    }
}
