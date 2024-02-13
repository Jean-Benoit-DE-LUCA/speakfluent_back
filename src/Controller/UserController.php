<?php

namespace App\Controller;

use App\JWT\JwtClass;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

use DateTime;
use DateTimeZone;

class UserController extends AbstractController
{
    protected $userRepository;
    protected $userRoleRepository;

    public function __construct(
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository
    ) {

        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
    }






    // SIGN IN USER //

    #[Route('/api/user/find', name: 'user_find')]
    public function find(Request $request): JsonResponse
    {

        $data = json_decode(file_get_contents('php://input'), true);

        $email = htmlspecialchars($data['email']);
        $password = htmlspecialchars($data['password']);

        // CHECK USER //

        $findUserByMail = $this->userRepository->findUserByMail(
            $email
        );

        if (!empty($findUserByMail)) {

            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'bcrypt']
            ]);

            $hasher = $factory->getPasswordHasher('common');

            if ($hasher->verify($findUserByMail[0]['password'], $password)) {

                $jwtObj = new JwtClass();
                $findUserByMail[0]['jwt'] = $jwtObj->encodeJwt();

                return new JsonResponse([
                    'user' => $findUserByMail[0]
                ]);
            }

            else {

                return new JsonResponse([
                    'password' => false
                ]);
            }
        }

        else {

            return new JsonResponse([
                'user' => false
            ]);
        }
    }






    // REGISTER USER //

    #[Route('/api/user/insert', name: 'user_insert')]
    public function insert(Request $request): JsonResponse
    {

        $flag = null;

        $photoUploaded = null;
        $message = null;

        $findUserByMail = null;

        // photo file //
        $photo = null;
        $fileName = null;
        $fileTmpName = null;
        $fileSize = null;
        $fileType = null;
        $newDateTime = null;
        $actualFileNameUploaded = null;
        

        // post data //
        $name = htmlspecialchars($_POST['name']);
        $firstname = htmlspecialchars($_POST['firstname']);
        $email = htmlspecialchars($_POST['email']);
        $birthdate = htmlspecialchars($_POST['birthdate']);
        $gender = htmlspecialchars($_POST['gender']);
        $address = htmlspecialchars($_POST['address']);
        $zip = htmlspecialchars($_POST['zip']);
        $city = htmlspecialchars($_POST['city']);
        $password = htmlspecialchars($_POST['password']);

        // add calculate age to result //

        $age = date_diff(date_create($birthdate), date_create('now'))->y;

        if ($age < 15) {

            return new JsonResponse([
                'flag' => false,
                'message' => 'You must be at least 15 years old'
            ]);
        }

        // HASH PASSWORD //

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt']
        ]);

        $hasher = $factory->getPasswordHasher('common');
        $password_hash = $hasher->hash($password);



        // MANAGE FILE PHOTO USER //

        if (count($_FILES) > 0) {

            $folderUpload = "../public/assets/pictures/";

            $photo = $_FILES['photo'];
            $fileName = $_FILES['photo']['name'];
            $fileTmpName = $_FILES['photo']['tmp_name'];
            $fileSize = $_FILES['photo']['size'];
            $fileType = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

            if ($fileType == 'jpg' || $fileType == 'jpeg' || $fileType == 'gif' || $fileType == 'png' || $fileType == 'svg' || $fileType == 'bmp') {

                if ($fileSize < 3000000) {

                    $newDateTime = new DateTime();
                    $timeZone = new DateTimeZone('Europe/Paris');

                    $newDateTime->setTimezone($timeZone);
                    $newDateTimeFormat = $newDateTime->format('Y-m-d_H-i-s');

                    move_uploaded_file($fileTmpName, $folderUpload . $newDateTimeFormat . "_" . $fileName);
                    $photoUploaded = true;
                }

                else {

                    $photoUploaded = false;
                    $message = 'File too big (max: 3mb)';

                    return new JsonResponse([
                        'flag' => $photoUploaded,
                        'message' => $message
                    ]);
                }
            }

            else {

                $photoUploaded = false;
                $message = 'File extension not accepted (only .jpg, .gif, .png, .svg or .bmp)';

                return new JsonResponse([
                    'flag' => $photoUploaded,
                    'message' => $message
                ]);
            }
        }

        else if (count($_FILES) == 0) {

            $photoUploaded = null;
        }





        // IF USER DOESN'T UPLOAD PHOTO OR UPLOAD WITH SUCCESS

        if ($photoUploaded == null || $photoUploaded == true) {


            // CHECK USER BEFORE INSERT //

            $findUserByMail = $this->userRepository->findUserByMail(
                $email
            );

            if (empty($findUserByMail)) {

                if ($photoUploaded == true) {

                    $actualFileNameUploaded =  $newDateTimeFormat . "_" . $fileName;
                }

                else if ($photoUploaded == null) {

                    $actualFileNameUploaded = null;
                }

                // INSERT USER //

                $insertLastId = $this->userRepository->insertUser(
                    $name,
                    $firstname,
                    $email,
                    $birthdate,
                    $gender,
                    $address,
                    $zip,
                    $city,
                    $password_hash,
                    $actualFileNameUploaded
                );

                // INSERT USER_ROLE //

                $insertUserRole = $this->userRoleRepository->insertUserRole(
                    $insertLastId, 2
                );

                $flag = true;
            }

            else if (!empty($findUserByMail)) {

                $flag = false;
            }
        }




        return new JsonResponse([
            'flag' => $flag,
            'message' => $message
        ]);
    }






    // UPDATE USER //

    #[Route('/api/user/update', name: 'user_update')]
    public function updateUser() {

        /*$jwtCheck = null;
        $data = null;

        $name = null;
        $firstname = null;
        $email = null;
        $birthdate = null;
        $gender = null;
        $address = null;
        $zip = null;
        $city = null;
        $password = null;
        $user_id = null;*/



        $photoUploaded = null;
        $message = null;

        $jwtCheck = null;
        
        // photo file //
        $photo = null;
        $fileName = null;
        $fileTmpName = null;
        $fileSize = null;
        $fileType = null;
        $newDateTime = null;
        $actualFileNameUploaded = null;
        

        // post data //
        $name = htmlspecialchars($_POST['name']);
        $firstname = htmlspecialchars($_POST['firstname']);
        $email = htmlspecialchars($_POST['email']);
        $birthdate = htmlspecialchars($_POST['birthdate']);
        $gender = htmlspecialchars($_POST['gender']);
        $address = htmlspecialchars($_POST['address']);
        $zip = htmlspecialchars($_POST['zip']);
        $city = htmlspecialchars($_POST['city']);
        $password = htmlspecialchars($_POST['password']);
        $jwt = $_POST['jwt'];
        $user_id = htmlspecialchars($_POST['user_id']);
        $removeFile = $_POST['removeFile'];







        // decode jwt //

        $jwtObj = new JwtClass();
        $jwtCheck = $jwtObj->decodeJwt(str_replace('Bearer ', '', $jwt));






        // if token has not expired and is ok //

        try {

            if ($jwtCheck->iat !== null) {


                    // hash password //

                    $factory = new PasswordHasherFactory([
                        'common' => ['algorithm' => 'bcrypt']
                    ]);
            
                    $hasher = $factory->getPasswordHasher('common');
                    $password_hash = $hasher->hash($password);


                    // MANAGE FILE PHOTO USER //

                    if (count($_FILES) > 0) {

                        $folderUpload = "../public/assets/pictures/";
            
                        $photo = $_FILES['photo'];
                        $fileName = $_FILES['photo']['name'];
                        $fileTmpName = $_FILES['photo']['tmp_name'];
                        $fileSize = $_FILES['photo']['size'];
                        $fileType = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            
                        if ($fileType == 'jpg' || $fileType == 'jpeg' || $fileType == 'gif' || $fileType == 'png' || $fileType == 'svg' || $fileType == 'bmp') {
            
                            if ($fileSize < 3000000) {
            
                                $newDateTime = new DateTime();
                                $timeZone = new DateTimeZone('Europe/Paris');
            
                                $newDateTime->setTimezone($timeZone);
                                $newDateTimeFormat = $newDateTime->format('Y-m-d_H-i-s');
            
                                move_uploaded_file($fileTmpName, $folderUpload . $newDateTimeFormat . "_" . $fileName);
                                $photoUploaded = true;

                                $actualFileNameUploaded = $newDateTimeFormat . "_" . $fileName;



                                // remove old file //

                                $findUserById = $this->userRepository->findUserById(
                                    $user_id
                                );

                                if ($findUserById[0]['photo'] !== null) {

                                    unlink($folderUpload . $findUserById[0]['photo']);
                                }






                                // update data user database //

                                $this->userRepository->updateUser($name, $firstname, $email, $birthdate, $gender, $address, $zip, $city, $password_hash, $actualFileNameUploaded, $user_id);

                                $jwtCheck = true;




                                // get updated user data //

                                $findUserByIdUpdated = $this->userRepository->findUserById(
                                    $user_id
                                );

                                return new JsonResponse([
                                    'flag' => $photoUploaded,
                                    'user' => $findUserByIdUpdated[0]
                                ]);
                            }
            
                            else {
            
                                $photoUploaded = false;
                                $message = 'File too big (max: 3mb)';
            
                                return new JsonResponse([
                                    'flag' => $photoUploaded,
                                    'message' => $message
                                ]);
                            }
                        }

                        else {

                            $photoUploaded = false;
                            $message = 'File extension not accepted (only .jpg, .gif, .png, .svg or .bmp)';
            
                            return new JsonResponse([
                                'flag' => $photoUploaded,
                                'message' => $message
                            ]);
                        }
                    }



                    else if (count($_FILES) == 0) {


                        // if no need to update photo column //

                        if ($removeFile == "no") {



                            // update data user database //

                            $this->userRepository->updateUserNoPhoto($name, $firstname, $email, $birthdate, $gender, $address, $zip, $city, $password_hash, $user_id);




                            // get updated user data //

                            $findUserByIdUpdated = $this->userRepository->findUserById(
                                $user_id
                            );

                            $jwtCheck = true;

                            return new JsonResponse([
                                'flag' => $jwtCheck,
                                'user' => $findUserByIdUpdated[0]
                            ]);
                        }


                        // if need to update photo column to null //

                        else if ($removeFile == "yes") {

                            $findUserById = $this->userRepository->findUserById(
                                $user_id
                            );

                            $folderUpload = "../public/assets/pictures/";

                            if ($findUserById[0]['photo'] !== null) {


                                // remove file from server //

                                unlink($folderUpload . $findUserById[0]['photo']);
                            }




                            // update data user database //

                            $this->userRepository->updateUser($name, $firstname, $email, $birthdate, $gender, $address, $zip, $city, $password_hash, null, $user_id);




                            // get updated user data //

                            $findUserByIdUpdated = $this->userRepository->findUserById(
                                $user_id
                            );

                            $jwtCheck = true;

                            return new JsonResponse([
                                'flag' => $jwtCheck,
                                'user' => $findUserByIdUpdated[0]
                            ]);
                        }
                    }
                }
            
        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }



        return new JsonResponse([
            'flag' => $jwtCheck
        ]);
    }






    // FIND USER BY ID //

    #[Route('/api/user/find/id/{user_id}', name: 'user_find_id')]
    public function findUserById($user_id) {

        $findUserById = null;


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

                // find user //

                $findUserById = $this->userRepository->findUserById($user_id)[0];




                // add calculate age to result //

                $age = date_diff(date_create($findUserById['birthdate']), date_create('now'));

                $findUserById['age'] = $age->y;




                $jwtCheck = true;

            }

        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }




        // return json response //

        return new JsonResponse([
            'userObj' => $findUserById,
            'jwtCheck' => $jwtCheck
        ]);
    }





    // DELETE USER //

    #[Route('/api/user/delete/{user_id}', name: 'user_delete_id')]
    public function deleteUser($user_id) {

        $jwtCheck = null;
        $jwt = null;




        // get jwt token //

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

                try {

                    // find user to check if NULL picture otherwise -> DELETE //

                    $findUserById = $this->userRepository->findUserById($user_id)[0];

                    if ($findUserById['photo'] !== null) {

                        $folderUpload = "../public/assets/pictures/";

                        unlink($folderUpload . $findUserById['photo']);
                    }



                    // delete user //


                    $this->userRepository->deleteUser($user_id);
                    $jwtCheck = true;
                }

                catch (\Exception $e) {

                    $jwtCheck = false;
                }
            }
        }

        catch (\Exception $e) {

            $jwtCheck = false;
        }



        return new JsonResponse([
            'flag' => $jwtCheck
        ]);
    }





    // CHECK PASSWORD //

    #[Route('/api/user/check/password/{user_id}', name: 'user_check_password_user_id')]
    public function checkPassword($user_id) {

        $jwtCheck = null;
        $message = null;
        $jwt = null;

        $data = json_decode(file_get_contents('php://input'), true);
        $password = null;
        $findUserById = null;




        // get jwt token //

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

                try {

                    if (!is_null($data))  {

                        $password = $data['password'];

                        // find user //

                        $findUserById = $this->userRepository->findUserById($user_id)[0];



                        // add calculate age to result //

                        $age = date_diff(date_create($findUserById['birthdate']), date_create('now'));

                        $findUserById['age'] = $age->y;



                        // check if password OK //

                        $factory = new PasswordHasherFactory([
                            'common' => ['algorithm' => 'bcrypt']
                        ]);
            
                        $hasher = $factory->getPasswordHasher('common');

                        if ($hasher->verify($findUserById['password'], $password)) {

                            $jwtCheck = true;
                            $message = 'Password is correct';

                            return new JsonResponse([
                                'flag' => $jwtCheck,
                                'message' => $message
                            ]);
                        }

                        else {

                            $jwtCheck = false;
                            $message = 'Password is incorrect';

                            return new JsonResponse([
                                'flag' => $jwtCheck,
                                'message' => $message
                            ]);
                        }
                    }
                }

                catch (\Exception $e) {

                    $jwtCheck = false;
                    $message = 'Authentication problem, please sign in again';

                    return new JsonResponse([
                        'flag' => $jwtCheck,
                        'message' => $message
                    ]);
                }
            }
        }

        catch (\Exception $e) {

            $jwtCheck = false;
            $message = 'Authentication problem, please sign in again';

            return new JsonResponse([
                'flag' => $jwtCheck,
                'message' => $message
            ]);
        }



        return new JsonResponse([
            'flag' => $jwtCheck
        ]);
    }
}
