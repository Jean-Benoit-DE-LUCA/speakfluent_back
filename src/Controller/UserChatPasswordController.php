<?php

namespace App\Controller;

use App\JWT\JwtClass;
use App\Repository\UserChatPasswordRepository;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserChatPasswordController extends AbstractController
{

    protected $userChatPasswordRepository;

    public function __construct(
        UserChatPasswordRepository $userChatPasswordRepository
    ) {
        $this->userChatPasswordRepository = $userChatPasswordRepository;
    }


    #[Route('/api/userchatpassword/insert', name: 'user_chat_password_insert')]
    public function insert(): JsonResponse
    {

        $passwordInputs = null;
        $userSet = null;
        $userReceive = null;
        $languageId = null;

        $lastRowInserted = null;

        // get jwt token //

        $jwt = '';

        foreach (getallheaders() as $key => $value) {

            if ($key == 'Authorization') {

                $jwt .= $value;
            }
        }

        // decode jwt //

        $jwtObj = new JwtClass();
        $jwtCheck = $jwtObj->decodeJwt(str_replace('Bearer ', '', $jwt));






        // if token has not expired and is ok //

        try {

            if ($jwtCheck->iat !== null) {


                // get body request //

                $data = json_decode(file_get_contents('php://input'), true);


                if (!is_null($data)) {

                    $passwordInputs = $data['passwordInputs'];
                    $userSet = $data['userSet'];
                    $userReceive = $data['userReceive'];
                    $languageId = $data['languageId'];
                }



                // get and set current date time for database //

                $newDateTime = new DateTime();
                $timeZone = new DateTimeZone('Europe/Paris');

                $newDateTime->setTimezone($timeZone);
                $newDateTimeFormat = $newDateTime->format('Y-m-d H:i:s');


                // check length password //

                if (strlen($passwordInputs) == 4) {


                    // insert password //
                    $this->userChatPasswordRepository->insertPassword($userSet, $userReceive, $passwordInputs, $newDateTimeFormat, $newDateTimeFormat);

                    // fetch last insert //
                    $lastRowInserted = $this->userChatPasswordRepository->fetchPassword($userSet, $userReceive, $newDateTimeFormat);
                }


                $jwtCheck = true;

                
            }
        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }

        


        return new JsonResponse([
            'jwtCheck' => $jwtCheck,
            'lastRowInserted' => $lastRowInserted
        ]);
    }
}
