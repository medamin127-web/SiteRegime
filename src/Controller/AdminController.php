<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin_management", name="admin_management")
     */
    public function adminManagement(UserRepository $userRepository): Response
    {
        // Fetch super admin and admins
        $superAdmin = $userRepository->findBySuperAdminRoles();
        $admins = $userRepository->findByAdminRoles();
        
      
        // Fetch normal users without admin roles
        $normalUsers = $userRepository->findByNotAdminRoles();

    
        return $this->render('admin_panel/Admins/admin_management.html.twig', [
            'superAdmin' => $superAdmin,
            'admins' => $admins,
            'normalUsers' => $normalUsers,
        ]);
    }

    /**
     * @Route("/promote_admin/{id}", name="promote_admin")
     */
    public function promoteAdmin(User $user): Response
    {
        // Promote user to admin
        $user->setRoles(['ROLE_ADMIN']);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Admin promoted successfully.');

        return $this->redirectToRoute('admin_management');
    }

    /**
     * @Route("/delete_admin/{id}", name="delete_admin")
     */
    public function deleteAdmin(int $id, EntityManagerInterface $entityManager): Response
    {

        // Fetch the user from the database using the ID
        $user = $entityManager->getRepository(User::class)->find($id);
        
        // Remove admin role
        $user->setRoles([]);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Admin deleted successfully.');

        return $this->redirectToRoute('admin_management');
    }

    /**
     * @Route("/make_user_admin/{id}", name="make_user_admin")
     */
    public function makeUserAdmin(int $id, EntityManagerInterface $entityManager): Response
    {
        // Fetch the user from the database using the ID
        $user = $entityManager->getRepository(User::class)->find($id);

        // Check if the user exists
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Make user an admin
        $user->setRoles(['ROLE_ADMIN']);
        $entityManager->flush();

        $this->addFlash('success', 'User is now an admin.');

        return $this->redirectToRoute('admin_management');
    }
}