<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the form submission, e.g., save user data to the database
            // You can access the form data using $form->getData()

            // Redirect to a success page or perform other actions
            return $this->redirectToRoute('homepage');
        }

        return $this->render('signup/signup.html.twig', [
            'form' => $form->createView(), // Pass the form to the template
        ]);
    }
}
