<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    /**
     * @Route("/commentaires/{id}", name="app_topic_show")
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

        return $this->render('topic/index.html.twig', [
            'topic' => $topic,
            'post' => $post,
            'commentaires' => $commentaires
        ]);
    }
}
