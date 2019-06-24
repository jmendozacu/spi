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
use Magento\Framework\UrlInterface;

class JobActions extends Column
{
    const URL_PATH_EDIT = 'binaryanvil_jobmanager/job/view';

    const URL_PATH_DELETE = 'binaryanvil_jobmanager/job/delete';

    /**
     * @var string $deleteTitle
     */
    protected $deleteTitle = 'Deleting job <strong>#%id</strong>.';

    /**
     * @var string $deleteMessage
     */
    protected $deleteMessage = 'Are you sure you wan\'t to delete  this job?
        <ul style="margin: 15px 0 0 30px;">
            <li><strong>Job ID</strong>: %id</li>
            <li><strong>Job Type</strong>: <code>%type</code></li>
        </ul>';

    /**
     * @var \Magento\Framework\UrlInterface $urlBuilder
     */
    protected $urlBuilder;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['job_id'])) {
                continue;
            }

            $item[$this->getData('name')] = [
                'view' => [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_EDIT,
                        [
                            'id' => $item['job_id'],
                        ]
                    ),
                    'label' => __('View'),
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_DELETE,
                        [
                            'id' => $item['job_id'],
                        ]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __(
                            $this->deleteTitle,
                            [
                                'id' => '${ $.$data.job_id }',
                            ]
                        ),
                        'message' => __(
                            $this->deleteMessage,
                            [
                                'type' => '${ $.$data.type }',
                                'id' => '${ $.$data.job_id }',
                            ]
                        ),
                    ],
                ],
            ];
        }

        return $dataSource;
    }
}
