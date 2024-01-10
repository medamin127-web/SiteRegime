<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("categories", name="categories")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }



   

    /**
     * @Route("/categories/{id}/edit", name="category_edit")
     */
    public function edit(Request $request, Category $category): Response
    {
        // You may want to add a form here to handle category editing

        return $this->render('category/edit.html.twig', ['category' => $category]);
    }

      /**
     * @Route("/categories/{id}", name="category_delete")
     */
    public function delete($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $entityManager->remove($category);
        $entityManager->flush();

       

        // Add a flash message for successful deletion
        $this->addFlash('success', 'Category deleted successfully');

          // Redirect to the meal management page after deletion
          return $this->redirectToRoute('admin_categories');
        }
        
    



    /**
     * @Route("admin/categories", name="admin_categories")
     */
    public function admin_index(Request $request): Response
    {   
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin_panel/category/category.html.twig', ['categories' => $categories , 'form' => $form->createView()]);
    }


    /**
     * @Route("/admin/categories/new", name="category_new")
    */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the new category to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            // Add a flash message
            $this->addFlash('success', 'Category added successfully');

            return $this->redirectToRoute('admin_categories');
        }

        // Render the form in the category.html.twig template
        return $this->render('admin_panel/category/category.html.twig', [         
            'form' => $form->createView(),
            
        ]);
    }
    
    
}
