<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\JWT\JwtClass;
use App\Mail\Mail;


class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'contact')]
    public function contact()
    {   
        $data = null;
        $name = null;
        $firstname = null;
        $email = null;
        $message = null;

        $flag = null;

        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_null($data)) {

            $name = htmlspecialchars($data['name']);
            $firstname = htmlspecialchars($data['firstname']);
            $email = htmlspecialchars($data['email']);
            $message = htmlspecialchars($data['message']);

            try {
                Mail::sendMail($email, $name, $firstname, $message);
                $flag = true;
            }

            catch (\Exception $e) {
                $flag = false;
            }

        }

        return new JsonResponse([
            'result' => $flag
        ]);
    }
}
