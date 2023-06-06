<?php

namespace App\Services;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Topic;
use Twig\Environment;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostServices
{
    private $container;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    private $postRepository;
    private $postImagesDirectory;
    private $userRepository;
    private $urlGenerator;
    private $formFactory;
    private $twig;
    private $flashBag;
    private $slugger;
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        PostRepository $postRepository,
        ParameterBagInterface $parameterBag,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory,
        Environment $twig,
        FlashBagInterface $flashBag,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->postImagesDirectory = $parameterBag->get('post_images_directory');
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
    }

    public function checkUsers(string $username, User $userConnected): array
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        return [
            'userConnected' => $userConnected,
            'user' => $user,
        ];
    }

    public function checkUserAuthorization(User $user, User $userConnected): void
    {
        if ($user !== $userConnected) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à effectuer cette action');
        }
    }

    public function new(Request $request, User $userConnected): Response
    {
        $users = $this->checkUsers($userConnected->getUsername(), $userConnected);

        $userConnected = $users['userConnected'];
        $user = $users['user'];

        $this->checkUserAuthorization($user, $userConnected);

        $post = new Post();
        $form = $this->formFactory->create(PostType::class, $post)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on génère du slug à partir du titre du post
            $title = $post->getTitle();
            $slug = $this->slugger->slug($title)->replace(' ', '-')->lower();
            $post->setSlug($slug);

            // on crée un nouvel objet Topic
            $topic = new Topic();
            $topic->setPost($post);
    
            // Traitement supplémentaire (upload d'image, sauvegarde, etc.)
            $this->handlePostImageUpload($form, $post);
            $this->savePost($post, $user);
    
            // on enregistrer le topic
            $this->entityManager->persist($topic);
            $this->entityManager->flush();


            return new RedirectResponse($this->urlGenerator->generate('app_account', ['username' => $user->getUsername()]));
        }

        return new Response($this->twig->render('post/new.html.twig', [
            'user' => $user,
            'post' => $post,
            'form' => $form->createView(),
        ]));
    }

    public function editPost(Request $request, Post $post, User $userConnected): Response
    {
        // Vérifier si l'utilisateur connecté est l'auteur du post
        $this->checkUserAuthorization($post->getUser(), $userConnected);

        $form = $this->formFactory->create(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on génère du slug à partir du titre du post
            $title = $post->getTitle();
            $slug = $this->slugger->slug($title)->replace(' ', '-')->lower();
            $post->setSlug($slug);
            
            $this->handlePostImageUpload($form, $post);
            $this->savePost($post, $post->getUser());

            return new RedirectResponse($this->urlGenerator->generate('app_account', ['username' => $post->getUser()->getUsername()]));
        }
        elseif ($form->isSubmitted() && !$form->isValid()) {
            // Message d'erreur
            $this->flashBag->add('error', 'Votre publication n\'a pas pu être publiée.');
        }

        return new Response($this->twig->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'user' => $post->getUser(),
        ]));
    }
    
    public function handlePostImageUpload(FormInterface $form, Post $post): void
    {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->postImagesDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                // gestion des erreurs d'upload ici
            }

            $post->setImage($newFilename);
        }
    }

    public function savePost(Post $post, User $user): void
    {
        $post->setUser($user);
        $this->postRepository->add($post, true);
    }

    public function deletePost(Post $post, User $userConnected): void
    {
        // Vérifier si l'utilisateur connecté est l'auteur du post
        $this->checkUserAuthorization($post->getUser(), $userConnected);

        $this->postRepository->remove($post, true);
    }
}