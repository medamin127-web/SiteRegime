<?php

namespace App\Controller;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Repository\DietRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(DietRepository $dietRepository, ArticleRepository $articleRepository): Response
    {
        // Fetch diets for each category using the custom repository method
        $weightLossDiets = $dietRepository->findByCategoryName('Weight Loss');
        $weightGainDiets = $dietRepository->findByCategoryName('Weight Gain');
        $healthyDiets = $dietRepository->findByCategoryName('Maintenance and staying healthy');


         // Fetch the latest articles
        $latestArticles = $articleRepository->findLatestArticles(3);


        return $this->render('home/index.html.twig', [
            'weightLossDiets' => $weightLossDiets,
            'weightGainDiets' => $weightGainDiets,
            'healthyDiets' => $healthyDiets,
            'latestArticles' => $latestArticles,
        ]);
    }


    

     /**
     * @Route("/admin-panel", name="admin_panel")
     */

    public function adminPanel()
    {
        // You can add any logic here to fetch data for the admin panel if needed

        return $this->render('admin_panel/index.html.twig');

    }


     /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request): Response
    {
       
        return $this->render('home/ContactUs.html.twig');
    }
}