<?php

namespace App\Controller;

use App\Entity\UserDetails;
use App\Form\UserDetailsType;
use App\Entity\User;
use App\Entity\PlatDiet;
use App\Entity\Diet;
use App\Entity\Plat;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;



class CustomizeDietController extends AbstractController
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/customize/diet", name="generate_diet")
     */
    public function index(Request $request): Response
    {


        $userDetails = new UserDetails();
        $form = $this->createForm(UserDetailsType::class, $userDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Perform calculations and diet generation here
            // ...

            return $this->redirectToRoute('generate_diet');
        }

            
        return $this->render('home/generate_diet.html.twig', [
            'form' => $form->createView(),
            'currentStep' => 1
        ]);
    }

     /**
 * @Route("/save-user-details", name="save_user_details", methods={"GET", "POST"})
 */
public function saveUserDetails(EntityManagerInterface $entityManager,Request $request): Response
{
    // Retrieve data from the request
    $data = json_decode($request->getContent(), true);

    
   // Retrieve data from the POST request
   $age = $request->request->get('age');
   $gender = $request->request->get('gender');
   $weight = $request->request->get('weight');
   $height = $request->request->get('height');
   $userId = $request->request->get('userId');
   $userCalories = $request->request->get('userCalories');
   $caloriesRange = $request->request->get('caloriesRange');
   $userDuration = $request->request->get('userDuration');
   $userPreference = $request->request->get('userPreference');
   $userGoal = $request->request->get('userGoal');

    

    // Example: Save data to the database
    $entityManager = $this->getDoctrine()->getManager();
    $userDetails = new UserDetails();
    $userDetails->setAge($age);
    $userDetails->setGender($gender);
    $userDetails->setWeight($weight);
    $userDetails->setHeight($height);



    // Retrieve the User entity based on the provided ID
    $userEntity = $entityManager->getRepository(User::class)->find($userId);

    // Set the User entity in UserDetails
    $userDetails->setUser($userEntity);

    $entityManager->persist($userDetails);
    $entityManager->flush();


     // Organize diets and calculate total calories
     $organizedDiets = $this->organizeDiets($entityManager);

     // Filter diets based on total calories
     $filteredDiets = $this->filterDietsByCalories($organizedDiets, $userCalories, $caloriesRange, $userDuration, $userPreference, $userGoal);

     // Perform additional filtering
    $newFilteredDietIds = $this->filterAdditionalDiets($organizedDiets, $userGoal, $userPreference, $userCalories);
   
     $selectedDietIds = array_map(function ($diet) {
        return $diet['diet']->getId();
    }, $filteredDiets);

   

        // Redirect to the generate_result route with the selected and new filtered diet IDs
        return $this->redirectToRoute('generate_result', [
            'selectedDietIds' => $selectedDietIds,
            'newFilteredDietIds' => $newFilteredDietIds,
            'goal' => $userGoal,
            'user_calories' => $userCalories,
            'userDuration' => $userDuration
        ]);
}


 /**
     * @Route("/generate-result", name="generate_result")
     */
    public function generateResult(Request $request, EntityManagerInterface $entityManager): Response
    {
        $selectedDietIds = $request->query->get('selectedDietIds', []);
        $newFilteredDietIds = $request->query->get('newFilteredDietIds', []);

          // Fetch the first diet entity
        $firstDietId = !empty($selectedDietIds) ? reset($selectedDietIds) : null;
        $diet = $firstDietId ? $entityManager->getRepository(Diet::class)->find($firstDietId) : null;

        $newFilteredDiets = $this->getDoctrine()->getRepository(Diet::class)->findById($newFilteredDietIds);

        $userGoal= $request->query->get('goal');
        $userCalories= $request->query->get('user_calories');
        $userDuration= $request->query->get('userDuration');

        return $this->render('home/generate_result.html.twig', [
            'selectedDietIds' => $selectedDietIds,
            'newFilteredDiets' => $newFilteredDiets,
            'goal' => $userGoal,
            'user_calories' => $userCalories,
            'userDuration' => $userDuration,
            'diet' => $diet,
        ]);
    }

  /**
     * @param array $organizedDiets
     * @param string|null $userGoal
     * @param string|null $userPreference
     * @param int $userCalories
     * @return array
     */
    private function filterAdditionalDiets(array $organizedDiets, ?string $userGoal, ?string $userPreference, int $userCalories): array
    {
        $newFilteredDietIds = [];

        foreach ($organizedDiets as $organizedDiet) {
            $diet = $organizedDiet['diet'];
            $dietCalories = $organizedDiet['totalCalories'];

            // Check if the diet's category matches the user's goal
            if ($userGoal !== null && $diet->getCategory()->getCategoryName() === $userGoal) {
                $newFilteredDietIds[] = $diet->getId();
            }

            // Check if the diet's type matches the user's preference
            if ($userPreference !== null && $diet->getType() === $userPreference) {
                $newFilteredDietIds[] = $diet->getId();
            }

            // Check if the absolute difference between total calories and user calories is 150
            if (abs($dietCalories - $userCalories) === 150) {
                $newFilteredDietIds[] = $diet->getId();
            }
        }

        // Remove duplicates from the array
        $newFilteredDietIds = array_unique($newFilteredDietIds);

        return $newFilteredDietIds;
    }


