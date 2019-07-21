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

namespace BinaryAnvil\JobManager\Controller\Adminhtml\Job;

use BinaryAnvil\JobManager\Controller\Adminhtml\Job;
use Magento\Framework\Controller\ResultFactory;
use BinaryAnvil\JobManager\Helper\Config;

class MassDelete extends Job
{
    /**
     * @var string $jobsDeleteCount
     */
    private $jobsDeleteCount = Config::MESSAGE_JOB_DELETE_COUNT;

    /**
     * @var string $jobDeleteSuccess
     */
    private $jobDeleteSuccess = Config::MESSAGE_JOB_DELETE_SUCCESS;

    /**
     * @var string $jobDeleteFailed
     */
    private $jobDeleteFailed = Config::MESSAGE_JOB_DELETE_FAILED;

    /**
     * Mass delete jobs action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $jobsCount = 0;

        /** @var \BinaryAnvil\JobManager\Api\Data\JobInterface $job */
        foreach ($collection->getItems() as $job) {
            try {
                $job->delete();
                $jobsCount++;

                $this->jobHelper->log->success(
                    __($this->jobDeleteSuccess, ['type' => $job->getType(), 'id' => $job->getId()])
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __($this->jobDeleteFailed)
                );

                $this->jobHelper->log->critical(
                    __($this->jobDeleteFailed)
                );

                $this->jobHelper->log->critical($e);
            }
        }

        if ($jobsCount) {
            $this->messageManager->addSuccessMessage(
                __($this->jobsDeleteCount, ['count' => $jobsCount])
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('binaryanvil_jobmanager/*/');
    }
}
