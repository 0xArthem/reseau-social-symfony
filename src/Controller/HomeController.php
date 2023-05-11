<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\ReviewsProduct;
use App\Entity\ArticleCategory;
use App\Entity\Post;
use App\Form\ReviewsProductType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoriesRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ArticleCategoryRepository;
use App\Repository\PostTagRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Orm\EntityPaginatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(
        ArticleCategoryRepository $articleCategoryRepository, 
        ArticleRepository $articleRepository, 
        ProductRepository $repoProduct, 
        CategoriesRepository $categoriesRepository,
        PaginatorInterface $paginator, 
        Request $request, 
        Security $security,
        UserRepository $userRepository,
        PostRepository $postRepository,
        PostTagRepository $postTagRepository
    ): Response {
        // récupérer l'utilisateur actuellement connecté
        $user = $security->getUser();

        // $allUsers = $userRepository->findAll();
    
        // récupérer les données pour la page d'accueil
        $query = $repoProduct->findBy(array('isActive' => true), array('id' => 'DESC'));
        $products = $paginator->paginate($query, $request->query->get('page', 1), 12);
        $categories = $categoriesRepository->findAll();
        $productBestSeller = $repoProduct->findBy([
            'isBestSeller' => true,
            'isActive' => true
        ]);
        $productNewArrival = $repoProduct->findBy([
            'isNewArrival' => true,
            'isActive' => true
        ]);
        $productFeatured = $repoProduct->findBy([
            'isFeatured' => true,
            'isActive' => true
        ]);
        $productSpecialOffer = $repoProduct->findBy([
            'isSpecialOffer' => true,
            'isActive' => true
        ]);
        $articles = $articleRepository->findBy(array('isActive' => true), array('id' =>'DESC'), 3, 0);
        $articlesCategories = $articleCategoryRepository->findAll();

       if ($user) {
            // Affichage des posts des abonnements de l'utilisateur connecté //
            // on récupére les abonnements de l'utilisateur actuel
            $abonnements = $user->getAbonnements();
            $usersAbonnement = [];
            // récupérer les utilisateurs auxquels l'utilisateur actuel est abonné
            foreach ($abonnements as $abonnement) {
                $usersAbonnement[] = $abonnement->getAbonnement();
            }
            // et on récupère les posts des utilisateurs abonnés
            $posts = $postRepository->findBy(['user' => $usersAbonnement], ['id' => 'DESC']);

            $postTags = $postTagRepository->findAll();

             // récupère l'utilisateur du compte visité à partir de son nom d'utilisateur dans l'URL
            return $this->render('home/index.html.twig', [
                'user' => $user,
                // TL
                'posts' => $posts,
                'abonnements' => $abonnements,
                'postTags' => $postTags
            ]);
       }
       else {
        $posts = $postRepository->findBy(array(), array('id' => 'DESC'));
        $postTags = $postTagRepository->findAll();

        return $this->render('home/index-other.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags
        ]);
       }
    }
    
    /**
     * @Route("/posts/recherche/tag/{slug}", name="posts_tag")
     */
    public function byPostTag($slug, PostTagRepository $postTagRepository): Response
    {
        $postTag = $postTagRepository->findOneBySlug($slug);
        $posts = $postTag->getPosts();
        $postTags = $postTagRepository->findAll();


        return $this->render('home/byTag.html.twig', [
            'posts' => $posts,
            'postTag' => $postTag,
            'postTags' => $postTags
        ]);
    }

    /**
     * @Route("/posts/recherche", name="abonnements_post_search")
     */
    public function searchAbonnementsPosts(Request $request, PostRepository $postRepository, PostTagRepository $postTagRepository): Response
    {
        $query = $request->query->get('q');
        $posts = $postRepository->searchByPost($query);

        $postTags = $postTagRepository->findAll();

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags
        ]);
    }

     /**
     * @Route("/search", name="search_products")
     */
    public function searchProducts(Request $request, ProductRepository $productRepository)
    {
        $searchTerm = $request->query->get('search');
        $products = $productRepository->createQueryBuilder('p')
            ->where('p.name LIKE :searchTerm OR p.description LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%')
            ->getQuery()
            ->getResult();

        return $this->render('home/search.html.twig', [
            'products' => $products,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * @Route("/product/category/{slug}", name="product_category")
     */
    public function product_category($slug, ProductRepository $repoProduct, CategoriesRepository $categoriesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $category = $categoriesRepository->findOneBySlug($slug);

        $query = $repoProduct->createQueryBuilder('p')
        ->leftJoin('p.category', 'c')
        ->where('c.id = :category_id')
        ->andWhere('p.isActive = :isActive')
        ->setParameter('category_id', $category->getId())
        ->setParameter('isActive', true)
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();

        $products = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('home/product-category.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }

     /**
     * @Route("/product/{slug}", name="product")
     */
    public function product($slug, ProductRepository $repoProduct, Request $request): Response
    {
        $product = $repoProduct->findOneBySlug($slug);

        $review = new ReviewsProduct();
        $review->setProduct($product);
        $form = $this->createForm(ReviewsProductType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $review = $form->getData();
            $review->setUser($user);
            $review->setProduct($product);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
        }
        
        return $this->render('home/product.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

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
