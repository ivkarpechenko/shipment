<?php

namespace App\Infrastructure\Console\Country;

use App\Application\CommandBus;
use App\Application\Country\Command\CreateCountryCommand;
use App\Application\Country\Query\GetAllCountriesQuery;
use App\Application\QueryBus;
use App\Domain\Country\Entity\Country;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:country:create', description: 'Create country console command')]
class CreateCountryConsoleCommand extends Command
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        /**
         * Set country name
         */
        $setNameQuestion = new Question("Set country name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 250) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 250 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set country code
         */
        $setCodeQuestion = new Question("Set country code \n", null);
        $setCodeQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) != 2) {
                throw new \RuntimeException(
                    'The code field is required and must be 2 characters long'
                );
            }

            if ($answer == trim($answer) && str_contains($answer, ' ')) {
                throw new \RuntimeException(
                    'The code field must be without spaces'
                );
            }

            return $answer;
        });
        $code = $helper->ask($input, $output, $setCodeQuestion);

        $this->commandBus->dispatch(new CreateCountryCommand($name, $code));

        $countries = $this->queryBus->handle(new GetAllCountriesQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'code', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (Country $country) {
                return [
                    $country->getId()->toRfc4122(),
                    $country->getCode(),
                    $country->getName(),
                    $country->isActive(),
                    $country->getCreatedAt()->format('Y-m-d H:i:s'),
                    $country->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $countries));

        $table->render();

        $output->writeln(sprintf('Country with code %s successfully added', $code));

        return Command::SUCCESS;
    }
}
