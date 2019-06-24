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

class Archive extends Job
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Jobs Archive'), __('Jobs Archive'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Jobs Archive'));
        $this->_view->renderLayout('root');
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
