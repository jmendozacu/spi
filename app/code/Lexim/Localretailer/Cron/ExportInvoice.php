<?php declare(strict_types=1);

namespace Lexim\Localretailer\Cron;

use Lexim\Localretailer\Model\Export\Invoice;

class ExportInvoice
{
    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * InventoryUpdate constructor.
     * @param Invoice $invoice
     */
    public function __construct(
        Invoice $invoice
    ) {
        $this->invoice = $invoice;
    }

    public function execute()
    {
        $this->invoice->export(true);
    }
}
