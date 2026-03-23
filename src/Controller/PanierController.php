<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Service\PanierService;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
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

//
//Créer une route app_panier_commander et une méthode commander dans le contrôleur PanierController :

//o Cette action devra bien sûr utiliser la méthode panierToCommande créée précédemment
//o Elle utilisera (temporairement) l’usager d’identifiant égal à 1 comme propriétaire de la commande.

//Au TP suivant, c’est l’usager authentifié qui sera bien sûr choisi comme propriétaire de la commande !
//o Elle se terminera par l’affichage d’un template commande.html.twig qui indiquera à l’utilisateur son
//prénom, son nom, le numéro et la date de la commande qu’il vient de passer


//            'prenom' => $usager->getPrenom(),
//            'nom' => $usager->getNom(),
//            'numCommande' => $usager->getCommandes()->first()->getId(),
//            'dateCommande' => $usager->getCommandes()->first()->getDateCommande()

    #[Route('/commander', name: 'app_panier_commander')]
    public function commander(PanierService $panierService, EntityManagerInterface $entityManager): Response {

        $usager = $this->getUser();

        if (!$usager) {
            return $this->redirectToRoute('app_login');
        }

        $commande = $panierService->panierToCommande($usager);
        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->render('panier/commande.html.twig', [
            'prenom' => $usager->getPrenom(),
            'nom' => $usager->getNom(),
            'numCommande' => $commande->getId(),
            'dateCommande' => $commande->getDateCreation()
        ]);
    }

}
