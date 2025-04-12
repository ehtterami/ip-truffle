<?php

namespace Ehtterami\IpTruffle\Applications;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;
use Ehtterami\IpTruffle\Facades\NetIDCalculatorFacade as Facade;
use Exception;
use InvalidArgumentException;

class CalculateNetID extends Command
{
    protected static $defaultName = "calculate:net-id";
    private Facade $calculator;

    public function __construct()
    {
        parent::__construct(self::$defaultName);
        $this->calculator = new Facade();
    }

    protected function configure()
    {
        $this->setDescription('Calculate Network ID and related details')
            ->addOption('ip', 'i', InputOption::VALUE_REQUIRED, 'IPv4 address (e.g., 192.168.1.1)')
            ->addOption('mask', 'm', InputOption::VALUE_REQUIRED, 'Subnet mask (e.g., 255.255.255.0)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $ipAddress = $input->getOption('ip');
            $subnetMask = $input->getOption('mask');
            if(!$ipAddress || !$subnetMask) {
                throw new InvalidArgumentException('Both IP address and subnet mask are required.');
            }

            $result = $this->calculator->calculate($ipAddress, $subnetMask);

            $io->title('Network Calculation Results');

            $table = new Table($output);
            $table->setHeaderTitle('Network ID')
                ->setStyle('box')
                ->setHeaders(['Property', 'Value'])
                ->addRows([
                    ['Network ID', $result['id']],
                    ['CIDR Notation', $result['cidr']],
                    ['First Host', $result['first']],
                    ['Last Host', $result['last']],
                    ['Wildcard', $result['wild']],
                    ['IP Address', $ipAddress],
                    ['Subnet Mask', $subnetMask],
                    ['Broadcast', $result['broadcast']],
                ]);

            $table->render();

            $io->newLine();
            $io->info("This is results of {$ipAddress} IPv4 address and {$subnetMask} subnet mask.");

            $io->section('Metrics');
            $io->listing([
                sprintf('Execution Time: %s seconds', $result['time']),
                sprintf('Memory Usage: %s bytes', $result['memory']),
            ]);

            return Command::SUCCESS;
        }catch(Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}