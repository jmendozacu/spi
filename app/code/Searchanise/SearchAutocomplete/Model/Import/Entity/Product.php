<?php

namespace Searchanise\SearchAutocomplete\Model\Import\Entity;

class Product extends \Magento\CatalogImportExport\Model\Import\Product
{
    /**
     * Delete products.
     *
     * @return \Magento\CatalogImportExport\Model\Import\Product
     */
    protected function _deleteProducts()
    {
        $idsToDelete = null;

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $idsToDelete = [];

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->validateRow($rowData, $rowNum) && self::SCOPE_DEFAULT == $this->getRowScope($rowData)) {
                    $idsToDelete[] = $this->_oldSku[$rowData[self::COL_SKU]]['entity_id'];
                }
            }
        }

        $this->_eventManager->dispatch(
            'searchanise_import_delete_product_entity_after',
            [
            'idsToDelete' => $idsToDelete
            ]
        );

        return parent::_deleteProducts();
    }

    /**
     * Update and insert data in entity table.
     *
     * @param  array $entityRowsIn Row for insert
     * @param  array $entityRowsUp Row for update
     * @return \Magento\CatalogImportExport\Model\Import\Product
     */
    public function saveProductEntity(array $entityRowsIn, array $entityRowsUp)
    {
        $productIds = [];
        $entityId = 'entity_id';

        // Update products data
        $ret = parent::saveProductEntity($entityRowsIn, $entityRowsUp);

        if (!empty($entityRowsUp)) {
            $productIds = array_merge(
                $productIds,
                array_map(
                    function ($v) use ($entityId) {
                        return $v[$entityId];
                    },
                    $entityRowsUp
                )
            );
        }

        if (!empty($entityRowsIn)) {
            static $entityTable = null;

            if (!$entityTable) {
                $entityTable = $this->_resourceFactory->create()->getEntityTable();
            }

            $select = $this->_connection
                ->select()
                ->from($entityTable, [$entityId])
                ->where('sku IN (?)', array_keys($entityRowsIn));

            $insertedProducts = $this->_connection->fetchAll($select);

            if (!empty($insertedProducts)) {
                foreach ($insertedProducts as $product) {
                    if (!empty($product[$entityId])) {
                        $productIds[] = $product[$entityId];
                    }
                }
            }
        }

        if (!empty($productIds)) {
            $this->_eventManager->dispatch(
                'searchanise_import_save_product_entity_after',
                [
                'productIds' => $productIds
                ]
            );
        }

        return $ret;
    }
}
