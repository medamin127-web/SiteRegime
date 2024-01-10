<?php

namespace App\Controller;

use App\Entity\Diet;
use App\Entity\Plat;
use App\Entity\PlatDiet;
use App\Form\DietType;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\DietRepository;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;



class DietController extends AbstractController
{
    /**
     * @Route("/diet", name="app_diet")
     */
    public function index(): Response
    {
        return $this->render('diet/index.html.twig', [
            'controller_name' => 'DietController',
        ]);
    }



    /**
     * @Route("/admin/diet", name="admin_diet")
     */
    public function admin_index(DietRepository $dietRepository): Response
    {
        $diets = $dietRepository->findAll();

        return $this->render('admin_panel/diet/diet.html.twig', ['diets' => $diets]);
    }


      /**
     * @Route("/view_diet/{id}", name="view_diet")
     */
    public function viewDiet(int $id): Response
    {
        $diet = $this->getDoctrine()->getRepository(Diet::class)->find($id);

        // Check if the diet was not found
        if (!$diet) {
            throw $this->createNotFoundException('Diet not found');
        }

        return $this->render('home/view_diet.html.twig', [
            'diet' => $diet,
        ]);
    }


     /**
     * @Route("/diet_category/{categoryName}", name="diet_category")
     */
    public function dietCategory(DietRepository $dietRepository, string $categoryName): Response
    {
        $diets = $dietRepository->findByCategoryName($categoryName); // You should implement this method in your repository

        return $this->render('home/diet_category.html.twig', [
            'categoryName' => $categoryName,
            'diets' => $diets,
        ]);
    }
    /**
     * @Route("/admin/diet/new", name="diet_new")
    */
    public function new(Request $request): Response
    {
        // Fetch meals from the database to display in Step 2
        $meals = $this->getDoctrine()->getRepository(Plat::class)->findAll();
    
        $categoryEntities = $this->getDoctrine()->getRepository(Category::class)->findAll();
    
        // Create a new instance of your Diet entity
        $dietEntity = new Diet();
    
        $form = $this->createForm(DietType::class, $dietEntity, [
            'categories' => $categoryEntities,
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
              // Handle image upload
                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    // Move the file to the directory where diet images are stored
                    try {
                        $imageFile->move(
                            $this->getParameter('diet_images_directory'),
                            $newFilename
                        );
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Error uploading the image.');
                        return $this->redirectToRoute('your_failure_route'); // Redirect to a failure route or handle accordingly
                    }

                    $dietEntity->setImage($newFilename);
                }


            // Handle Step 1: Persist basic diet information
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dietEntity);
            $entityManager->flush();
            
            // Get the ID of the newly created diet
            $dietId = $dietEntity->getId();


         // Handle Step 2: Persist selected meals
         
         $selectedMealsJson = $request->request->get('selectedMeals');
         $selectedMeals = json_decode($selectedMealsJson, true);

        if (!empty($selectedMeals)) {
            foreach ($selectedMeals as $mealId) {
                $meal = $this->getDoctrine()->getRepository(Plat::class)->find($mealId);
                if ($meal) {
                    // Create and persist a new PlatDiet entity for each selected meal
                    $platDiet = new PlatDiet();
                    $platDiet->setPlat($meal);
                    $platDiet->setDiet($dietEntity);
                    $entityManager->persist($platDiet);
                }
            }
            $entityManager->flush();
        }
            
               // Redirect or handle success as needed
            $this->addFlash('success', 'Diet has been successfully created!');
            return $this->redirectToRoute('admin_diet');
        }
    
