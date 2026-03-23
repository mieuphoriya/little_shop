<?php
namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    /// patratim@iut2-dg033-d09:/users/info/pub/2a/R4.01/TP/patratim$ php bin/console make:controller PanierController
    /// //pour creer un controller
    ///
    const PANIER_SESSION = 'panier';   // Le service session
    private $session;                   // Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    private $panier;                    // Le nom de la variable de session pour faire persister $this->panier

    public function __construct(RequestStack $requestStack,  ProduitRepository $produitRepository) {

        $this->session = $requestStack->getSession();
        $this->produitRepository = $produitRepository;
        // récupération du panier en session
        $this->panier = $this->session->get(self::PANIER_SESSION, []);
    }


    // Renvoie le montant total du panier
    public function getTotal() : float {
        $total = 0;
        foreach ($this->panier as $idProduit => $quantite){

            $produit = $this->produitRepository->find($idProduit);
            if ($produit) {
                $total +=$produit->getPrix() * $quantite;
            }
        }
        return $total;
    }


    // Renvoie le nombre de produits dans le panier
    public function getNombreProduits() : int {
        return array_sum($this->panier);
    }


    // Ajouter au panier le produit $idProduit en quantite $quantite
    public function ajouterProduit(int $idProduit, int $quantite = 1) : void {
        /* A COMPLETER */
        // 1 Est ce qu'un produit existe dans le panier ?
        // si oui +quantite
        // si non tu créés produit et la quantite dans le panier
        // 2 maj de la session

        if (isset($this->panier[$idProduit]) ) {
            $this->panier[$idProduit]+= $quantite;
        } else {
            $this->panier[$idProduit] = $quantite;
        }

        $this->session->set(self::PANIER_SESSION, $this->panier);

    }


    // Enlever du panier le produit $idProduit en quantite $quantite
    public function enleverProduit(int $idProduit, int $quantite = 1) : void {
        if (!isset($this->panier[$idProduit])) {
            return;
        }

        $this->panier[$idProduit] -= $quantite;

        if ($this->panier[$idProduit] <= 0) {
            unset($this->panier[$idProduit]);
        }

        $this->session->set(self::PANIER_SESSION, $this->panier);
    }


    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void {
        if (isset($this->panier[$idProduit])) {
            unset($this->panier[$idProduit]);
            $this->session->set(self::PANIER_SESSION, $this->panier);
        }
    }

    // Vider complètement le panier
    public function vider() : void {
        $this->panier = [];
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }


    // Renvoie le contenu du panier dans le but de l'afficher
    //   => un tableau d'éléments [ "produit" => un objet produit, "quantite" => sa quantite ]
    public function getContenu() : array {
        return $this->panier;
    }


    public function panierToCommande(Usager $usager) : ?Commande {

        if($this->panier){
            $commande = new Commande();
            $commande->setDateCreation(new \DateTime());
            $usager->addCommande($commande);
        }

        foreach ($this->panier as $idProduit => $quantite){
            $ligneCommande = new LigneCommande();
            $produit = $this->produitRepository->find($idProduit);
            $ligneCommande->setProduit($produit);
            $ligneCommande->setQuantite($quantite);
            $ligneCommande->setPrix($produit->getPrix() * $quantite);
            $commande->addLigneCommande($ligneCommande);
        }
        $this->vider();
        return $commande;
    }
}
