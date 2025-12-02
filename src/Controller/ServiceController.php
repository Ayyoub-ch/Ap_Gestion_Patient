<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController
{
    #[Route('/admin/services', name: 'app_admin_services')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findAll();
        
        return $this->render('admin/services/index.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/admin/services/new', name: 'app_admin_services_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            $this->addFlash('success', 'Service créé avec succès !');
            return $this->redirectToRoute('app_admin_services');
        }

        return $this->render('admin/services/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/admin/services/{id}/edit', name: 'app_admin_services_edit')]
    public function edit(Service $service, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Service modifié avec succès !');
            return $this->redirectToRoute('app_admin_services');
        }

        return $this->render('admin/services/form.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
            'is_edit' => true,
        ]);
    }

    #[Route('/admin/services/{id}/delete', name: 'app_admin_services_delete', methods: ['POST'])]
    public function delete(Service $service, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            // Vérifier si le service a des utilisateurs ou des chambres
            if ($service->getUtilisateurs()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce service car des utilisateurs y sont rattachés.');
            } elseif ($service->getChambres()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce service car des chambres y sont rattachées.');
            } else {
                $entityManager->remove($service);
                $entityManager->flush();
                $this->addFlash('success', 'Service supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('app_admin_services');
    }

    #[Route('/admin/services/{id}', name: 'app_admin_services_show')]
    public function show(Service $service): Response
    {
        return $this->render('admin/services/show.html.twig', [
            'service' => $service,
        ]);
    }
}
