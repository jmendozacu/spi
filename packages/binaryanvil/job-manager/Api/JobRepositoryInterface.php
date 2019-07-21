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

use BinaryAnvil\JobManager\Api\Data\JobInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface JobRepositoryInterface
{
    /**
     * Save Job
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface| $job
     * @param array $data
     * @return \BinaryAnvil\JobManager\Api\Data\JobInterface
     */
    public function save(JobInterface $job, array $data = []);

    /**
     * Get job info by id
     *
     * @param int $id
     * @return \BinaryAnvil\JobManager\Api\Data\JobInterface
     */
    public function get($id);

    /**
     * Get list of jobs
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BinaryAnvil\JobManager\Api\Data\JobSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete job
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @return bool
     */
    public function delete(JobInterface $job);

    /**
     * Delete job by id
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id);
}
