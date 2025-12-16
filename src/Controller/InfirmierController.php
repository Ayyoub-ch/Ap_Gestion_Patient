<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Patient;
use App\Entity\Sejour;
use Doctrine\ORM\EntityManagerInterface;

final class InfirmierController extends AbstractController
{
    #[Route('/infirmier', name: 'app_infirmier')]
    public function index(): Response
    {
    return $this->render('infirmier/index.html.twig',);
    }
    #[Route('/infirmier/sejour/dateJour', name: 'app_infirmier_sejour')]
    public function afficherSejourDateJour(EntityManagerInterface $em): Response {
        
        $sejours = $em->getRepository(Sejour::class)->findSejourDateJour();
        return $this->render('infirmier/sejours-list-jour.html.twig', [
            'sejours' => $sejours
        ]);
    }
    
     #[Route('/infirmier/patient/{id}', name: 'infos_patient')]
    public function detailPatient(EntityManagerInterface $em,int $id): Response {
        $patients = $em->getRepository(Patient::class)->findById($id);
        return $this->render('infirmier/patient-list.html.twig',
         [
            'patients' => $patients
        ]);
    }
    #[Route('/infirmier/patient/arrivee/{id}', name: 'arrivee_patient')]
    public function arriveePatient(EntityManagerInterface $em,int $id): Response {
        $patients = $em->getRepository(Patient::class)->findByDateEntree($id);
        return $this->render('infirmier/arrivee-patient.html.twig',
         [
            'patients' => $patients
        ]);
    }

}
    