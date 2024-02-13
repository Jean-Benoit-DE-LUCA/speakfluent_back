<?php

namespace App\Controller;

use App\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\JWT\JwtClass;

class LanguageController extends AbstractController

{

    private $languageRepository;

    public function __construct(LanguageRepository $languageRepository) {

        $this->languageRepository = $languageRepository;
    }



    // GET LANGUAGES //

    #[Route('/api/home', name: 'home')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'language' => $this->languageRepository->getLanguages()
        ]);
    }




    // CHECK JWT //

    #[Route('/api/home/checkjwt', name: 'home_checkjwt')]
    public function checkJwt() {


        $jwt = "";
        $jwtCheck = null;

        // get jwt token //

        foreach (getallheaders() as $key => $value) {

            if ($key == 'Authorization') {

                $jwt .= $value;
            }
        }




        // decode jwt //

        $jwtObj = new JwtClass();
        $jwtCheck = $jwtObj->decodeJwt(str_replace('Bearer ', '', $jwt));


        return new JsonResponse([
            'flag' => isset($jwtCheck->iat)
        ]);
    }
}
