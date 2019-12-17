<?php declare(strict_types=1);

namespace Lexim\InventoryIntegration\Cron;

use Lexim\InventoryIntegration\Model\Inventory;

class UpdateInventory
{
    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * InventoryUpdate constructor.
     * @param Inventory $inventory
     */
    public function __construct(
        Inventory $inventory
    ) {
        $this->inventory = $inventory;
    }

    public function execute()
    {
        $this->inventory->updateProductList();
    }
}
