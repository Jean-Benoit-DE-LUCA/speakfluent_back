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

        $data = json_decode(file_get_contents('php://input'), true);

        $name = htmlspecialchars($data['name']);
        $firstname = htmlspecialchars($data['firstname']);
        $email = htmlspecialchars($data['email']);
        $birthdate = htmlspecialchars($data['birthdate']);
        $gender = htmlspecialchars($data['gender']);
        $address = htmlspecialchars($data['address']);
        $zip = htmlspecialchars($data['zip']);
        $city = htmlspecialchars($data['city']);
        $password = htmlspecialchars($data['password']);

        // HASH PASSWORD //

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt']
        ]);

        $hasher = $factory->getPasswordHasher('common');
        $password_hash = $hasher->hash($password);

        // CHECK USER BEFORE INSERT //

        $findUserByMail = $this->userRepository->findUserByMail(
            $email
        );

        $flag = null;

        if (empty($findUserByMail)) {

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
                $password_hash
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

        return new JsonResponse([
            'flag' => $flag
        ]);
    }






    // FIND USER BY ID //

    #[Route('/api/user/find/id/{user_id}', name: 'user_find_id')]
    public function findUserById($user_id) {


        // get jwt token //

        $jwt = '';

        foreach (getallheaders() as $key => $value) {

            if ($key == 'Authorization') {

                $jwt .= $value;
            }
        }




        // find user //

        $findUserById = $this->userRepository->findUserById($user_id);





        // add calculate age to result //

        $age = date_diff(date_create($findUserById[0]['birthdate']), date_create('now'));

        $findUserById[0]['age'] = $age->y;







        // decode jwt //

        $jwtObj = new JwtClass();
        $jwtCheck = $jwtObj->decodeJwt(str_replace('Bearer ', '', $jwt));

        if ($jwtCheck == 'Expired token') {

            $jwtCheck = false;
        }



        // return json response //

        return new JsonResponse([
            'userObj' => $findUserById[0],
            'jwtCheck' => $jwtCheck
        ]);
    }
}
