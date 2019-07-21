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
use BinaryAnvil\JobManager\Helper\Config;

class Delete extends Job
{
    /**
     * @var string $jobDeleteSuccess
     */
    private $jobDeleteSuccess = Config::MESSAGE_JOB_DELETE_SUCCESS;

    /**
     * @var string $jobDeleteFailed
     */
    private $jobDeleteFailed = Config::MESSAGE_JOB_DELETE_FAILED;

    /**
     * Delete job action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                /** @var \BinaryAnvil\JobManager\Model\Job $model */
                $model = $this->jobFactory->create();
                $model->load($id);
                $model->delete();

                $this->messageManager->addSuccessMessage(
                    __($this->jobDeleteSuccess, ['type' => $model->getType(), 'id' => $model->getId()])
                );
            } catch (LocalizedException $e) {
                $this->jobHelper->log->critical($e);
                $this->messageManager->addErrorMessage(__($this->jobDeleteFailed));
            } catch (\Exception $e) {
                $this->jobHelper->log->critical($e);
                $this->messageManager->addErrorMessage(__($this->jobDeleteFailed));
            }
        }

        return $this->_redirect('binaryanvil_jobmanager/*');
    }
}
