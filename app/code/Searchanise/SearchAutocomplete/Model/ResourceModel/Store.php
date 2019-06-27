<?php

namespace Searchanise\SearchAutocomplete\Model\ResourceModel;

class Store extends \Magento\Store\Model\ResourceModel\Store
{
    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\Cache\Type\Config          $configCacheType
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->eventManager = $eventManager;

        parent::__construct(
            $context,
            $configCacheType
        );
    }

    /**
     * Check store code before save
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return Magento\Store\Model\ResourceModel\Store
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $ret = parent::_beforeSave($object);

        $this->eventManager->dispatch('searchanise_core_save_store_before', ['store' => $object]);

        return $ret;
    }

    /**
     * Remove core configuration data after delete store
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return Magento\Store\Model\ResourceModel\Store
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $ret = parent::_afterDelete($object);

        $this->eventManager->dispatch('searchanise_core_delete_store_after', ['store' => $object]);

        return $ret;
    }
}
