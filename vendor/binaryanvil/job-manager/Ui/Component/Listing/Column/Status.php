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

namespace BinaryAnvil\JobManager\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use BinaryAnvil\JobManager\Helper\Config;

class Status extends Column
{
    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $config
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Config $config,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $types = [];
        foreach ($this->config->statusToArray() as $status) {
            $types[] = 'jobmanager-status-' . strtolower($status);
        }

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as & $item) {
            if ($item[$fieldName] == 0 || !empty($item[$fieldName])) {
                $class = $this->getItemClass($item[$fieldName]);
                $item[$fieldName . '_cssclass'] = $class;
                $item[$fieldName . '_typeMap'] = implode(',', $types);
            }
        }

        return $dataSource;
    }

    /**
     * Retrieve item css class
     *
     * @param int $item
     * @return string
     */
    protected function getItemClass($item)
    {
        $config = $this->config;
        $class = '';

        switch ($item) {
            case $config::JOB_STATUS_PENDING:
                $class .= 'jobmanager-status-pending';
                break;
            case $config::JOB_STATUS_RUNNING:
                $class .= 'jobmanager-status-running';
                break;
            case $config::JOB_STATUS_EXECUTED:
                $class .= 'jobmanager-status-executed';
                break;
            case $config::JOB_STATUS_ERROR:
                $class .= 'jobmanager-status-error';
                break;
            default:
                $class .= '';
        }

        return $class;
    }
}
