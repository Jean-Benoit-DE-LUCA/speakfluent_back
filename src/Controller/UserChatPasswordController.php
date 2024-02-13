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



    // CHECK CHAT PASSWORD //

    #[Route('/api/userchatpassword/check', name: 'user_chat_password_check')]
    public function checkPassword()
    {

        $jwt = '';
        $data = null;
        $result = null;
        $pvChatId = null;
        $chatPasswordInput = null;
        $chatPasswordDatabase = null;
        $resultCheckChatPassword = null;

        // get jwt //

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
                $result = json_decode(file_get_contents('php://input'), true);


                if (!is_null($result)) {

                    $pvChatId = $result['pvChatId'];
                    $chatPasswordInput = $result['passwordInputs'];

                    $data = $this->userChatPasswordRepository->getById($pvChatId);
                }





                try {

                    if (!is_null($data)) {

                        $chatPasswordDatabase = $data[0]['user_chat_password_chat_password'];
                        $resultCheckChatPassword = $chatPasswordDatabase == $chatPasswordInput;

                    }
                }

                catch (\Exception $e) {

                    $chatPasswordDatabase = null;
                    $resultCheckChatPassword = null;
                }




                $jwtCheck = true;
            }
        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }


        return new JsonResponse([
            "data" => $data,
            "resultCheckChatPassword" => $resultCheckChatPassword,
            "jwtCheck" => $jwtCheck
        ]);
    }







    // FETCH UNIQUE CHAT PASSWORD DATA //

    #[Route('/api/userchatpassword/get/id/{chat_id}', name: 'user_chat_password_get_id')]
    public function getById($chat_id): JsonResponse
    {   

        // get jwt //

        $jwt = '';
        $data = null;

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

                $data = $this->userChatPasswordRepository->getById($chat_id)[0];

                $jwtCheck = true;
            }
        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }




        return new JsonResponse([
            'data' => $data,
            'jwt' => $jwtCheck
        ]);
    }







    // FETCH GENERAL CHAT BY ID //

    #[Route('/api/userchatpassword/get/general/id/{chat_id}', name: 'user_chat_password_get_general_id')]
    public function getByIdGeneral($chat_id)
    {
        // get jwt //

        $jwt = '';
        $data = null;

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

                $data = $this->userChatPasswordRepository->getByIdGeneral($chat_id)[0];

                $jwtCheck = true;
            }
        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }




        return new JsonResponse([
            'data' => $data,
            'jwtCheck' => $jwtCheck
        ]);
    }





    // INSERT CHAT PASSWORD //

    #[Route('/api/userchatpassword/insert', name: 'user_chat_password_insert')]
    public function insert(): JsonResponse
    {

        $passwordInputs = null;
        $userSet = null;
        $userReceive = null;
        $languageId = null;
        $chatType = null;

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
                    $chatType = $data['chatType'];
                }



                // get and set current date time for database //

                $newDateTime = new DateTime();
                $timeZone = new DateTimeZone('Europe/Paris');

                $newDateTime->setTimezone($timeZone);
                $newDateTimeFormat = $newDateTime->format('Y-m-d H:i:s');


                // ----- IF CHAT TYPE IS PRIVATE ----- //
                if ($chatType == 'private') {

                    // check length password //

                    if (strlen($passwordInputs) == 4) {


                        // insert password //
                        $this->userChatPasswordRepository->insertPassword($userSet, $userReceive, $passwordInputs, $newDateTimeFormat, $newDateTimeFormat, $userSet);

                        // fetch last insert //
                        $lastRowInserted = $this->userChatPasswordRepository->fetchPassword($userSet, $userReceive, $newDateTimeFormat);
                    }
                }


                



                // ----- IF CHAT TYPE IS GENERAL ----- //
                if ($chatType == 'general') {

                    // insert NULL password + NULL user_id + NULL user_receive -> user_owner_id = owner general chat //
                    $this->userChatPasswordRepository->insertPassword(null, null, null, $newDateTimeFormat, $newDateTimeFormat, $userSet);

                    // fetch last insert //
                    $lastRowInserted = $this->userChatPasswordRepository->fetchPasswordGeneral($userSet, $newDateTimeFormat);
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





    // UPDATE USER GENERAL CHAT //
    #[Route('/api/userchatpassword/updateuser/{chat_id}', name: 'user_chat_password_update_user')]
    public function updateUserGeneralChatPassword($chat_id)

    {

        $data = null;
        $addUser = null;
        $fetchGeneralChat = null;
        $fetchGeneralChatResult = null;

        $fetchGeneralChatUserId = null;
        $fetchGeneralChatUserReceive = null;


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

                    $addUser = $data['addUser'];
                }



                // check NULL user field in current general chat id //

                $fetchGeneralChat = $this->userChatPasswordRepository->getByIdGeneral($chat_id);

                if (!is_null($fetchGeneralChat)) {

                    $fetchGeneralChat = $fetchGeneralChat[0];

                    if (!is_null($fetchGeneralChat)) {

                        $fetchGeneralChatUserId = $fetchGeneralChat['user_id'];
                        $fetchGeneralChatUserReceive = $fetchGeneralChat['user_receive'];


                        // if user_id NULL //

                        if ($fetchGeneralChatUserId == null) {

                            $this->userChatPasswordRepository->updateUserGeneralChatPasswordUserId($addUser, $chat_id);

                            $jwtCheck = true;

                            return new JsonResponse([
                                'jwtCheck' => $jwtCheck
                            ]);
                        }

                        

                        // if user_receive NULL //

                        else if ($fetchGeneralChatUserReceive == null) {

                            $jwtCheck = true;

                            // check if user not already registered to chat //

                            if ($addUser !== $fetchGeneralChatUserId) {

                                $this->userChatPasswordRepository->updateUserGeneralChatPasswordUserReceive($addUser, $chat_id);


                                return new JsonResponse([
                                    'jwtCheck' => $jwtCheck
                                ]);
                            }

                            else if ($addUser == $fetchGeneralChatUserId) {

                                return new JsonResponse([
                                    'jwtCheck' => $jwtCheck,
                                    'alreadyRegistered' => true
                                ]);
                            }

                            else {

                                return new JsonResponse([
                                    'jwtCheck' => $jwtCheck,
                                    'error' => 'User already registered for this chat'
                                ]);
                            }

                            
                        }
                    }
                }
                
                $jwtCheck = true;
            }
        }

        catch (\Exception $e) {

            $jwtCheck = $e->getMessage();
        }

        return new JsonResponse([
            'fetchGeneralChatUserId' => $fetchGeneralChatUserId,
            'fetchGeneralChatUserReceive' => $fetchGeneralChatUserReceive,
            'jwtCheck' => $jwtCheck
        ]);
    }




}
