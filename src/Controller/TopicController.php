<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    /**
     * @Route("/topic/{id}", name="app_topic_show")
     */
    public function show($id): Response
    {
        $topic = $this->getDoctrine()->getRepository(Topic::class)->find($id);

        if (!$topic) {
            // Si le topic n'existe pas, vous pouvez gérer l'erreur ou rediriger vers une autre page.
            // Dans cet exemple, je redirige simplement vers la page d'accueil.
            return $this->redirectToRoute('app_home');
        }

        return $this->render('topic/show.html.twig', ['topic' => $topic]);
    }
}
