<?php declare(strict_types=1);

namespace Lexim\Localretailer\Console\Command;

use Exception;
use Lexim\Localretailer\Model\Export\Invoice;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportInvoice extends Command
{
    /** @var State **/
    private $appState;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * UpdateInventory constructor.
     * @param Invoice $invoice
     * @param State $appState
     * @param null $name
     */
    public function __construct(
        Invoice $invoice,
        State $appState,
        $name = null
    ) {
        $this->invoice = $invoice;
        $this->appState = $appState;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('lexim:invoice:export');
        $this->setDescription('Lexim Invoice Export.');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        $output->writeln('<info>Starting export...</info>');
        $start = microtime(true);
        $output->writeln("<info>Starting time at $start</info>");
        try {
            $this->invoice->export(false);
        } catch (Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
        $time_elapsed_secs = microtime(true) - $start;
        $output->writeln("<info>End time $time_elapsed_secs secs</info>");
        $output->writeln('<info>Exported successfully.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