        return $this->render('admin_panel/diet/newdiet.html.twig', [
            'meals' => $meals,
            'form' => $form->createView(),
        ]);
    }


     /**
     * @Route("/admin/diet/edit/{id}", name="diet_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, DietRepository $dietRepository, PlatRepository $platRepository, EntityManagerInterface $entityManager): Response
    {
        // Fetch selected meals for the given diet
        $diet = $dietRepository->find($request->get('id'));

        $selectedMeals = $platRepository->findSelectedMealsByDiet($diet->getId());

        // Get the initial diet image
        $initialImage = $diet->getImage();

    
        // Assuming $selectedMeals is an array of Plat entities
        $selectedMealIds = array_map(function ($meal) {
            return $meal->getId();
        }, $selectedMeals);

        $categoryEntities = $this->getDoctrine()->getRepository(Category::class)->findAll();

        // Form creation and handling
        $form = $this->createForm(DietType::class, $diet, [
            'categories' => $categoryEntities,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
// Handle image upload directly in the controller
$imageFile = $form->get('image')->getData();

if ($imageFile) {
    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

    // Move the file to the directory where diet images are stored
    try {
        $imageFile->move(
            $this->getParameter('diet_images_directory'),
            $newFilename
        );
    } catch (\Exception $e) {
        $this->addFlash('error', 'Error uploading the image.');
        return $this->redirectToRoute('admin_diet');
    }

    // Remove the old image file
   

    // Set the new image file path
    $diet->setImage($newFilename);
}

// Update diet and associated meals in the database
$selectedMeals = $request->request->get('selectedMeals');
$selectedMealIds = is_array($selectedMeals) ? $selectedMeals : explode(',', $selectedMeals);
$this->updateDietAndMeals($diet, $selectedMealIds, $entityManager, $form);

// Redirect to the diet list page or any other desired route
return $this->redirectToRoute('admin_diet');
        }

        // Render the edit diet template
        return $this->render('admin_panel/diet/editdiet.html.twig', [
            'form' => $form->createView(),
            'meals' => $platRepository->findAll(), // Fetch all meals for pagination
            'selectedMeals' => $selectedMealIds,
        ]);
    }


    /**
     * Uploads an image file and returns the new filename.
     *
     * @param UploadedFile $file
     * @return string
     */
    
    private function uploadImage(\Symfony\Component\HttpFoundation\File\UploadedFile $file): string
    {
        
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();

        
        // Move the file to the directory where diet images are stored
        try {
            $file->move(
                $this->getParameter('diet_images_directory'),
                $newFilename
            );
        } catch (\Exception $e) {
            // Handle the exception, e.g., add a flash message
            $this->addFlash('error', 'Error uploading the image.');
        }

        return $newFilename;
    }
    /**
     * Update the diet and associated meals in the database.
     *
     * @param Diet $diet
     * @param array $selectedMealIds
     * @param EntityManagerInterface $entityManager
     */
    private function updateDietAndMeals(Diet $diet, array $selectedMealIds, EntityManagerInterface $entityManager, FormInterface $form): void
    {
        // Update diet information from the form
        $diet->setDietName($form->get('diet_name')->getData());
        $diet->setDuration($form->get('duration')->getData());
        $diet->setImage($form->get('image')->getData());
        $diet->setCategory($form->get('category')->getData());
        $diet->setDescription($form->get('description')->getData());
        $diet->setType($form->get('type')->getData());
    
        // Remove existing meals
        foreach ($diet->getPlatDiets() as $platDiet) {
            $entityManager->remove($platDiet);
        }

        // Update associated meals
        foreach ($selectedMealIds as $mealId) {
            // Fetch meal entity from the database
            $meal = $entityManager->getRepository(Plat::class)->find($mealId);
    
            if ($meal instanceof Plat && !$diet->getPlatDiets()->contains($meal)) {
                // Create and persist a new PlatDiet entity for each selected meal
                $platDiet = new PlatDiet();
                $platDiet->setPlat($meal);
                $platDiet->setDiet($diet);
                $entityManager->persist($platDiet);
        
                // Add the meal to the diet
                $diet->addPlatDiet($platDiet);
            }
        }
    
        // Persist and flush changes
        $entityManager->persist($diet);
        $entityManager->flush();
    }

    /**
     * @Route("/admin/diet/delete/{id}", name="diet_delete", methods={"GET"})
     */
    public function delete(int $id, EntityManagerInterface $entityManager)
    {
        $diet = $this->getDoctrine()->getRepository(Diet::class)->find($id);

        // If diet is not found, handle accordingly (e.g., redirect to an error page)
        if (!$diet) {
            $this->addFlash('error', 'Diet not found.');
            return $this->redirectToRoute('admin_diet');
        }

        // Delete related records in dietplat table
        $dietPlats = $diet->getPlatDiets();
        foreach ($dietPlats as $dietPlat) {
            $entityManager->remove($dietPlat);
        }

        // Delete the diet itself
        $entityManager->remove($diet);
        $entityManager->flush();

        $this->addFlash('success', 'Diet deleted successfully.');

        return $this->redirectToRoute('admin_diet');
    }

}
