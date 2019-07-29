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

namespace BinaryAnvil\JobManager\Ui\Component\MassAction\Priority;

use Zend\Stdlib\JsonSerializable;
use Magento\Framework\UrlInterface;
use BinaryAnvil\JobManager\Helper\Config;

class Options implements JsonSerializable
{
    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $config
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface $urlBuilder
     */
    protected $urlBuilder;

    /**
     * @var array $data
     */
    protected $data;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * @var string $urlPath
     */
    protected $urlPath;

    /**
     * @var string $paramName
     */
    protected $paramName;

    /**
     * @var array $additionalData
     */
    protected $additionalData = [];

    /**
     * Options massaction constructor
     *
     * @param \BinaryAnvil\JobManager\Helper\Config $config
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Config $config,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->data = $data;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $priorities = $this->config->priorityToArray();
            $this->prepareData();

            foreach ($priorities as $key => $label) {
                $this->options[$key]['type'] = 'job_priority_' . $key;
                $this->options[$key]['label'] = $label;

                if ($this->urlPath && $this->paramName) {
                    $this->options[$key]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $key]
                    );
                }

                $this->options[$key] = array_merge_recursive(
                    $this->options[$key],
                    $this->additionalData
                );
            }

            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare addition data for actions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
