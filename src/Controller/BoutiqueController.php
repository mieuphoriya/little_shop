<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BoutiqueController extends AbstractController
{
    #[Route('', name : 'app_boutique')] // URL : /boutique
    public function index(BoutiqueService $boutique) : Response {
// Utiliser le service pour récupérer les catégories
        $categories = $boutique->findAllCategories();
// Rendre un template auquel on transmet les catégories
//
    }
}
