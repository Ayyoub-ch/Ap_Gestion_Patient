<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdministratifController extends AbstractController
{
    #[Route('/administratif', name: 'app_administratif')]
    public function index(): Response
    {
        return $this->render('administratif/index.html.twig');
    }

    #[Route('/patient', name: 'app_patient')]
    public function patients(): Response
    {
        return $this->render('administratif/patient.html.twig');
    }

    #[Route('/sejour', name: 'app_sejour')]
    public function sejours(): Response
    {
        return $this->render('administratif/sejour.html.twig');
    }
}
