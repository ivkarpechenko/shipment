<?php

namespace App\Infrastructure\Console\Tax;

use App\Application\CommandBus;
use App\Application\Country\Query\GetAllCountriesQuery;
use App\Application\QueryBus;
use App\Application\Tax\Command\CreateTaxCommand;
use App\Application\Tax\Query\GetAllTaxesQuery;
use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Entity\Tax;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:tax:create', description: 'Create tax console command')]
class CreateTaxConsoleCommand extends Command
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
         * Choice country service
         */
        $countries = $this->queryBus->handle(new GetAllCountriesQuery());
        $choiceList = new ChoiceQuestion(
            'Please select country',
            array_map(function (Country $country) {
                return $country->getCode();
            }, $countries),
            null
        );
        $choiceList->setErrorMessage("Selected country %s is invalid.\n");
        $countryCode = $helper->ask($input, $output, $choiceList);

        /**
         * Set tax name
         */
        $setNameQuestion = new Question("Set tax name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 200) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 200 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set tax value
         */
        $setValueQuestion = new Question("Set value \n", null);
        $setValueQuestion->setValidator(function ($answer) {
            if (is_float($answer)) {
                throw new \RuntimeException(
                    'The value field must be float'
                );
            }

            return $answer;
        });
        $value = $helper->ask($input, $output, $setValueQuestion);

        /**
         * Set tax expression
         */
        $setExpressionQuestion = new Question("Set tax expression. For example 'price/(1+value)*value',\n 
            where 'price' is total delivery price, 'value' is value of tax \n", null);
        $setExpressionQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 250) {
                throw new \RuntimeException(
                    'The expression field is required and must be less than 250 characters'
                );
            }

            return $answer;
        });
        $expression = $helper->ask($input, $output, $setExpressionQuestion);

        $this->commandBus->dispatch(new CreateTaxCommand($countryCode, $name, $value, $expression));

        $taxes = $this->queryBus->handle(new GetAllTaxesQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'country_code', 'name', 'value', 'expression', 'created_at', 'updated_at'])
            ->setRows(array_map(function (Tax $tax) {
                return [
                    $tax->getId()->toRfc4122(),
                    $tax->getCountry()->getCode(),
                    $tax->getName(),
                    $tax->getValue(),
                    $tax->getExpression(),
                    $tax->getCreatedAt()->format('Y-m-d H:i:s'),
                    $tax->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $taxes));

        $table->render();

        $output->writeln(sprintf('Tax with country code %s and name %s successfully added', $countryCode, $name));

        return Command::SUCCESS;
    }
}
