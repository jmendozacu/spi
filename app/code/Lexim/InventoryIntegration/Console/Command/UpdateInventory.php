<?php declare(strict_types=1);

namespace Lexim\InventoryIntegration\Console\Command;

use Lexim\InventoryIntegration\Model\Inventory;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateInventory extends Command
{
    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * @var State
     */
    private $state;

    /**
     * UpdateInventory constructor.
     * @param Inventory $inventory
     * @param \Magento\Framework\App\State $state
     * @param null $name
     */
    public function __construct(
        Inventory $inventory,
        \Magento\Framework\App\State $state,
        $name = null
    ) {
        $this->state = $state;
        $this->inventory = $inventory;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('lexim:inventory:update');
        $this->setDescription('Lexim update inventory.');

        parent::configure();
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $output->writeln('<info>Starting update...</info>');
        $start = microtime(true);
        $output->writeln("<info>Starting time at $start</info>");
        try {
            $this->inventory->updateProductList();
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
        $time_elapsed_secs = microtime(true) - $start;
        $output->writeln("<info>End time $time_elapsed_secs secs</info>");
        $output->writeln('<info>Updated successfully.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