private function organizeDiets(EntityManagerInterface $entityManager): array
{
    $organizedDiets = [];

    // Fetch PlatDiet entities
    $platDiets = $entityManager->getRepository(PlatDiet::class)->findAll();

    // Organize diets and calculate total calories
    foreach ($platDiets as $platDiet) {
        $dietId = $platDiet->getDiet()->getId();

        // Check if diet ID is already in the array
        if (!isset($organizedDiets[$dietId])) {
            $organizedDiets[$dietId] = [
                'diet' => $platDiet->getDiet(),
                'totalCalories' => 0,
            ];
        }

        // Calculate calories based on meal category (you need to implement this logic)
        $calories = $this->calculateCaloriesForPlat($platDiet->getPlat());

        // Add calories to the total for the diet
        $organizedDiets[$dietId]['totalCalories'] += $calories;
    }

    return $organizedDiets;
}

private function calculateCaloriesForPlat(Plat $plat): int
{
    // Use the nbrCalories property of the Plat entity
    $calories = $plat->getNbrCalories();

    // Ensure that the calories value is not null
    if ($calories === null) {
        // You may want to handle the case where calories are not set for a meal
        // For now, we'll return 0, but you can adjust this based on your requirements.
        return 0;
    }

    return $calories;
}

private function filterDietsByCalories(array $organizedDiets, int $userCalories, int $caloriesRange, string $userDuration, ?string $userPreference, ?string $userGoal): array
{
    // Convert the user's selected duration range into two separate values (min and max)
    list($userMinDuration, $userMaxDuration) = array_map('intval', explode('-', $userDuration));

    $filteredDiets = array_filter($organizedDiets, function (array $organizedDiet) use ($userCalories, $caloriesRange, $userMinDuration, $userMaxDuration, $userPreference, $userGoal) {
        $totalCalories = $organizedDiet['totalCalories'];

        // Check if the diet's total calories are within the acceptable range (+/- 100)
        $caloriesDifference = abs($totalCalories - $userCalories);

        // Check if the calories are within +/- 100 range
        if ($caloriesDifference > $caloriesRange) {
            return false;
        }

        // Extract the duration value from the diet's duration range
        $dietDuration = intval($organizedDiet['diet']->getDuration());

        // Check if the diet's duration is within the user's selected range
        if ($dietDuration < $userMinDuration || $dietDuration > $userMaxDuration) {
            return false;
        }

        // Check if $userPreference is set and if the diet's preference matches the user's selected preference
        if ($userPreference !== null && $organizedDiet['diet']->getType() !== $userPreference) {
            return false;
        }

        // Check if $userGoal is set and if the diet's goal matches the user's selected goal
        if ($userGoal !== null && $organizedDiet['diet']->getCategory()->getCategoryName() !== $userGoal) {
            return false;
        }

        // Additional filtering criteria can be added based on your requirements

        // If all conditions are met, include the diet in the filtered list
        return true;
    });

    return $filteredDiets;
}





}



