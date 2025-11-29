<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Directory;

use App\Application\CommandBus;
use App\Application\Directory\Command\ImportOkatoOktmoCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-okato-oktmo',
    description: 'Импорт данных ОКАТО-ОКТМО из excel файла в базу данных'
)]
class ImportOkatoOktmoConsoleCommand extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $parameter,
        private readonly CommandBus $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Импорт данных ОКАТО-ОКТМО');

        $filePath = $this->parameter->get('okato_file_path');

        try {
            $this->commandBus->dispatch(new ImportOkatoOktmoCommand($filePath));
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('Произошла ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Импорт успешно завершен');

        return Command::SUCCESS;
    }
}
