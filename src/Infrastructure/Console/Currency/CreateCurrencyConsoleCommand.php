<?php

namespace App\Infrastructure\Console\Currency;

use App\Application\CommandBus;
use App\Application\Currency\Command\CreateCurrencyCommand;
use App\Application\Currency\Query\GetAllCurrenciesQuery;
use App\Application\QueryBus;
use App\Domain\Currency\Entity\Currency;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:currency:create', description: 'Create currency console command')]
class CreateCurrencyConsoleCommand extends Command
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
         * Set currency code
         */
        $setCodeQuestion = new Question("Set currency code \n", null);
        $setCodeQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 3) {
                throw new \RuntimeException(
                    'The code field is required and must be less than 3 characters'
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

        /**
         * Set currency num
         */
        $setNumQuestion = new Question("Set currency num \n", null);
        $setNumQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_INT)) {
                throw new \RuntimeException(
                    'The num field must be numeric only'
                );
            }

            if (strlen($answer) > 3) {
                throw new \RuntimeException(
                    'The num field is required and must be less than 3 characters'
                );
            }

            return (int) $answer;
        });
        $num = $helper->ask($input, $output, $setNumQuestion);

        /**
         * Set currency name
         */
        $setNameQuestion = new Question("Set currency name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 100 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        $this->commandBus->dispatch(new CreateCurrencyCommand($code, $num, $name));

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

        $output->writeln(sprintf('Currency with code %s successfully added', $code));

        return Command::SUCCESS;
    }
}
