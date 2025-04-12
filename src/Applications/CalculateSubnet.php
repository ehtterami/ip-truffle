<?php

namespace Ehtterami\IpTruffle\Applications;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;
use Ehtterami\IpTruffle\Facades\SubnetCalculatorFacade;
use InvalidArgumentException;

class CalculateSubnet extends Command
{
    protected static $defaultName = 'calculate:subnet';
    private SubnetCalculatorFacade $calculator;

    public function __construct()
    {
        parent::__construct(self::$defaultName);
        $this->calculator = new SubnetCalculatorFacade();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Calculate subnet details based on base !Pv4 address and required hosts')
            ->addOption(
                'base',
                'b',
                InputOption::VALUE_REQUIRED,
                'THe base IPv4 address (e.g., 192.168.1.0)'
            )
            ->addOption(
                'counts',
                'c',
                InputOption::VALUE_REQUIRED,
                'The number of required hosts'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $baseIPv4 = $input->getOption('base');
            $requiredHosts = $input->getOption('counts');
            if(!$baseIPv4 || !$requiredHosts) {
                throw new \InvalidArgumentException('Both --base (-b) and --counts (-c) options are required.');
            }

            $result = $this->calculator->calculate($baseIPv4, (int) $requiredHosts);

            $io->title('Subnet Calculation Results');
            $io->section('Here is the breakdown of your subnet configuration:');

            $table = new Table($output);
            $table->setHeaderTitle('Subnet Summery')
            ->setStyle('box')
                ->setHeaders(['Property', 'Value'])
                ->addRows([
                    ['Required Hosts', $result['required']],
                    ['Available Hosts', $result['available']],
                    ['Subnet Mask', $result['mask']],
                    ['CIDR Notation', $result['cidr']],
                    ['IP Class', $result['class']],
                    ['Base Address', $result['base']],
                    ['First Usable IP', $result['start']],
                    ['Last Usable IP', $result['end']],
                    ['Memory Usage (kb)', ($result['memory'] / 1024)],
                    ['Execution Time (seconds)', number_format($result['time'], 6)],
                ]);

            $table->render();

            $io->newLine();
            $io->info("The calculation result of {$baseIPv4} base IP and {$requiredHosts} required hosts.");

            return Command::SUCCESS;
        }catch(InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}