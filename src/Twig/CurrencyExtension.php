<?php

namespace App\Twig;

use App\Service\DeviseService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CurrencyExtension extends AbstractExtension
{
    private DeviseService $deviseService;

    public function __construct(DeviseService $deviseService)
    {
        $this->deviseService = $deviseService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('currency_convert', [$this, 'convert']),
        ];
    }

    public function convert(float $montant): float
    {
        return $this->deviseService->convert($montant);
    }
}
