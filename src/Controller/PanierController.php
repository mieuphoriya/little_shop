<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/{_locale}/panier/',
    requirements: ['_locale' => '%app.supported_locales%'],
    defaults: ['_locale' => 'fr']
)]
final class PanierController extends AbstractController
{
    #[Route('', name: 'app_panier_index')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    #[Route('ajouter/{idProduit}/{quantite}', name: 'app_panier_ajouter')]

    public function ajouter(int $idProduit, int $quantite, PanierService $panierService): Response
    {

        //
        $panierService->ajouterProduit($idProduit, $quantite);

        //
        return $this->redirectToRoute('app_panier_index');

            }

}
