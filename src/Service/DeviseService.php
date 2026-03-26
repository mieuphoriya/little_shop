<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeviseService
{
    private RequestStack $requestStack;
    private HttpClientInterface $client;
    private string $accessKey;

    public function __construct(RequestStack $requestStack, HttpClientInterface $client, string $exchangeApiKey)
    {
        $this->requestStack = $requestStack;
        $this->client = $client;
        $this->accessKey = $exchangeApiKey;
    }

    public function getDevise(): string
    {
        $session = $this->requestStack->getSession();
        return $session->get('devise', 'EUR');
    }

    public function getTaux(): array
    {
        $session = $this->requestStack->getSession();

        if ($session->has('taux')) {
            return $session->get('taux');
        }

        $url = 'https://api.exchangeratesapi.io/v1/latest?access_key=' . $this->accessKey . '&format=1';
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        $taux = $data['rates'];
        $base = $data['base'];

        if ($base !== 'EUR') {
            foreach ($taux as $dev => $rate) {
                $taux[$dev] = $rate / $taux['EUR'];
            }
            $taux['EUR'] = 1.0;
        }

        $session->set('taux', $taux);

        return $taux;
    }

    public function convert(float $montant, ?string $devise = null): float
    {
        $devise = $devise ?? $this->getDevise();
        $taux = $this->getTaux();

        if ($devise === 'EUR') {
            return $montant;
        }

        if (!isset($taux[$devise])) {
            return $montant;
        }

        return $montant * $taux[$devise];
    }
}
