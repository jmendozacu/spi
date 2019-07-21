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
 * @package     JobManager
 * @copyright   Copyright (c) 2016-present Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\JobManager\Api;

use BinaryAnvil\JobManager\Api\Data\JobHistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface JobHistoryRepositoryInterface
{
    /**
     * Save job history
     *
     * @param array $data
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(array $data = []);

    /**
     * Save job history array
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveMultiple(array $data = []);

    /**
     * Get job history by id
     *
     * @param int|null $id
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Get job history by id
     *
     * @param int $id
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Get list of job history
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete job history
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface $jobHistory
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(JobHistoryInterface $jobHistory);

    /**
     * Delete job history by id
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($id);
}
