<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeviseController extends AbstractController
{
    #[Route('/devise/{code}', name: 'app_devise_change')]
    public function change(string $code, Request $request): Response
    {
        $request->getSession()->set('devise', $code);
        return $this->redirect($request->headers->get('referer'));
    }
}
