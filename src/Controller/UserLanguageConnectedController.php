<?php

namespace App\Controller;

use App\Repository\UserLanguageConnectedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLanguageConnectedController extends AbstractController
{

    private $userLanguageConnectedRepository;

    public function __construct(UserLanguageConnectedRepository $userLanguageConnectedRepository) {

        $this->userLanguageConnectedRepository = $userLanguageConnectedRepository;
    }

    // INSERT USER LANGUAGE CONNECTED //

    #[Route('/api/user/{user_id}/language/{language_id}/connected', name: 'user_language_connected')]
    public function insertUserLanguageConnected(Request $request, $user_id, $language_id): JsonResponse
    {

        $this->userLanguageConnectedRepository->insertUserLanguageConnected($user_id, $language_id);

        return new JsonResponse([
            'flag' => true
        ]);
    }

    // DELETE USER LANGUAGE CONNECTED //

    #[Route('/api/user/{user_id}/language/{language_id}/connected/delete', name: 'user_language_connected_delete')]
    public function deleteUserLanguageConnected(Request $request, $user_id, $language_id): JsonResponse
    {

        $this->userLanguageConnectedRepository->deleteUserLanguageConnected($user_id, $language_id);

        return new JsonResponse([
            'flag' => true
        ]);
    }

    // GET USERS CONNECTED BY LANGUAGE //

    #[Route('/api/language/{language_id}/getusers', name: 'language_get_users')]
    public function getUsersConnectedByLanguage($language_id)
    {

        $result = $this->userLanguageConnectedRepository->getUsersConnectedByLanguage($language_id);

        return new JsonResponse([
            'usersOnline' => $result
        ]);
    }
}
