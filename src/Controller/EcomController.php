<?php

namespace App\Controller;

use App\Entity\ReviewsProduct;
use App\Form\ReviewsProductType;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoriesRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ArticleCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EcomController extends AbstractController
{
    //  /**
    //  * @Route("/", name="app_ecom")
    //  */
    // public function index_ecom(ArticleCategoryRepository $articleCategoryRepository, ArticleRepository $articleRepository, ProductRepository $repoProduct, CategoriesRepository $categoriesRepository, PaginatorInterface $paginator, Request $request): Response {
    //     // récupérer les données pour la page d'accueil
    //     $query = $repoProduct->findBy(array('isActive' => true), array('id' => 'DESC'));
    //     $products = $paginator->paginate($query, $request->query->get('page', 1), 12);
    //     $categories = $categoriesRepository->findAll();
    //     $productBestSeller = $repoProduct->findBy([
    //         'isBestSeller' => true,
    //         'isActive' => true
    //     ]);
    //     $productNewArrival = $repoProduct->findBy([
    //         'isNewArrival' => true,
    //         'isActive' => true
    //     ]);
    //     $productFeatured = $repoProduct->findBy([
    //         'isFeatured' => true,
    //         'isActive' => true
    //     ]);
    //     $productSpecialOffer = $repoProduct->findBy([
    //         'isSpecialOffer' => true,
    //         'isActive' => true
    //     ]);
    //     $articles = $articleRepository->findBy(array('isActive' => true), array('id' =>'DESC'), 3, 0);
    //     $articlesCategories = $articleCategoryRepository->findAll();

    //     // return $this->redirectToRoute('app_home');
    // }

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
}
