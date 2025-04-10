<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Ehtterami\IpTruffle\Facades\BannerFacade;
use Ehtterami\IpTruffle\Facades\TranslateFacade;

class Translate extends Command {
    protected static $defaultName = 'translate';
    private TranslateFacade $translator;

    public function __construct()
    {
        parent::__construct(self::$defaultName);
        $this->translator = new TranslateFacade();
    }

    protected function configure(): void
    {
        $this->setDescription('Translate IP address between decimal and binary formats')->addOption('ip', 'i', InputOption::VALUE_REQUIRED, 'Input IP address/Binary address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $ipAddress = $input->getOption('ip');

            if(!$ipAddress) {
                throw new InvalidArgumentException('IP address is required. Use --ip or -i to input an IP address.');
            }

            $result = $this->translator->translate($ipAddress);

            $table = new Table($output);
            $table->setHeaders(['Input', 'Translate']);
            $table->addRow([$ipAddress, $result]);

            $output->writeln("\n<info>Translate Result:</info>");
            $table->render();

            return Command::SUCCESS;
        }catch(InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

$application = new Application('IP Truffle', '1.3.1');
$application->add(new Translate());
BannerFacade::render();
$application->run();