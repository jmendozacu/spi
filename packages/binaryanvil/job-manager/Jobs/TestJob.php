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

namespace BinaryAnvil\JobManager\Jobs;

use BinaryAnvil\JobManager\Api\JobRunInterface;
use BinaryAnvil\JobManager\Api\JobRunResultInterface;

class TestJob implements JobRunInterface
{
    /**
     * @var \BinaryAnvil\JobManager\Api\JobRunResultInterface $jobRunResult
     */
    protected $jobRunResult;

    /**
     * @param \BinaryAnvil\JobManager\Api\JobRunResultInterface $jobRunResult
     */
    public function __construct(JobRunResultInterface $jobRunResult)
    {
        $this->jobRunResult = $jobRunResult;
    }

    /**
     * Run job
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @return \BinaryAnvil\JobManager\Api\JobRunResultInterface
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function run($job)
    {
        try {
            sleep(1);
            /** Do some custom logic here */

            /** Set errors array, if any
             $this->jobRunResult->setErrors([
                 'Something wrong with this job run',
                 'Another error for this test job'
             ]);
             */

            /** Set request array, if any
            $this->jobRunResult->setRequest([$request]);
             */

            /** Set response array, if any
            $this->jobRunResult->setResponse([$response]);
             */

            /** Set notice array, if any
            $this->jobRunResult->setNotice([
                'Some notice goes here!'
            ]);
             */

            /** Set success string, if any
            $this->jobRunResult->setSuccess('Job executed successfully.');
             */

            /** Set result status to reflect the job run result */
            $this->jobRunResult->setDone(true);

            return $this->jobRunResult;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
