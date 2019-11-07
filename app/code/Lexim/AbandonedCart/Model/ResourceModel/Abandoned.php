<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Abandoned extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('lx_email_abandoned_cart', 'id');
    }    

}
