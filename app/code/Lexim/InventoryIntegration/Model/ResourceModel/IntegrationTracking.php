<?php

// declare(strict_types=1);

namespace Lexim\InventoryIntegration\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class IntegrationTracking extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lexim_inventory_integration_tracking', 'id');
    }

    /**
     * @return string|null
     * @throws LocalizedException
     */
    public function getProductId()
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getMainTable(),
            ['product_id']
        );
        $id = $connection->fetchOne($select);
        if ($id) {
            return $id;
        }
        return 0;
    }

    /**
     * @param $productId
     * @throws LocalizedException
     */
    public function updateCurrentProductId($productId)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getMainTable(),
            ['id']
        );
        $id = $connection->fetchOne($select);
        if ($id) {
            $connection->update(
                $this->getMainTable(),
                ['product_id' => $productId],
                ['id = ?' => $id]
            );
        } else {
            $connection->insert($this->getMainTable(), ['product_id' => $productId]);
        }
    }
}
