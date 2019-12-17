<?php declare(strict_types=1);

namespace Lexim\Localretailer\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LocalRetailerstore extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('local_retailer_store', 'id');
    }

    /**
     * @param $customerId
     * @return array
     * @throws LocalizedException
     */
    public function getStoreInfoByCustomerId($customerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getMainTable(),
            ['retailer_id']
        )->where(
            'id_customer_use = :customer_id'
        );
        return $connection->fetchRow($select, [':customer_id' => $customerId]);
    }
}