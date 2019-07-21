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

class MassUpdate extends Job
{
    /**
     * @var string|null $currentParam
     */
    private $currentParam = null;

    /**
     * @var string|null $currentValue
     */
    private $currentValue = null;

    /**
     * @var int $jobsCount
     */
    private $jobsCount = 0;

    /**
     * @var array $allowedParams
     */
    private $allowedParams = ['priority', 'status'];

    /**
     * @var string $_jobsDeleteCount
     */
    private $jobsUpdateCount = Config::MESSAGE_JOB_UPDATE_COUNT;

    /**
     * @var string $_jobDeleteSuccess
     */
    private $jobUpdateSuccess = Config::MESSAGE_JOB_UPDATE_SUCCESS;

    /**
     * @var string $_jobDeleteFailed
     */
    private $jobUpdateFailed = Config::MESSAGE_JOB_UPDATE_FAILED;

    /**
     * Mass delete jobs action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $this->initParams();

        if ($this->currentParam && $this->currentValue) {
            $labels = $this->getParamLabels($this->currentParam);
            $label = !empty($labels[$this->currentValue]) ? $labels[$this->currentValue] : $this->currentValue;

            /** @var \BinaryAnvil\JobManager\Api\Data\JobInterface $job */
            foreach ($collection->getItems() as $job) {
                try {
                    $job->setData($this->currentParam, $this->currentValue);
                    $job->save();

                    $this->jobsCount++;

                    $this->jobHelper->log->success(
                        __($this->jobUpdateSuccess, [
                            'type' => $job->getType(),
                            'id' => $job->getId(),
                            'param' => $this->currentParam,
                            'value' => $label,
                        ])
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __($this->jobUpdateFailed)
                    );

                    $this->jobHelper->log->critical(
                        __($this->jobUpdateFailed)
                    );

                    $this->jobHelper->log->critical($e);
                }
            }

            if ($this->jobsCount > 0) {
                $this->messageManager->addSuccessMessage(
                    __($this->jobsUpdateCount, [
                        'param' => $this->currentParam,
                        'value' => $label,
                        'count' => $this->jobsCount,
                    ])
                );
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('binaryanvil_jobmanager/*/');
    }

    // @codingStandardsIgnoreStart
    /**
     * Initialize current parameters
     *
     * @return void
     */
    protected function initParams()
    {
        foreach ($this->allowedParams as $action) {
            if (!empty($this->getRequest()->getParam($action))) {
                $this->currentParam = $action;
                $this->currentValue = $this->getRequest()->getParam($action);
            }
        }
    }
    // @codingStandardsIgnoreEnd

    /**
     * Retrieve param labels
     *
     * @param string $action
     * @return array
     */
    protected function getParamLabels($action)
    {
        $method = $action . 'ToArray';
        $paramLabels = $this->jobConfig->{$method}();

        if (!empty($paramLabels)) {
            return $paramLabels;
        }

        return [];
    }
}
