<?php

namespace App\Controller;

use App\Repository\UserLanguageConnectedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateTimeZone;

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

        // get and set current date time for database //

        $newDateTime = new DateTime();
        $timeZone = new DateTimeZone('Europe/Paris');

        $newDateTime->setTimezone($timeZone);
        $newDateTimeFormat = $newDateTime->format('Y-m-d H:i:s');

        $this->userLanguageConnectedRepository->insertUserLanguageConnected($user_id, $language_id, $newDateTimeFormat, $newDateTimeFormat);

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

    // DELETE ALL BY ID -> USER LANGUAGE CONNECTED ( LOGOUT ) //
    #[Route('/api/user/{user_id}/connected/delete', name: 'user_language_connected_delete_all_by_id')]
    public function deleteUserLanguageConnectedAllById($user_id)
    {

        $this->userLanguageConnectedRepository->deleteUserLanguageConnectedAllById($user_id);

        return new Response(
            http_response_code(200)
        );
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




    // GET ALL USERS CONNECTED //

    #[Route('/api/userlanguageconnected/getall', name: 'user_language_connected_get_all')]
    public function getAllUsersConnected()
    {

        $result = $this->userLanguageConnectedRepository->getAllUsersConnected();

        $flag = null;

        try {
            self::removeUsersConnectedOffline($result);
            $flag = true;
        }
        catch (\Exception $e) {
            $flag = false;
        }

        

        return new JsonResponse([
            'flag' => $flag,
        ]);
    }


    // REMOVE USERS CONNECTED IF LAST ACTIVITY > 15MIN -> NO ROUTE //

    public function removeUsersConnectedOffline(array $allUsersConnected)
    {

        $currentDateTime = new DateTime('now');

        $currentDateTime = $currentDateTime->format('U');

        foreach ($allUsersConnected as $key => $value) {

            if ((int)$currentDateTime - \strtotime($value['updated_at']) >= 900) {

                self::deleteUserLanguageConnectedAllById($value['user_id']);
            }
        }
    }





    // UPDATE LAST ACTIVITY USER CONNECTED //

    #[Route('/api/userlanguageconnected/updateactivity', name: 'user_language_connected_update_activity')]
    public function updateActivity()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = null;
        $dateTime = null;
        $dateTimeFormat = null;
        $flag = null;

        if (!is_null($data)) {

            $user_id = $data['user_id'];
            $dateTime = $data['dateTime'];


            $dateTimeFormat = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime)->format('Y-m-d H:i:s');


            try {
                $this->userLanguageConnectedRepository->updateActivity($user_id, $dateTimeFormat);
                $flag = true;
            }
            catch (\Exception $e) {
                $flag = false;
            }

        }

        else {

            $flag = false;
        }

        return new JsonResponse([
            'flag' => $flag
        ]);
    }
}
