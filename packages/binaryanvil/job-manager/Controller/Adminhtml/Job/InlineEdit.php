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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use BinaryAnvil\JobManager\Helper\Config;

class InlineEdit extends Job
{
    /**
     * @var string $jobSaveFailed
     */
    private $jobSaveFailed = Config::MESSAGE_JOB_FAILED_GENERIC;

    /**
     * @var string $jobSaveSuccess
     */
    private $jobSaveSuccess = Config::MESSAGE_JOB_SAVE_GENERIC;

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (empty($this->getRequest()->getPostValue()) || $this->getRequest()->isAjax() !== true) {
            $this->jobHelper->log->critical('Request post value is empty');

            return $this->returnJsonResponce();
        }

        $config = $this->jobConfig;
        $data = $this->prepareData($this->getRequest()->getPostValue());

        try {
            /** @var \BinaryAnvil\JobManager\Api\Data\JobInterface $job */
            $job = $this->jobRepository->get($data[$config::SCHEMA_JOB_FIELD_ID]);

            if (!$job->getId()) {
                return $this->returnJsonResponce();
            }

            foreach ($job->getData() as $key => $value) {
                if (!empty($data[$key]) && $value != $data[$key]) {
                    $job->setData($key, $data[$key]);
                }
            }

            $job->save();

            return $this->returnJsonResponce(false);
        } catch (LocalizedException $e) {
            $this->jobHelper->log->critical($e);

            return $this->returnJsonResponce();
        } catch (\Exception $e) {
            $this->jobHelper->log->critical($e);

            return $this->returnJsonResponce();
        }
    }

    /**
     * Return error
     *
     * @param bool $error
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function returnJsonResponce($error = true)
    {
        $data = ['messages' => [
            $error ? __($this->jobSaveFailed) : __($this->jobSaveSuccess)
        ], 'error' => $error];

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);

        return $resultJson;
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $dataItems) {
                $dataItems['form_key'] = $data['form_key'];
                $data = $dataItems;
            }
        }

        return $data;
    }

    // @codingStandardsIgnoreStart
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('BinaryAnvil_JobManager::binaryanvil_jobmanager');
    }
    // @codingStandardsIgnoreEnd
}
