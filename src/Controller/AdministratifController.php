<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Patient;
use Doctrine\Persistence\ManagerRegistry;

final class AdministratifController extends AbstractController
{
    #[Route('/', name: 'app_administratif')]
    public function index(): Response
    {
        return $this->render('administratif/index.html.twig');
    }
    
    //Partie Patient
    #[Route('/patient', name: 'app_patient')]
    public function patients(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Patient::class);
        $patients = $repository->findAll(); // Récupère tous les patients
        return $this->render('administratif/patient.html.twig', [
            'patients' => $patients,
        ]);
    }

    /*Supprimer un patient*/
    #[Route('/retirerPatient/{id}', name: 'app_retirer_patient', methods: ['POST'])]
    public function retirerPatient(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Patient::class);
        $em = $doctrine->getManager();

        // Récupération du patient à supprimer
        $patient = $repository->find($id);

        if ($patient) {
            $em->remove($patient);
            $em->flush();

            $this->addFlash('success', 'Le patient a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Aucun patient trouvé avec cet ID.');
        }

        // Redirection vers la liste des patients après suppression
        return $this->redirectToRoute('app_patient');
    }

    
    /*Modifier un patient*/
    #[Route('/modifierPatient/{id}', name: 'app_modifier_patient')]
    public function modifierPatient(ManagerRegistry $doctrine, $id): Response
    {
        // Logique pour modifier un patient
        return $this->render('administratif/modifier_patient.html.twig', [
            'patientId' => $id,
        ]);
    }           



    //Partie Séjour
    #[Route('/sejour', name: 'app_sejour')]
    public function sejours(): Response
    {
        return $this->render('administratif/sejour.html.twig');
    }
}
