<?php
// src/Twig/DeviseExtension.php
namespace App\Twig;

use App\Service\DeviseService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DeviseExtension extends AbstractExtension
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

    public function convert(float $montant): string
    {
        $montantConverti = $this->deviseService->convert($montant);
        $devise = $this->deviseService->getDevise();
        return number_format($montantConverti, 2, ',', ' ') . ' ' . $devise;
    }
}
