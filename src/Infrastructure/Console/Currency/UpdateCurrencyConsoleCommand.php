<?php

namespace App\Infrastructure\Console\Currency;

use App\Application\CommandBus;
use App\Application\Currency\Command\UpdateCurrencyCommand;
use App\Application\Currency\Query\GetAllCurrenciesQuery;
use App\Application\QueryBus;
use App\Domain\Currency\Entity\Currency;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:currency:update', description: 'Update currency console command')]
class UpdateCurrencyConsoleCommand extends Command
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
         * Choice currency
         */
        $currencies = $this->queryBus->handle(new GetAllCurrenciesQuery());
        $choiceList = new ChoiceQuestion(
            'Please select your currency',
            array_map(function (Currency $currency) {
                return $currency->getCode();
            }, $currencies),
            null
        );
        $choiceList->setErrorMessage("Selected currency %s is invalid.\n");
        $code = $helper->ask($input, $output, $choiceList);

        /**
         * Set new name for selected currency
         */
        $setNameQuestion = new Question("Set new name (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The name field must be less than 100 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set status for selected currency
         */
        $setIsActiveQuestion = new Question("Set is_active (press enter for skip stage) \n", null);
        $setIsActiveQuestion->setValidator(function ($answer) {
            if (is_null($answer)) {
                return null;
            }

            $answer = filter_var($answer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_bool($answer)) {
                throw new \RuntimeException(
                    'The is_active field must contain a boolean value'
                );
            }

            return $answer;
        });
        $isActive = $helper->ask($input, $output, $setIsActiveQuestion);

        $this->commandBus->dispatch(new UpdateCurrencyCommand($code, $name, $isActive));

        $currencies = $this->queryBus->handle(new GetAllCurrenciesQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'code', 'num', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (Currency $currency) {
                return [
                    $currency->getId()->toRfc4122(),
                    $currency->getCode(),
                    $currency->getNum(),
                    $currency->getName(),
                    $currency->isActive(),
                    $currency->getCreatedAt()->format('Y-m-d H:i:s'),
                    $currency->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $currencies));

        $table->render();

        if (!is_null($name) || !is_null($isActive)) {
            $output->writeln(sprintf('Currency with code %s successfully updated', $code));
        } else {
            $output->writeln(sprintf('Currency with code %s not updated', $code));
        }

        return Command::SUCCESS;
    }
}
