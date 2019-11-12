<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Model;

use Lexim\AbandonedCart\Model\ResourceModel\Abandoned as ResourceModelCartAbandonment;

class Abandoned extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelCartAbandonment::class);
    }
}
