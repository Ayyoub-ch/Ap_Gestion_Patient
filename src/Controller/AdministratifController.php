<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Patient;
use App\Entity\Sejour;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


final class AdministratifController extends AbstractController
{
    #[Route('/administratif/dashboard', name: 'app_administratif')]
    public function index(): Response
    {
        return $this->render('administratif/index.html.twig');
    }
    
    //Partie Patient
    #[Route('/administratif/patient', name: 'app_patient')]
    public function patients(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Patient::class);
        $patients = $repository->findAll(); // Récupère tous les patients
        return $this->render('administratif/patient.html.twig', [
            'patients' => $patients,
        ]);
    }

    /*Supprimer un patient*/
    #[Route('/administratif/retirerPatient/{id}', name: 'app_retirer_patient', methods: ['POST'])]
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

    #[Route('/administratif/ajoutPatient', name: 'app_ajout_patient', methods: ['GET'])]
    public function afficherFormulaire(): Response
    {
        // Affiche juste le formulaire
        return $this->render('administratif/ajouter_patient.html.twig');
    }


    /* Ajouter un patient */
    #[Route('/administratif/ajouterPatient', name: 'app_ajouter_patient', methods: ['POST'])]
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
    #[Route('/administratif/modifierPatient/{id}', name: 'app_modifier_patient', methods: ['GET', 'POST'])]
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
    #[Route('/administratif/sejour', name: 'app_sejour')]
    public function sejours(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Sejour::class);
        $sejours = $repository->findAll(); // Récupère tous les séjours
        return $this->render('administratif/sejour.html.twig', [
            'sejours' => $sejours,
        ]);
    }


    /*Supprimer un sejour*/
    #[Route('/retirerSejour/{id}', name: 'app_retirer_sejour', methods: ['POST'])]
    public function retirerSejour(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Sejour::class);
        $em = $doctrine->getManager();

        // Récupération du patient à supprimer
        $sejour = $repository->find($id);

        if ($sejour) {
            $em->remove($sejour);
            $em->flush();

            $this->addFlash('success', 'Le sejour a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Aucun sejour trouvé avec cet ID.');
        }

        // Redirection vers la liste des sejours après suppression
        return $this->redirectToRoute('app_sejour');
    }

    #[Route('/administratif/ajoutSejour', name: 'app_ajout_sejour', methods: ['GET'])]
    public function afficherFormulaireSejour(): Response
    {
        // Affiche juste le formulaire
        return $this->render('administratif/ajouter_sejour.html.twig');
    }

    /* Ajouter un patient */
    #[Route('/administratif/ajouterSejour', name: 'app_ajouter_sejour', methods: ['POST'])]
    public function ajouterSejour(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $sejour = new Sejour();

        // Récupération des données
        $date_entree = $request->request->get('date_entree');
        $date_sortie = $request->request->get('date_sortie');
        $libelle = $request->request->get('libelle');
        $statut_du_jour = $request->request->get('statut_du_jour');

        // Affectation
        $sejour->setDateEntree(new \DateTime($date_entree));
        $sejour->setDateSortie(new \DateTime($date_sortie));
        $sejour->setLibelle($libelle);
        $sejour->setStatutDuJour($statut_du_jour);

        // Sauvegarde
        $em->persist($sejour);
        $em->flush();

        // Message de réussite
        $this->addFlash('success', 'Le sejour a été ajouté avec succès.');

        // Redirection immédiate vers la liste
        return $this->redirectToRoute('app_sejour');
    }




    /* Modifier un séjour */
    #[Route('/administratif/modifierSejour/{id}', name: 'app_modifier_sejour', methods: ['GET', 'POST'])]
    public function modifierSejour(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Sejour::class);
        $patientRepository = $doctrine->getRepository(Patient::class);
        $chambreRepository = $doctrine->getRepository(\App\Entity\Chambre::class);
        $em = $doctrine->getManager();

        $sejour = $repository->find($id);

        if (!$sejour) {
            $this->addFlash('error', 'Aucun sejour trouvé avec cet ID.');
            return $this->redirectToRoute('app_sejour');
        }

        if ($request->isMethod('POST')) {
            // Conversion des dates
            $dateEntree = $request->request->get('date_entree');
            $dateSortie = $request->request->get('date_sortie');
            
            $sejour->setDateEntree(new \DateTime($dateEntree));
            $sejour->setDateSortie(new \DateTime($dateSortie));
            $sejour->setLibelle($request->request->get('libelle'));
            $sejour->setStatutDuJour($request->request->get('statut_du_jour'));
            
            // Gestion des relations
            $patientId = $request->request->get('patient_id');
            $chambreId = $request->request->get('chambre_id');
            
            if ($patientId) {
                $patient = $patientRepository->find($patientId);
                $sejour->setPatient($patient);
            }
            
            if ($chambreId) {
                $chambre = $chambreRepository->find($chambreId);
                $sejour->setChambre($chambre);
            }
            
            $em->flush();

            $this->addFlash('success', 'Le sejour a été modifié avec succès.');

            return $this->redirectToRoute('app_sejour');
        }

        // Récupération des listes pour les selects
        $patients = $patientRepository->findAll();
        $chambres = $chambreRepository->findAll();

        return $this->render('administratif/modifier_sejour.html.twig', [
            'sejour' => $sejour,
            'patients' => $patients,
            'chambres' => $chambres,
        ]);
    }   
}
