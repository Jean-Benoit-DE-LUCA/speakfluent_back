<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ResetPasswordRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Annotation\Route;

use App\Mail\Mail;

use \DateTime;
use \DateTimeZone;

class ResetPasswordController extends AbstractController
{

    protected $resetPasswordRepository;
    protected $userRepository;

    public function __construct(UserRepository $userRepository, ResetPasswordRepository $resetPasswordRepository) {

        $this->resetPasswordRepository = $resetPasswordRepository;
        $this->userRepository = $userRepository;
    }


    // CHECK MAIL + TOKEN + SET NEW PASSWORD //

    #[Route('/api/resetpassword/check', name: 'reset_password_check')]
    public function checkMailToken() {

        $data = json_decode(file_get_contents('php://input'), true);
        $email = null;
        $token = null;
        $password = null;
        $password_hash = null;

        $check = null;

        $flag = null;
        $message = null;

        $timestampToken = null;
        $currentTimestamp = null;

        if (!is_null($data)) {

            $email = $data['email'];
            $token = $data['token'];
            $password = $data['password'];

            $check = $this->resetPasswordRepository->checkMailToken($email, $token);

            if (count($check) > 0) {

                $timestampToken = strtotime($check[0]['created_at']);
                $currentTimestamp = time();

                if (($currentTimestamp - $timestampToken) < 3600) {

                    $factory = new PasswordHasherFactory([
                        'common' => ['algorithm' => 'bcrypt']
                    ]);

                    $hasher = $factory->getPasswordHasher('common');

                    $password_hash = $hasher->hash($password);

                    $this->userRepository->setNewPassword($password_hash, $email);

                    return new JsonResponse([
                        'flag' => true
                    ]);
                }

                else {

                    return new JsonResponse([
                        'flag' => false,
                        'message' => 'Mail expired, please restart the process to set a new password'
                    ]);
                }
            }

            else {

                return new JsonResponse([
                    'flag' => false,
                    'message' => 'User not found'
                ]);
            }
        }

        return new JsonResponse([
            'flag' => false,
            'message' => $message
        ]);
    }



    // GENERATE TOKEN AND SEND MAIL TO RESET PASSWORD //

    #[Route('/api/resetpassword/generate', name: 'reset_password_generate')]
    public function resetPasswordGenerate()
    {

        $data = json_decode(file_get_contents('php://input'), true);

        $email = null;
        $findUserByMail = null;
        $token = null;
        $newDateTimeFormat = null;

        $flag = null;
        $message = null;

        if (!is_null($data)) {

            $email = \htmlspecialchars($data['email']);

            // check if user exists //

            $findUserByMail = $this->userRepository->findUserByMail(
                $email
            );




            // if no //

            if (count($findUserByMail) == 0) {

                $flag = false;
                $message = 'User not found';
            }

            // if yes //

            else if (count($findUserByMail) > 0) {

                $token = bin2hex(random_bytes(30));

                // get and set current date time for database //

                $newDateTime = new DateTime();
                $timeZone = new DateTimeZone('Europe/Paris');

                $newDateTime->setTimezone($timeZone);
                $newDateTimeFormat = $newDateTime->format('Y-m-d H:i:s');

                $this->resetPasswordRepository->insertToken($email, $token, $newDateTimeFormat, $newDateTimeFormat);

                Mail::sendMailResetPassword($email, $token);

                $flag = true;
            }
        }

        return new JsonResponse([
            'flag' => $flag,
            'message' => $message
        ]);
    }
}
