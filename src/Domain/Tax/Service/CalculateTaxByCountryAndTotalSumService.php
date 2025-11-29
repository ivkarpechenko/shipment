<?php

namespace App\Domain\Tax\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CalculateTaxByCountryAndTotalSumService
{
    public function __construct(
        public TaxRepositoryInterface $taxRepository
    ) {
    }

    public function calculate(Country $country, float $price): float
    {
        $taxTotal = 0;
        $expression = new ExpressionLanguage();
        $taxes = $this->taxRepository->ofCountry($country);
        foreach ($taxes as $tax) {
            $taxTotal += $expression->evaluate($tax->getExpression(), ['price' => $price, 'value' => $tax->getValue()]);
        }

        return max($taxTotal, 0);
    }
}
