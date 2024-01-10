<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {

        // If the user is already authenticated, redirect to the homepage
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
         // Get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();

         // Last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
 
         return $this->render('security/login.html.twig', [
             'last_username' => $lastUsername,
             'error'         => $error,
              'username'      => $lastUsername,
         ]);
     }
 
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout() 
    {
        return $this->redirectToRoute('homepage');
    }
    /**
     * @Route("/login_check", name="app_login_check")
     */
    public function loginCheck()
    {
        // This controller will not be executed,
        // as the route is handled by the Security system.
    }

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // Get form data
            $formData = $form->getData();
            $imageFile = $form->get('image')->getData();

            // Create a new User entity
            $user = new User();
            $user->setEmail($formData['email']);
            $user->setName($formData['name']);

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('user_images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error uploading the image.');
                    return $this->redirectToRoute('app_signup');
                }

                $user->setImage($newFilename);
            }
            
           
            // Encode and set the password
            $encodedPassword = $passwordEncoder->encodePassword($user, $formData['password']);
            $user->setPassword($encodedPassword);

            // Save the User entity to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

          
            // Redirect to a success page or perform other actions
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
