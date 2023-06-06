<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\TopicRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    /**
     * @Route("/{id}/commentaires", name="app_topic_show")
     */
    public function show($id, TopicRepository $topicRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $topic = $topicRepository->find($id);
        if (!$topic) {
            return $this->redirectToRoute('app_home');
        }

        $post = $topic->getPost();

        $stockCommentaires = $topic->getCommentaires();

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

            return $this->redirectToRoute('app_topic_show', ['id' => $id]);
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
}
