<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Model\ResourceModel\Abandoned;

use Lexim\AbandonedCart\Model\Abandoned;
use Lexim\AbandonedCart\Model\ResourceModel\Abandoned as ResourceModelCartAbandonment;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection  extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Abandoned::class, ResourceModelCartAbandonment::class);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function filterStore($storeId)
    {
        $this->addFilter('store_id', $storeId);
        return $this;
    }

    /**
     * @return $this
     */
    public function filterStatusPending()
    {
        $this->addFilter('status', 'pending');
        return $this;
    }
}
