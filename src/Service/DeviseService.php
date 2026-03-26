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

        // если уже есть в session → берём
        if ($session->has('taux')) {
            return $session->get('taux');
        }

        // иначе → идём в API
        $url = 'https://api.exchangeratesapi.io/v1/latest?access_key=' . $this->accessKey . '&format=1';
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        $taux = $data['rates'];
        $base = $data['base']; // например "USD" или "EUR"

        // если база API != EUR → пересчитаем все курсы относительно EUR
        if ($base !== 'EUR') {
            foreach ($taux as $dev => $rate) {
                $taux[$dev] = $rate / $taux['EUR'];
            }
            $taux['EUR'] = 1.0;
        }

        // сохраняем в session
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
            // если нет курса → возвращаем без изменений
            return $montant;
        }

        // умножаем на курс, т.к. $taux[devise] = сколько единиц валюты за 1 EUR
        return $montant * $taux[$devise];
    }
}
