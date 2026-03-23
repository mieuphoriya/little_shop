<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppUserAuthenticator;
#[Route(
    path: '/{_locale}/usager',
    requirements: ['_locale' => '%app.supported_locales%'],
    defaults: ['_locale' => 'fr'],

)]
final class UsagerController extends AbstractController
{
    #[Route(name: 'app_usager_index', methods: ['GET'])]
    public function index(UsagerRepository $usagerRepository): Response
    {
        return $this->render('usager/index.html.twig', [
            'usagers' => $usagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_usager_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        EntityManagerInterface $entityManager,
                        UserPasswordHasherInterface $passwordHasher,
                        UserAuthenticatorInterface $userAuthenticator,
                        AppUserAuthenticator $authenticator): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $usager->setRoles(["ROLE_CLIENT"]);

            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);

            $entityManager->persist($usager);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $usager,
                $authenticator,
                $request
            );

//            return $this->redirectToRoute('app_usager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
        ]);
    }

    #[Route('/commandes', name: 'app_usager_commandes')]
    public function commandes(): Response {

        $usager = $this->getUser();
        if (!$usager) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('usager/commandes.html.twig', [
            'commandes' => $usager->getCommandes(),
        ]);
    }

    #[Route('/commandes/{id}', name: 'app_usager_commande')]
    public function commande(Commande $commande): Response {

        if ($commande->getUsager() !== $this->getUser()) {
            return $this->redirectToRoute('app_usager_commandes');
        }

        return $this->render('usager/commande.html.twig', [
            'commande' => $commande,
        ]);
    }

}
