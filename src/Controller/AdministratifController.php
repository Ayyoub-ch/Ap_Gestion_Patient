<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Patient;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


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

    
    /* Ajouter un patient */
    #[Route('/ajouterPatient', name: 'app_ajouter_patient', methods: ['POST'])]
    public function ajouterPatient(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $patient = new Patient();

        // Récupération des données
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $telephone = $request->request->get('telephone');
        $sexe = $request->request->get('sexe');
        $note = $request->request->get('note');

        // Affectation
        $patient->setNom($nom);
        $patient->setPrenom($prenom);
        $patient->setTelephone($telephone);
        $patient->setSexe($sexe);
        $patient->setNote($note);

        // Sauvegarde
        $em->persist($patient);
        $em->flush();

        // Message de réussite
        $this->addFlash('success', 'Le patient a été ajouté avec succès.');

        // Redirection immédiate vers la liste
        return $this->redirectToRoute('app_patient');
    }


    /* Modifier un patient */
    #[Route('/modifierPatient/{id}', name: 'app_modifier_patient', methods: ['GET', 'POST'])]
    public function modifierPatient(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Patient::class);
        $em = $doctrine->getManager();

        $patient = $repository->find($id);

        if (!$patient) {
            $this->addFlash('error', 'Aucun patient trouvé avec cet ID.');
            return $this->redirectToRoute('app_patient');
        }

        if ($request->isMethod('POST')) {
            $patient->setNom($request->request->get('nom'));
            $patient->setPrenom($request->request->get('prenom'));
            $patient->setTelephone($request->request->get('telephone'));
            $patient->setSexe($request->request->get('sexe'));
            $patient->setNote($request->request->get('note'));

            $em->flush();

            $this->addFlash('success', 'Le patient a été modifié avec succès.');

            return $this->redirectToRoute('app_patient');
        }

        return $this->render('administratif/modifier_patient.html.twig', [
            'patient' => $patient,
        ]);
    }   



    //Partie Séjour
    #[Route('/sejour', name: 'app_sejour')]
    public function sejours(): Response
    {
        return $this->render('administratif/sejour.html.twig');
    }
}
