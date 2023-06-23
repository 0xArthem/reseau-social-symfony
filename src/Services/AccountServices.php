<?php

namespace App\Services;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\EditProfilType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\AbonnementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AccountServices
{
    private $twig;
    private $entityManager;
    private $formFactory;
    private $session;
    private $abonnementRepository;
    private $paginator;
    private $postRepository;
    private $users;
    private $urlGenerator;
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag, UrlGeneratorInterface $urlGenerator, UserRepository $users, PostRepository $postRepository, PaginatorInterface $paginator, AbonnementRepository $abonnementRepository, \Twig\Environment $twig, \Doctrine\ORM\EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, SessionInterface $session)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->abonnementRepository = $abonnementRepository;
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
        $this->users = $users;
        $this->urlGenerator = $urlGenerator;
        $this->parameterBag = $parameterBag;
    }

    public function updateProfile(User $user, Request $request): void
    {
        $form = $this->formFactory->create(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image de profil
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->parameterBag->get('profile_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les erreurs d'upload ici
                }

                $user->setImage($newFilename);
            }

            $this->entityManager->flush();

            // Message de succès
            $this->session->getFlashBag()->add('success', 'Votre profil a été correctement mis à jour !');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            // Message d'erreur
            $this->session->getFlashBag()->add('error', 'Votre profil n\'a pas pu être correctement mis à jour.');
        }
    }

    public function index(Request $request, ?User $user): Response
    {
        $visitedUser = $this->users->findOneBy(['username' => $request->attributes->get('username')]);

        if ($visitedUser) {
            $abonnements = $visitedUser->getAbonnements();
            $abonnes = $visitedUser->getAbonnes();

            $query = $this->postRepository->findBy(['user' => $visitedUser], ['createdAt' => 'DESC']);
            $posts = $this->paginator->paginate(
                $query,
                $request->query->get('page', 1),
                9
            );

            $postsIsPinned = $this->postRepository->findBy(['isPinned' => true, 'user' => $visitedUser], ['id' => 'DESC']);

            if ($visitedUser === $user) {
                return $this->indexLoggedInUser($user, $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, $request);
            } else {
                return $this->indexOtherUser($user, $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned);
            }
        } else {
            $redirectUrl = $this->urlGenerator->generate('app_home');
            return new RedirectResponse($redirectUrl);
        }
    }

    public function accountType(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $visitedUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $request->attributes->get('username')]);

        if ($visitedUser === $user) {
            $form = $this->formFactory->create(AccountType::class, $user);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $form->get('password')->getData();
                if ($newPassword) {
                    $hashedPassword = $passwordEncoder->encodePassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                }

                $this->entityManager->flush();

                $this->session->getFlashBag()->add('success', 'Vos informations ont été correctement mises à jour !');
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                $this->session->getFlashBag()->add('error', 'Vos informations n\'ont pas pu être correctement mises à jour.');
            }
        }

        $content = $this->twig->render('account/accountType.html.twig', [
            'user' => $user,
            'visitedUser' => $visitedUser,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }

    /** profil des autres utilisateurs **/
    public function indexOtherUser(?User $user, User $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned): Response
    {
        // On vérifie si l'utilisateur visité est abonné à l'utilisateur connecté (qui regarde donc son profil)
        $isFollowed = $this->abonnementRepository->findOneBy(['abonne' => $visitedUser, 'abonnement' => $user]) !== null;

        // On vérifie si l'utilisateur connecté est abonné à l'utilisateur visité
        $isSubscribed = $this->abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $visitedUser]) !== null;
        if ($user) {
            $abonnement = $this->abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $visitedUser]);
            $isSubscribed = ($abonnement !== null);
        }

        $content = $this->twig->render('account/other.html.twig', [
            'visitedUser' => $visitedUser,
            'posts' => $posts,
            'postsIsPinned' => $postsIsPinned,
            'abonnements' => $abonnements,
            'abonnes' => $abonnes,
            'isSubscribed' => $isSubscribed,
            'isFollowed' => $isFollowed
        ]);

        return new Response($content);
    }

    public function indexLoggedInUser(User $user, User $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, Request $request): Response
    {
        $this->updateProfile($user, $request);

        $form = $this->formFactory->create(EditProfilType::class, $user);
        $form->handleRequest($request);

        return $this->renderAccountPage($user, $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, $form);
    }

    public function renderAccountPage(User $user, User $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, $form): Response
    {
        $content = $this->twig->render('account/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'postsIsPinned' => $postsIsPinned,
            'visitedUser' => $visitedUser,
            'abonnements' => $abonnements,
            'abonnes' => $abonnes,
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }
}