<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }


    /**
     * @Route("/create-article", name="create_article")
     */

    public function createArticle(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $file = $form->get('articleImage')->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('article_images_directory'), $fileName);
                $article->setArticleImage($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            // Handle successful form submission

            return $this->redirectToRoute('create_article');
        }

        return $this->render('admin_panel/Article/create_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

      /**
     * @Route("/delete-article/{id}", name="delete_article")
     */
    public function deleteArticle(Article $article): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        // Handle successful deletion

        return $this->redirectToRoute('your_success_route');
    }

    /**
     * @Route("/view-all-articles", name="view_all_articles")
     */
    public function viewAllArticles(): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('view_all_articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/view-article/{id}", name="view_article")
     */
    public function viewArticle(Article $article): Response
    {
        return $this->render('view_article.html.twig', [
            'article' => $article,
        ]);
    }


}
