<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Patient;
use App\Entity\Sejour;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SejourRepository;

class InfirmierController extends AbstractController
{
    #[Route('/infirmier/', name: 'app_infirmier')]
    public function index(): Response
    {
        return $this->render('infirmier/index.html.twig');
    }

    //Gestion des séjours des patients
    #[Route('/infirmier/sejours', name: 'app_gestion')]
    public function gestionSejours(): Response {
        return $this->render('infirmier/index_gestion.html.twig'); 
    }


    #[Route('/infirmier/sejour/arrivee', name: 'app_arrivee')]
    public function arriveePatient(EntityManagerInterface $em, SejourRepository $sejours): Response {
        $sejours = $em->getRepository(Sejour::class)->findByArriveeJour(new \DateTimeImmutable('today'));
        return $this->render('infirmier/arrivee_patient.html.twig',
         [
            'sejours' => $sejours
        ]);
    }


    #[Route('/infirmier/patient/sortie', name: 'app_sortie')]
    public function sortiePatient(EntityManagerInterface $em, SejourRepository $sejours): Response {
        $sejours = $em->getRepository(Sejour::class)->findBySortieJour(new \DateTimeImmutable('today'));
        return $this->render('infirmier/sortie_patient.html.twig',
         [
            'sejours' => $sejours
        ]);
    }


    //Partie Consulation

    // Consultation des séjours des patients
    #[Route('/infirmier/consultation', name: 'app_consultation')]
    public function consultationSejours(): Response {
        return $this->render('infirmier/index_consultation.html.twig'); 
    }

}
    