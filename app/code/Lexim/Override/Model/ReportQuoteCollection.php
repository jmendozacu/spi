<?php

namespace Lexim\Override\Model;


/**
 * Class ReportQuoteCollection
 * @package Lexim\Override\Model
 */
class ReportQuoteCollection extends \Magento\Reports\Model\ResourceModel\Quote\Collection
{

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $customerResource,
            $connection,
            $resource
        );
    }

    /**
     * Prepare for abandoned report
     *
     * @param array $storeIds
     * @param string $filter
     * @return $this
     */
    public function prepareForAbandonedReport($storeIds, $filter = null)
    {
        $this->addFieldToFilter(
            'items_count',
            ['neq' => '0']
        )->addFieldToFilter(
            'main_table.is_active',
            '1'
//        )->addFieldToFilter(
//            'main_table.customer_id',
//            ['neq' => null]
        )->addFieldToFilter(
            'main_table.customer_email',
            ['neq' => null]
        )->addSubtotal(
            $storeIds,
            $filter
        )->setOrder(
            'updated_at'
        );

        if (isset($filter['email']) || isset($filter['customer_name'])) {
            $this->addCustomerData($filter);
        }
        if (is_array($storeIds) && !empty($storeIds)) {
            $this->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        return $this;
    }


    /**
     * Resolve customers data based on ids quote table.
     *
     * @return void
     */
    public function resolveCustomerNames()
    {
        $select = $this->customerResource->getConnection()->select();
        $customerName = $this->customerResource->getConnection()->getConcatSql(['firstname', 'lastname'], ' ');

        $select->from(
            ['customer' => $this->customerResource->getTable('customer_entity')],
            ['entity_id', 'email']
        );

        $select->columns(
            ['customer_name' => $customerName]
        );

        $select->where(
            'customer.entity_id IN (?)',
            array_column(
                $this->getData(),
                'customer_id'
            )
        );

        $customersData = $this->customerResource->getConnection()->fetchAll($select);

        foreach ($this->getItems() as $item) {
            $isGuest = true;
            foreach ($customersData as $customerItemData) {
                if ($item['customer_id'] == $customerItemData['entity_id']) {
                    $item->setData(array_merge($item->getData(), $customerItemData));
                    $isGuest = false;
                }
            }
            if ($isGuest) {
                $item->setData(array_merge(
                    $item->getData(),
                    [
                        "email" => $item["customer_email"],
                        "customer_name" => $item["customer_firstname"] . " " . $item["customer_lastname"]
                    ]
                ));
            }
        }
    }

}
