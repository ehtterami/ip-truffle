<?php

namespace Ehtterami\IpTruffle\Facades;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;
use Ehtterami\IpTruffle\Services\SubnetCalculatorService;
use InvalidArgumentException;

class SubnetCalculatorFacade extends Command
{
    protected static $defaultName = 'calculator';
    private SubnetCalculatorService $calculator;

    public function __construct()
    {
        parent::__construct(self::$defaultName);
        $this->calculator = new SubnetCalculatorService();
    }

    protected function configure()
    {
        $this->setDescription('Calculate subnet based on hosts')->addOption('hosts', 'c', InputOption::VALUE_REQUIRED, 'Number of hosts')->addOption('base', 'b', InputOption::VALUE_OPTIONAL, 'Base IP address (default: 1.1.1.0)', '1.1.1.0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $hosts = (int) $input->getOption('hosts');
            $base = $input->getOption('base');

            if($hosts <= 0) {
                throw new InvalidArgumentException('Number of hosts must be greater than 0');
            }

            $subnet = $this->calculator->calculate($hosts);
            $ipClass = $this->calculator->getIPClass($base);
            $possibleIPs = $this->calculator->generateIPRange($base, $subnet['cidr']);

            $table = new Table($output);
            $table->setStyle('box');
            $table->setHeaderTitle('Subnet Results')
                    ->setHeaders(['Property', 'Result'])
                    ->addRows([
                        ['Required Hosts', $hosts],
                        ['Available Hosts', $subnet['hosts']],
                        ['Subnet Mask', $subnet['mask']],
                        ['CIDR Notation', "/{$subnet['cidr']}"],
                        ['Class', $ipClass],
                        ['Base Address', $base],
                        ['First', $possibleIPs[0]],
                        ['Last', end($possibleIPs)],
                    ]);
            $output->writeln("\n<info>Subnet Calculation Result:</info>");
            $table->render();

            $availableAddressesTable = new Table($output);
            $availableAddressesTable->setStyle('box');
            $availableAddressesTable->setHeaderTitle('Available Addresses');

            $chunks = array_chunk($possibleIPs, 8);
            $rows = array_map(fn($chunk) => array_pad($chunk, 8, ''), $chunks);

            $availableAddressesTable->setHeaders(['S0', 'S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7'])->addRows($rows);

            $output->writeln("\n<info>Available IP Addresses:</info>");
            $availableAddressesTable->render();

            return Command::SUCCESS;
        } catch(InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}