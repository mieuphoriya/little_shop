<?php

namespace App\Controller;

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
    public function index(BoutiqueService $boutiqueService, TranslatorInterface $translator): Response
    {
        return $this->render(
            'boutique/index.html.twig',
            ['categories' => $boutiqueService->findAllCategories()]
        );

        $traduction = $translator->trans('boutique.index.mot_boutique', 'boutique.index.mot_rayon');

    }
    #[Route(
        path: '/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
        requirements: ['idCategorie' => '\d+']
    )]
    public function rayon(BoutiqueService $boutiqueService, int $idCategorie): Response
    {
        $categorie=$boutiqueService->findCategorieById($idCategorie);
        if (!$categorie) {
            throw $this->createNotFoundException("Le rayon numéro '$idCategorie' n'existe pas");
        }
        return $this->render(
            'boutique/rayon.html.twig',
            [
                'categorie' => $categorie,
                'produits' => $boutiqueService->findProduitsByCategorie($idCategorie)
            ]
        );
    }


}
