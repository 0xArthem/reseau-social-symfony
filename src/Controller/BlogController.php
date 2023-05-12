<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ArticleCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
     /**
     * @Route("/article/{slug}", name="article")
     */
    public function article($slug, ArticleRepository $articleRepository, Request $request): Response
    {
        $article = $articleRepository->findOneBySlug($slug);
        
        return $this->render('blog/article.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/articles/category/{slug}", name="article_category")
     */
    public function article_category($slug, ArticleCategoryRepository $articleCategoryRepository, ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $articlesCategorie = $articleCategoryRepository->findOneBySlug($slug);
        $articles = $articleRepository->findByCategory($articlesCategorie);

        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('blog/article-category.html.twig', [
            'articlesCategorie' => $articlesCategorie,
            'articles' => $pagination,
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $articleRepository->findBy(array('isActive' => true), array('id' =>'DESC'));
        $articles = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            12
        );

        return $this->render('blog/articles.html.twig', [
            'articles' => $articles,
        ]);
    }
}
