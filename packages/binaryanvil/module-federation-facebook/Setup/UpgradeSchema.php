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
 * @package     BinaryAnvil_FederationFacebook
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\FederationFacebook\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use BinaryAnvil\Federation\Model\SocialFeedFactory;
use BinaryAnvil\FederationFacebook\Block\SocialFeed;
use BinaryAnvil\Federation\Model\SocialFeedRepository;
use BinaryAnvil\Federation\Api\Data\SocialFeedInterface;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeed as Resource;

/**
 * Class UpgradeSchema
 * @package BinaryAnvil\FederationFacebook\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \BinaryAnvil\Federation\Model\ResourceModel\SocialFeed
     */
    private $resource;

    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedFactory
     */
    private $socialFeedFactory;

    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedRepository
     */
    private $socialFeedRepository;

    /**
     * UpgradeSchema constructor
     *
     * @param \BinaryAnvil\Federation\Model\SocialFeedFactory $socialFeedFactory
     * @param \BinaryAnvil\Federation\Model\SocialFeedRepository $socialFeedRepository
     * @param \BinaryAnvil\Federation\Model\ResourceModel\SocialFeed | Resource  $resource
     */
    public function __construct(
        Resource $resource,
        SocialFeedFactory $socialFeedFactory,
        SocialFeedRepository $socialFeedRepository
    ) {
        $this->resource = $resource;
        $this->socialFeedFactory = $socialFeedFactory;
        $this->socialFeedRepository = $socialFeedRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addSocialFeedInstance();
        }

        $setup->endSetup();
    }

    /**
     * Add new social feed instance
     *
     * @return void
     */
    private function addSocialFeedInstance()
    {
        $socialFeed = $this->socialFeedFactory->create();
        $this->resource->load($socialFeed, SocialFeed::SOCIAL_FEED_KEY, SocialFeedInterface::KEY);

        if (!$socialFeed->getId()) {
            $socialFeed->setData([
                SocialFeedInterface::BLOCK_CLASS    => SocialFeed::class,
                SocialFeedInterface::KEY            => SocialFeed::SOCIAL_FEED_KEY,
                SocialFeedInterface::PACKAGE        => SocialFeed::extractModuleName(SocialFeed::class)
            ]);

            $this->socialFeedRepository->save($socialFeed);
        }
    }
}
