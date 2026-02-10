<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BoutiqueService;
use Symfony\Contracts\Translation\TranslatorInterface;

# On peut définir ici un préfixe pour les URL de toutes les routes des actions de la classe DefaultController

#[Route(
    path: '/{_locale}',
    requirements: ['_locale' => '%app.supported_locales%'],
    defaults: ['_locale' => 'fr']
)]

class DefaultController extends AbstractController
{
    #[Route(
        path: '', // L'URL auquel répondra cette action sera donc /
        name: 'app_default_index',
    )]
    public function index(): Response
    {
        // On récupère les données à transmettre à la vue
        // Ici c'est la date du jour, mais ce pourrait être des données du Modèle
        $now = new \DateTime("now");

        // Et on retourne une réponse HTTP au format HTML (la vue)
        //   fabriquée à partir d'un template Twig
        //   auquel on transmet les données qu'il doit mettre en forme
        return $this->render('default/index.html.twig', [
            "dateActuelle" => $now,
        ]);
    }

    #[Route(
        path: 'test', // L'URL auquel répondra cette action sera donc /test
        name: 'app_default_test',
    )]
    public function test(): Response
    {
        // On renvoie une réponse HTTP, au format HTML (par défaut)
        //  qui contient juste un petit texte.
        return new Response("Hello World !");
    }

    // TODO : route et contrôleur de la page de contact

    #[Route(
        path: 'contact', // L'URL auquel répondra cette action sera donc /
        name: 'app_default_contact',
    )]
     public function contact(): Response
     {
        $now = new \DateTime("now");

        return $this->render('default/contact.html.twig', [
            "dateActuelle" => $now,
        ]);
     }



}
