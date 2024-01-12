<?php

namespace App\Controller;

use App\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController

{

    private $languageRepository;

    public function __construct(LanguageRepository $languageRepository) {

        $this->languageRepository = $languageRepository;
    }

    #[Route('/api/home', name: 'home')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'language' => $this->languageRepository->getLanguages()
        ]);
    }
}
