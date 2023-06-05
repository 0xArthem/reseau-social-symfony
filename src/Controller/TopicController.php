<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    /**
     * @Route("/topic/{id}", name="app_topic_show")
     */
    public function show($id, TopicRepository $topicRepository): Response
    {
        $topic = $topicRepository->find($id);

        if (!$topic) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('topic/index.html.twig', ['topic' => $topic]);
    }
}
