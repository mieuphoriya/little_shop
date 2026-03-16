<?php

namespace App\Controller;

use App\Service\PanierService;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/{_locale}/panier',
    requirements: ['_locale' => '%app.supported_locales%'],
    defaults: ['_locale' => 'fr'],

)]
final class PanierController extends AbstractController
{

    #[Route('/', name: 'app_panier_index')]
    public function index(PanierService $panierService, ProduitRepository $produitRepository): Response {

        $panier = [];

        foreach ($panierService->getContenu() as $idProduit => $quantite) {

            $produit = $produitRepository->find($idProduit);

            if ($produit) {
                $panier[] = [
                    'produit' => $produit,
                    'quantite' => $quantite
                ];
            }
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'total' => $panierService->getTotal()
        ]);
    }


    #[Route('/ajouter/{idProduit}/{quantite}', name: 'app_panier_ajouter', requirements: ['idProduit' => '\d+']) ]
    public function ajouter(int $idProduit, int $quantite, PanierService $panierService): Response {

        $panierService->ajouterProduit($idProduit, $quantite);

        return $this->redirectToRoute('app_panier_index');
    }


    #[Route('/enlever/{idProduit}/{quantite}', name: 'app_panier_enlever', requirements: ['idProduit' => '\d+'])]
    public function enlever(int $idProduit, int $quantite, PanierService $panierService): Response {

        $panierService->enleverProduit($idProduit, $quantite);

        return $this->redirectToRoute('app_panier_index');
    }


    #[Route('/supprimer/{idProduit}', name: 'app_panier_supprimer', requirements: ['idProduit' => '\d+'])]
    public function supprimer(int $idProduit, PanierService $panierService): Response {

        $panierService->supprimerProduit($idProduit);

        return $this->redirectToRoute('app_panier_index');
    }


    #[Route('/vider', name: 'app_panier_vider')]
    public function vider(PanierService $panierService): Response {
        $panierService->vider();
        return $this->redirectToRoute('app_panier_index');
    }

    public function nombreProduits(PanierService $panier): Response {
        return new Response((string) $panier->getNombreProduits());
    }

}
