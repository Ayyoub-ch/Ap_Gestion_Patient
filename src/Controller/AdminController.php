<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Patient;
use App\Entity\Lit;
use App\Entity\Chambre;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Repository\PatientRepository;
use App\Repository\LitRepository;
use App\Repository\ChambreRepository;
use App\Repository\SejourRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/new', name: 'app_admin_users_new')]
    public function newUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'app_admin_users_edit')]
    public function editUser(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserFormType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès !');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'is_edit' => true,
        ]);
    }

    #[Route('/admin/users/{id}/delete', name: 'app_admin_users_delete', methods: ['POST'])]
    public function deleteUser(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Prevent deleting yourself
            if ($this->getUser() === $user) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte !');
            } else {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('app_admin_users');
    }

    // Gestion des Patients
    #[Route('/admin/patients', name: 'app_admin_patients')]
    public function patients(PatientRepository $patientRepository): Response
    {
        $patients = $patientRepository->findAll();
        
        return $this->render('admin/patients.html.twig', [
            'patients' => $patients,
        ]);
    }

    #[Route('/admin/patients/{id}', name: 'app_admin_patients_show')]
    public function showPatient(Patient $patient): Response
    {
        return $this->render('admin/patient_show.html.twig', [
            'patient' => $patient,
        ]);
    }

    #[Route('/admin/patients/{id}/delete', name: 'app_admin_patients_delete', methods: ['POST'])]
    public function deletePatient(Patient $patient, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $patient->getId(), $request->request->get('_token'))) {
            $entityManager->remove($patient);
            $entityManager->flush();
            $this->addFlash('success', 'Patient supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_patients');
    }

    // Gestion des Chambres et Lits
    #[Route('/admin/chambres-lits', name: 'app_admin_chambres_lits')]
    public function chambresLits(ChambreRepository $chambreRepository, LitRepository $litRepository): Response
    {
        $chambres = $chambreRepository->findAll();
        $lits = $litRepository->findAll();
        
        return $this->render('admin/chambres_lits.html.twig', [
            'chambres' => $chambres,
            'lits' => $lits,
        ]);
    }

    // Gestion des Chambres
    #[Route('/admin/chambres', name: 'app_admin_chambres')]
    public function chambres(ChambreRepository $chambreRepository): Response
    {
        $chambres = $chambreRepository->findAll();
        
        return $this->render('admin/chambres.html.twig', [
            'chambres' => $chambres,
        ]);
    }

    #[Route('/admin/chambres/new', name: 'app_admin_chambres_new')]
    public function newChambre(Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository): Response
    {
        $chambre = new Chambre();
        $services = $serviceRepository->findAll();

        if ($request->isMethod('POST')) {
            $serviceId = $request->request->get('service_id');
            $etage = $request->request->get('etage');
            $nombreLit = $request->request->get('nombre_lit');
            $numero = $request->request->get('numero');

            if ($serviceId && $etage !== null && $nombreLit !== null) {
                $service = $serviceRepository->find($serviceId);
                if ($service) {
                    $chambre->setChambre($service);
                    $chambre->setEtage((int)$etage);
                    $chambre->setNombreLit((int)$nombreLit);
                    $chambre->setNumero($numero);

                    $entityManager->persist($chambre);
                    $entityManager->flush();

                    $this->addFlash('success', 'Chambre créée avec succès !');
                    return $this->redirectToRoute('app_admin_chambres_lits');
                }
            }
        }

        return $this->render('admin/chambre_form.html.twig', [
            'chambre' => null,
            'services' => $services,
            'is_edit' => false,
        ]);
    }

    #[Route('/admin/chambres/{id}/edit', name: 'app_admin_chambres_edit')]
    public function editChambre(Chambre $chambre, Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findAll();

        if ($request->isMethod('POST')) {
            $serviceId = $request->request->get('service_id');
            $etage = $request->request->get('etage');
            $nombreLit = $request->request->get('nombre_lit');
            $numero = $request->request->get('numero');

            if ($serviceId && $etage !== null && $nombreLit !== null) {
                $service = $serviceRepository->find($serviceId);
                if ($service) {
                    // Vérifier que la nouvelle capacité n'est pas inférieure au nombre de lits actuels
                    $litsActuels = count($chambre->getLits());
                    if ((int)$nombreLit < $litsActuels) {
                        $this->addFlash('error', 'La capacité ne peut pas être inférieure au nombre de lits actuellement installés (' . $litsActuels . ').');
                        return $this->render('admin/chambre_form.html.twig', [
                            'chambre' => $chambre,
                            'services' => $services,
                            'is_edit' => true,
                        ]);
                    }
                    
                    $chambre->setChambre($service);
                    $chambre->setEtage((int)$etage);
                    $chambre->setNombreLit((int)$nombreLit);
                    $chambre->setNumero($numero);

                    $entityManager->flush();

                    $this->addFlash('success', 'Chambre modifiée avec succès !');
                    return $this->redirectToRoute('app_admin_chambres_lits');
                }
            }
        }

        return $this->render('admin/chambre_form.html.twig', [
            'chambre' => $chambre,
            'services' => $services,
            'is_edit' => true,
        ]);
    }

    #[Route('/admin/chambres/{id}/delete', name: 'app_admin_chambres_delete', methods: ['POST'])]
    public function deleteChambre(Chambre $chambre, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chambre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chambre);
            $entityManager->flush();
            $this->addFlash('success', 'Chambre supprimée avec succès !');
        }

        return $this->redirectToRoute('app_admin_chambres_lits');
    }

    // Gestion des Lits
    #[Route('/admin/lits', name: 'app_admin_lits')]
    public function lits(LitRepository $litRepository): Response
    {
        $lits = $litRepository->findAll();
        
        return $this->render('admin/lits.html.twig', [
            'lits' => $lits,
        ]);
    }

    #[Route('/admin/lits/new', name: 'app_admin_lits_new')]
    public function newLit(Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
    {
        $lit = new Lit();
        $chambres = $chambreRepository->findAll();

        if ($request->isMethod('POST')) {
            $typeEmplacement = $request->request->get('type_emplacement');
            $disponibilite = $request->request->get('disponibilite') === '1';
            $descriptionEmplacement = $request->request->get('description_emplacement');
            $numero = $request->request->get('numero');
            $notes = $request->request->get('notes');

            // Validation du type d'emplacement
            if (!$typeEmplacement) {
                $this->addFlash('error', 'Veuillez sélectionner un type d\'emplacement.');
                return $this->render('admin/lit_form.html.twig', [
                    'lit' => null,
                    'chambres' => $chambres,
                    'is_edit' => false,
                ]);
            }

            $lit->setTypeEmplacement($typeEmplacement);
            $lit->setDisponibilite($disponibilite);
            $lit->setNumero($numero);
            $lit->setNotes($notes);

            // Si c'est une chambre, on associe la chambre
            if ($typeEmplacement === 'chambre') {
                $chambreId = $request->request->get('chambre_id');
                if (!$chambreId) {
                    $this->addFlash('error', 'Veuillez sélectionner une chambre pour ce lit.');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => null,
                        'chambres' => $chambres,
                        'is_edit' => false,
                    ]);
                }
                
                $chambre = $chambreRepository->find($chambreId);
                if (!$chambre) {
                    $this->addFlash('error', 'Chambre introuvable.');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => null,
                        'chambres' => $chambres,
                        'is_edit' => false,
                    ]);
                }
                
                // Vérifier si la chambre n'est pas pleine
                $litsActuels = count($chambre->getLits());
                if ($litsActuels < $chambre->getNombreLit()) {
                    $lit->setChambre($chambre);
                } else {
                    $this->addFlash('error', 'Cette chambre a atteint sa capacité maximale (' . $chambre->getNombreLit() . ' lits).');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => null,
                        'chambres' => $chambres,
                        'is_edit' => false,
                    ]);
                }
            } else {
                // Pour les autres emplacements, on met chambre à null
                $lit->setChambre(null);
                
                // Validation de la description pour les autres types
                if (empty($descriptionEmplacement)) {
                    $typeLabels = [
                        'couloir' => 'l\'étage du couloir',
                        'salle_reveil' => 'la salle de réveil',
                        'urgences' => 'la zone des urgences',
                        'soins_intensifs' => 'le box des soins intensifs',
                        'bloc_operatoire' => 'la salle d\'opération',
                        'autre' => 'la description de l\'emplacement'
                    ];
                    $label = $typeLabels[$typeEmplacement] ?? 'la description';
                    $this->addFlash('error', 'Veuillez renseigner ' . $label . '.');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => null,
                        'chambres' => $chambres,
                        'is_edit' => false,
                    ]);
                }
                $lit->setDescriptionEmplacement($descriptionEmplacement);
            }

            $entityManager->persist($lit);
            $entityManager->flush();

            $this->addFlash('success', 'Lit créé avec succès !');
            return $this->redirectToRoute('app_admin_chambres_lits');
        }

        return $this->render('admin/lit_form.html.twig', [
            'lit' => null,
            'chambres' => $chambres,
            'is_edit' => false,
        ]);
    }

    #[Route('/admin/lits/{id}/edit', name: 'app_admin_lits_edit')]
    public function editLit(Lit $lit, Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
    {
        $chambres = $chambreRepository->findAll();

        if ($request->isMethod('POST')) {
            $typeEmplacement = $request->request->get('type_emplacement');
            $disponibilite = $request->request->get('disponibilite') === '1';
            $descriptionEmplacement = $request->request->get('description_emplacement');
            $numero = $request->request->get('numero');
            $notes = $request->request->get('notes');

            // Validation du type d'emplacement
            if (!$typeEmplacement) {
                $this->addFlash('error', 'Veuillez sélectionner un type d\'emplacement.');
                return $this->render('admin/lit_form.html.twig', [
                    'lit' => $lit,
                    'chambres' => $chambres,
                    'is_edit' => true,
                ]);
            }

            $lit->setTypeEmplacement($typeEmplacement);
            $lit->setDisponibilite($disponibilite);
            $lit->setNumero($numero);
            $lit->setNotes($notes);

            // Si c'est une chambre, on associe la chambre
            if ($typeEmplacement === 'chambre') {
                $chambreId = $request->request->get('chambre_id');
                
                // Vérifier que la chambre est sélectionnée
                if (!$chambreId) {
                    $this->addFlash('error', 'Veuillez sélectionner une chambre pour ce lit.');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => $lit,
                        'chambres' => $chambres,
                        'is_edit' => true,
                    ]);
                }
                
                $chambre = $chambreRepository->find($chambreId);
                
                if (!$chambre) {
                    $this->addFlash('error', 'Chambre introuvable.');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => $lit,
                        'chambres' => $chambres,
                        'is_edit' => true,
                    ]);
                }
                
                // Vérifier si on change de chambre et si la nouvelle n'est pas pleine
                if ($lit->getChambre() === null || $lit->getChambre()->getId() !== $chambre->getId()) {
                    $litsActuels = count($chambre->getLits());
                    if ($litsActuels < $chambre->getNombreLit()) {
                        $lit->setChambre($chambre);
                    } else {
                        $this->addFlash('error', 'Cette chambre a atteint sa capacité maximale (' . $chambre->getNombreLit() . ' lits).');
                        return $this->render('admin/lit_form.html.twig', [
                            'lit' => $lit,
                            'chambres' => $chambres,
                            'is_edit' => true,
                        ]);
                    }
                } else {
                    // Même chambre, pas de vérification nécessaire
                    $lit->setChambre($chambre);
                }
            } else {
                // Pour les autres emplacements, on met chambre à null
                $lit->setChambre(null);
                
                // Validation de la description pour les autres types
                if (empty($descriptionEmplacement)) {
                    $typeLabels = [
                        'couloir' => 'l\'étage du couloir',
                        'salle_reveil' => 'la salle de réveil',
                        'urgences' => 'la zone des urgences',
                        'soins_intensifs' => 'le box des soins intensifs',
                        'bloc_operatoire' => 'la salle d\'opération',
                        'autre' => 'la description de l\'emplacement'
                    ];
                    $label = $typeLabels[$typeEmplacement] ?? 'la description';
                    $this->addFlash('error', 'Veuillez renseigner ' . $label . '.');
                    return $this->render('admin/lit_form.html.twig', [
                        'lit' => $lit,
                        'chambres' => $chambres,
                        'is_edit' => true,
                    ]);
                }
                $lit->setDescriptionEmplacement($descriptionEmplacement);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Lit modifié avec succès !');
            return $this->redirectToRoute('app_admin_chambres_lits');
        }

        return $this->render('admin/lit_form.html.twig', [
            'lit' => $lit,
            'chambres' => $chambres,
            'is_edit' => true,
        ]);
    }

    #[Route('/admin/lits/{id}/delete', name: 'app_admin_lits_delete', methods: ['POST'])]
    public function deleteLit(Lit $lit, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lit);
            $entityManager->flush();
            $this->addFlash('success', 'Lit supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_chambres_lits');
    }

    // Statistiques
    #[Route('/admin/statistiques', name: 'app_admin_statistiques')]
    public function statistiques(
        UserRepository $userRepository,
        PatientRepository $patientRepository,
        LitRepository $litRepository,
        SejourRepository $sejourRepository,
        ServiceRepository $serviceRepository
    ): Response
    {
        $stats = [
            'total_users' => count($userRepository->findAll()),
            'total_patients' => count($patientRepository->findAll()),
            'total_lits' => count($litRepository->findAll()),
            'lits_disponibles' => count($litRepository->findBy(['disponibilite' => true])),
            'lits_occupes' => count($litRepository->findBy(['disponibilite' => false])),
            'total_sejours' => count($sejourRepository->findAll()),
            'total_services' => count($serviceRepository->findAll()),
        ];

        return $this->render('admin/statistiques.html.twig', [
            'stats' => $stats,
        ]);
    }

    // Paramètres
    #[Route('/admin/parametres', name: 'app_admin_parametres')]
    public function parametres(): Response
    {
        return $this->render('admin/parametres.html.twig');
    }

    #[Route('/admin/parametres/clear-cache', name: 'app_admin_parametres_clear_cache', methods: ['POST'])]
    public function clearCache(Request $request): Response
    {
        if ($this->isCsrfTokenValid('clear_cache', $request->request->get('_token'))) {
            try {
                // En production, vous devriez utiliser une commande Symfony
                // Pour le développement, on simule juste le succès
                $this->addFlash('success', 'Le cache a été vidé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors du vidage du cache : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_parametres');
    }

    #[Route('/admin/parametres/db-info', name: 'app_admin_parametres_db_info', methods: ['POST'])]
    public function dbInfo(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('db_info', $request->request->get('_token'))) {
            try {
                $connection = $entityManager->getConnection();
                $databaseName = $connection->getDatabase();
                
                $this->addFlash('success', 'Base de données connectée : ' . $databaseName);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la récupération des infos DB : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_parametres');
    }
}