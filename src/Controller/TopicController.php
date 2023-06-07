<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    /**
     * @Route("/commentaires/{slug}", name="app_topic_show")
     */
    public function show($slug, PostRepository $postRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $post = $postRepository->findOneBySlug($slug);
        if (!$post) {
            return $this->redirectToRoute('app_home');
        }
        $topic = $post->getTopic();
        if (!$topic) {
            return $this->redirectToRoute('app_home');
        }
        $post = $topic->getPost();

        $stockCommentaires = $topic->getActiveCommentaires();
        $query = $stockCommentaires;
        $page = $request->query->get('page', 1);
        $commentaires = $paginator->paginate(
            $query,
            $page,
            15
        );

        $commentaire = new Commentaire();
        $commentaire->setTopic($topic);
        $commentaire->setUser($this->getUser());
        $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
        $commentaireForm->handleRequest($request);
        if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_topic_show', ['slug' => $slug]);
        }
        if ($commentaireForm->isSubmitted() && !$commentaireForm->isValid()) {
            $this->addFlash('error', 'Oups, votre commentaire n\'a pas pu être envoyé. Veuillez réessayer.');
        }

        return $this->render('topic/index.html.twig', [
            'topic' => $topic,
            'post' => $post,
            'commentaires' => $commentaires,
            'commentaireForm' => $commentaireForm->createView(),
        ]);
    }

    /**
     * @Route("/commentaire/{id}/supprimer", name="app_commentaire_delete")
     */
    public function deleteCommentaire(Commentaire $commentaire, Request $request): Response
    {
        // on vérifie si l'utilisateur connecté est l'auteur du commentaire
        if ($commentaire->getUser() === $this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            $commentaire->setIsActive(false);
            $entityManager->flush();
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        // on redirige l'utilisateur vers la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
