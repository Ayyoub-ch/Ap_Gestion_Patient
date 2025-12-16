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
    #[Route('/infirmier/dashboard', name: 'app_infirmier')]
    public function index(): Response
    {
    return $this->render('infirmier/index.html.twig',);
    }

    // Gestion des patients (arrivées et sorties)
    #[Route('/infirmier/gestion', name: 'app_gestion')]
    public function gestion(): Response {
        return $this->render('infirmier/index_gestion.html.twig');
    }

    //Arrivée des patients
    #[Route('/infirmier/arrivee', name: 'app_arrivee_patient')]
    public function arriveePatient(): Response {
        return $this->render('infirmier/arrivee_patient.html.twig');
    }

    //Sortie des patients
    #[Route('/infirmier/sortie', name: 'app_sortie_patient')]
    public function sortiePatient(): Response {
        return $this->render('infirmier/sortie_patient.html.twig');
    }

    
    // Consultation des séjours des patients
    #[Route('/infirmier/consultation', name: 'app_consultation')]
    public function afficherPatient(EntityManagerInterface $em,int $id): Response {
        $patients = $em->getRepository(Patient::class)->findById($id);
        return $this->render('infirmier/index_consultation.html.twig',
            [
                'patients' => $patients
            ]); 
    }

    #[Route('/infirmier/sejour/dateJour', name: 'app_infirmier_sejour')]
    public function afficherSejourDateJour(EntityManagerInterface $em): Response {
        
        $sejours = $em->getRepository(Sejour::class)->findSejourDateJour();
        return $this->render('infirmier/sejours-list-jour.html.twig', [
            'sejours' => $sejours
        ]);
    }
    
    #[Route('/infirmier/patient/{id}', name: 'infirmier_patient')]
    public function afficherPatientParDate(EntityManagerInterface $em,int $id): Response {
        $patients = $em->getRepository(Patient::class)->findByDateEntree($id);
        return $this->render('infirmier/patient-list.html.twig',
         [
            'patients' => $patients
        ]);
    }
}
