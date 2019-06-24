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

namespace BinaryAnvil\JobManager\Api\Data;

interface JobArchiveInterface
{
    /**
     * Job id
     *
     * @return int | null
     */
    public function getId();

    /**
     * Get job type
     *
     * @return string
     */
    public function getType();

    /**
     * Set job type
     *
     * @param string $type
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setType($type);

    /**
     * Get job priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Set job priority
     *
     * @param int $priority
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setPriority($priority);

    /**
     * Get job status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set job status
     *
     * @param int $status
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setStatus($status);

    /**
     * Get job details
     *
     * @return array
     */
    public function getDetails();

    /**
     * Set job details
     *
     * @param string $details
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setDetails($details = '');

    /**
     * Retrieve job attempts
     *
     * @return int
     */
    public function getAttempt();

    /**
     * Set job attempt
     *
     * @param int $attempt
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setAttempt($attempt = 1);

    /**
     * Retrieve job created at
     *
     * @return string
     */
    public function getCreated();

    /**
     * Set job created at time
     *
     * @param string $datetime
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setCreated($datetime = '');

    /**
     * Retrieve job executed time
     *
     * @return string
     */
    public function getExecuted();

    /**
     * Set job executed time
     *
     * @param string $datetime
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setExecuted($datetime = '');

    /**
     * Retrieve job last attempt time
     *
     * @return string
     */
    public function getLastAttempt();

    /**
     * Set job last attempt time
     *
     * @param string $datetime
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setLastAttempt($datetime = '');

    /**
     * Get job last error(s)
     *
     * @return array
     */
    public function getLastError();

    /**
     * Set job last error(s)
     *
     * @param string $errors
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setLastError($errors = '');

    /**
     * Get job source
     *
     * @return string
     */
    public function getSource();

    /**
     * Set job source
     *
     * @param string $source
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setSource($source);

    /**
     * Get job schedule
     *
     * @return string
     */
    public function getSchedule();

    /**
     * Set job schedule
     *
     * @param string|int $datetime
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setSchedule($datetime);

    /**
     * Set job data
     * @param string $key
     * @param int|string|array|null $value
     * @return \BinaryAnvil\JobManager\Api\Data\JobArchiveInterface
     */
    public function setData($key, $value = null);

    /**
     * Get job data
     *
     * @param string $key
     * @param string|int $index
     * @return array
     */
    public function getData($key = '', $index = null);
}
