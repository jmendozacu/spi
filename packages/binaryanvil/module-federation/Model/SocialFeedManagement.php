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

namespace BinaryAnvil\Federation\Model;

use BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface;
use BinaryAnvil\Federation\Api\SocialFeedItemRepositoryInterface;
use BinaryAnvil\Federation\Api\SocialFeedManagementInterface;
use BinaryAnvil\Federation\Api\SocialFeedRepositoryInterface;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem as SocialFeedItemResource;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class SocialFeedManagement
 */
class SocialFeedManagement implements SocialFeedManagementInterface
{
    /**
     * @var \BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem
     */
    protected $socialFeedItemResource;

    /**
     * @var \BinaryAnvil\Federation\Api\SocialFeedRepositoryInterface
     */
    protected $socialFeedRepository;

    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedItemFactory
     */
    protected $socialFeedItemFactory;

    /**
     * @var \BinaryAnvil\Federation\Api\SocialFeedItemRepositoryInterface
     */
    protected $socialFeedItemRepository;

    /**
     * @var \BinaryAnvil\Federation\Model\FlagFactory
     */
    protected $flagFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * SocialFeedManagement constructor.
     * @param \BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem $socialFeedItemResource
     * @param \BinaryAnvil\Federation\Api\SocialFeedRepositoryInterface $socialFeedRepository
     * @param \BinaryAnvil\Federation\Model\SocialFeedItemFactory $socialFeedItemFactory
     * @param \BinaryAnvil\Federation\Api\SocialFeedItemRepositoryInterface $socialFeedItemRepository
     * @param \BinaryAnvil\Federation\Model\FlagFactory $flagFactory
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     */
    public function __construct(
        SocialFeedItemResource $socialFeedItemResource,
        SocialFeedRepositoryInterface $socialFeedRepository,
        SocialFeedItemFactory $socialFeedItemFactory,
        SocialFeedItemRepositoryInterface $socialFeedItemRepository,
        FlagFactory $flagFactory,
        SearchCriteriaInterface $searchCriteria,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        $this->socialFeedItemResource = $socialFeedItemResource;
        $this->socialFeedRepository = $socialFeedRepository;
        $this->socialFeedItemFactory = $socialFeedItemFactory;
        $this->socialFeedItemRepository = $socialFeedItemRepository;
        $this->flagFactory = $flagFactory;
        $this->searchCriteria = $searchCriteria;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshItems($socialFeedKey, $itemsData)
    {
        $this->removePostsByKey($socialFeedKey);
        $social = $this->socialFeedRepository->get($socialFeedKey);
        $count = 0;
        foreach ($itemsData as $data) {
            $data[SocialFeedItemInterface::INSTANCE_ID] = $social->getEntityId();
            $socialFeedItem = $this->socialFeedItemFactory->create();
            $socialFeedItem->setData($data);
            $this->socialFeedItemResource->save($socialFeedItem);
            $count++;
        }
        $flag = $this->flagFactory->create();
        $flag->setSocialCacheFlagCode(Flag::FLAG_PREFIX_CACHE_SOCIAL_FEED_INSTANCE . $socialFeedKey)
            ->loadSelf()
            ->setFlagData(json_encode(['posts_count' => $count, 'time' => time()]))
            ->save();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostsByKey($socialFeedKey)
    {
        $data = [];
        $social = $this->socialFeedRepository->get($socialFeedKey);
        $filter = $this->filterBuilder
            ->setField(SocialFeedItemInterface::INSTANCE_ID)
            ->setConditionType('eq')
            ->setValue($social->getEntityId())
            ->create();
        $filterGroup = $this->filterGroupBuilder
            ->addFilter($filter)
            ->create();
        $searchCriteria = $this->searchCriteria->setFilterGroups([$filterGroup]);
        $socialFeed = $this->socialFeedItemRepository->getList($searchCriteria);
        foreach ($socialFeed->getItems() as $socialFeedItem) {
            $data[] = $socialFeedItem->getData();
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function removePostsByKey($socialFeedKey)
    {
        $social = $this->socialFeedRepository->get($socialFeedKey);
        $condition = [SocialFeedItemInterface::INSTANCE_ID . ' = ?' => (int) $social->getEntityId()];
        $this->socialFeedItemResource->getConnection()->delete(
            SocialFeedItemResource::DB_SCHEMA_TABLE_ENTITY_NAME,
            $condition
        );
        return true;
    }
}
