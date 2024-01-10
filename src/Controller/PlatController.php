<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;


class PlatController extends AbstractController
{
    /**
     * @Route("/plat", name="app_plat")
     */
    public function index(): Response
    {
        return $this->render('plat/index.html.twig', [
            'controller_name' => 'PlatController',
        ]);
    }


     /**
     * @Route("admin/plat", name="admin_plat")
     */
    public function admin_index(PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findAll();

        return $this->render('admin_panel/plat/plat.html.twig', ['plats' => $plats]);

    }    



     /**
     * @Route("admin/plat/{id}/edit", name="plat_edit")
     */
    public function editPlat(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $plat = $entityManager->getRepository(Plat::class)->find($id);

        if (!$plat) {
            throw $this->createNotFoundException('Meal not found');
        }

        // Create the edit form
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the changes to the database
            $entityManager->flush();

            // Add a flash message to inform the user about the edit
            $this->addFlash('success', 'Meal edited successfully.');

            // Redirect to the meal management page after editing
            return $this->redirectToRoute('admin_plat');
        }

        return $this->render('admin_panel/plat/editplat.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
   /**
 * @Route("admin/plat/{id}", name="plat_show", requirements={"id"="\d+"})
 */
    public function show(int $id, PlatRepository $platRepository): Response
    {
        $plat = $platRepository->find($id);

        if (!$plat) {
            throw $this->createNotFoundException('Meal not found');
        }

        return $this->render('admin_panel/plat/showplat.html.twig', [
            'plat' => $plat,
        ]);
    }

    // Create new plat 
       /**
     * @Route("admin/plat/new", name="plat_new")
     */
    public function new(Request $request): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();

            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('plat_images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error uploading the image.');
                    return $this->redirectToRoute('plat_new');
                }

                $plat->setImage($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plat);
            $entityManager->flush();

            $this->addFlash('success', 'The new meal has been created successfully.');
            return $this->redirectToRoute('admin_plat');
        }

        return $this->render('admin_panel/plat/newplat.html.twig', [
            'form' => $form->createView(),
        ]);
    }


     /**
     * @Route("admin/plat/delete/{id}", name="plat_delete")
     */
    public function deletePlat($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $plat = $entityManager->getRepository(Plat::class)->find($id);

        if (!$plat) {
            throw $this->createNotFoundException('Meal not found');
        }

        $entityManager->remove($plat);
        $entityManager->flush();

        // Add a flash message to inform the user about the deletion
        $this->addFlash('success', 'Meal deleted successfully.');

        // Redirect to the meal management page after deletion
        return $this->redirectToRoute('admin_plat');
    }
}
