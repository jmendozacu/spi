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

namespace BinaryAnvil\JobManager\Controller\Adminhtml;

use BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory;
use BinaryAnvil\JobManager\Api\JobRepositoryInterface;
use BinaryAnvil\JobManager\Model\JobFactory;
use BinaryAnvil\JobManager\Model\JobArchiveFactory;
use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\JobManager\Helper\Data;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Logging\Model\Processor;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;

abstract class Job extends Action
{
    /**
     * @var \Magento\Framework\Registry $coreRegistry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Logging\Model\Processor $processor
     */
    protected $processor;

    /**
     * @var \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    protected $resultFactory;

    /**
     * @var \BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \BinaryAnvil\JobManager\Api\JobRepositoryInterface $jobRepository
     */
    protected $jobRepository;

    /**
     * @var \BinaryAnvil\JobManager\Model\JobFactory $jobFactory
     */
    protected $jobFactory;

    /**
     * @var \BinaryAnvil\JobManager\Model\JobArchiveFactory $jobArchiveFactory
     */
    protected $jobArchiveFactory;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $jobConfig
     */
    protected $jobConfig;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Data $jobHelper
     */
    protected $jobHelper;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    /**
     * Abstract Job action constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Logging\Model\Processor $processor
     * @param \BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory $collectionFactory
     * @param \BinaryAnvil\JobManager\Api\JobRepositoryInterface $jobRepository
     * @param \BinaryAnvil\JobManager\Model\JobFactory $jobFactory
     * @param \BinaryAnvil\JobManager\Model\JobArchiveFactory $jobArchiveFactory
     * @param \BinaryAnvil\JobManager\Helper\Config $config
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Backend\App\Action\Context $context
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Registry $registry,
        Processor $processor,
        CollectionFactory $collectionFactory,
        JobRepositoryInterface $jobRepository,
        JobFactory $jobFactory,
        JobArchiveFactory $jobArchiveFactory,
        Config $config,
        Data $helper,
        Filter $filter,
        Context $context
    ) {
        parent::__construct($context);

        $this->coreRegistry = $registry;
        $this->processor = $processor;
        $this->collectionFactory = $collectionFactory;
        $this->jobRepository = $jobRepository;
        $this->jobFactory = $jobFactory;
        $this->jobArchiveFactory = $jobArchiveFactory;
        $this->jobConfig = $config;
        $this->jobHelper = $helper;
        $this->resultFactory = $context->getResultFactory();
        $this->filter = $filter;
    }
}
