<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\BoutiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route(
    path: '/{_locale}/boutique',
    requirements: ['_locale' => '%app.supported_locales%'],
    defaults: ['_locale' => 'fr']
)]
//#[Route(
//    path: '/boutique',
//)]
final class BoutiqueController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'app_boutique_index'
    )]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render(
            'boutique/index.html.twig',
            ['categories' => $categorieRepository->findAll()]
        );

    }

    #[Route(
        path: '/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
        requirements: ['idCategorie' => '\d+']
    )]
    public function rayon(CategorieRepository $categorieRepository, int $idCategorie): Response
    {
        $categorie = $categorieRepository->find($idCategorie);
        if (!$categorie) {
            throw $this->createNotFoundException("Le rayon numéro '$idCategorie' n'existe pas");
        }
        return $this->render(
            'boutique/rayon.html.twig',
            [
                'categorie' => $categorie,
                'produits' => $categorie->getProduits()
            ]
        );
    }

    #[Route(
        path: '/chercher/{recherche}',
        name: 'app_boutique_chercher',
        requirements: ['recherche' => '.+'], // regexp pour avoir tous les car, / compris
        defaults: ['recherche' => '']
    )]
    public function chercher(ProduitRepository $produitRepository , string $recherche): Response
    {

        return $this->render(
            'boutique/chercher.html.twig',
            [
                'searchedProduct' => $recherche,
                'findedProducts' => $produitRepository->findProduits($recherche)
            ]
        );

    }
}
