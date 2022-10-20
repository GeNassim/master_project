<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Form\CampagneType;
use App\Repository\CampagneRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('dashboard/campagne')]
class CampagneController extends AbstractController
{
    #[
        Route('-list/', name: 'app_campagne_index', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function index(Request $request, CampagneRepository $campagneRepository): Response
    {
        $campagnes = $campagneRepository->findAll();
        $nb = count($campagnes);
        $campagne = new Campagne();
        $form = $this->createForm(CampagneType::class, $campagne);
        $campagne->setCreatedAt(new DateTime());
        $campagne->setUpdatedAt(new DateTime());
        $form->remove("createdAt");
        $form->remove("updatedAt");
        $form->handleRequest($request);
        $campagnename = $campagne->getName();

        if ($form->isSubmitted() && $form->isValid()) {
            $campagneRepository->save($campagne, true);

            $this->addFlash('success', $campagnename.' - votre nouvelle campagne vient d\'être créee avec succès !');
            return $this->redirectToRoute('app_campagne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campagne/index.html.twig', [
            'campagnes' => $campagnes,
            'campagne'  => $campagne,
            'form'      => $form,
            'form_up'   => $form,
            'nb'        => $nb,
        ]);
    }

    #[
        Route('/new', name: 'app_campagne_new', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function new(Request $request, CampagneRepository $campagneRepository): Response
    {
        $campagne = new Campagne();
        $form = $this->createForm(CampagneType::class, $campagne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campagneRepository->save($campagne, true);

            return $this->redirectToRoute('app_campagne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campagne/new.html.twig', [
            'campagne' => $campagne,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_campagne_show', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function show(Campagne $campagne): Response
    {
        return $this->render('campagne/show.html.twig', [
            'campagne' => $campagne,
        ]);
    }

    #[
        Route('/{id}/edit', name: 'app_campagne_edit', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function edit(Request $request, Campagne $campagne, CampagneRepository $campagneRepository): Response
    {
        $form = $this->createForm(CampagneType::class, $campagne);
        $campagne->setUpdatedAt(new DateTime());
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campagneRepository->save($campagne, true);

            $this->addFlash('success', 'Votre campagne vient d\'être modifiée avec succès');
            return $this->redirectToRoute('app_etape_campagne', ['id'=>$campagne->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campagne/edit.html.twig', [
            'campagne' => $campagne,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_campagne_delete', methods: ['POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function delete(Request $request, Campagne $campagne, CampagneRepository $campagneRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campagne->getId(), $request->request->get('_token'))) {
            $campagneRepository->remove($campagne, true);
        }

        return $this->redirectToRoute('app_campagne_index', [], Response::HTTP_SEE_OTHER);
    }

    #[
        Route('delete/{id}', name: 'app_campagne_del'),
        IsGranted('ROLE_ADMIN')
    ]
    public function deleteCampagne(Campagne $campagne, ManagerRegistry $doctrine): RedirectResponse
    {
        //Recuperer la campagne
        if($campagne) {
            //Si la campagne existe => la supprimer et retourner un flashMessage de succès
            $manager = $doctrine->getManager();
            //Ajoute la fonction suppression dans la transaction
            $manager->remove($campagne);
            //Executer la transaction
            $manager->flush();
            $this->addFlash('success',"La suppression de votre campagne a réussie avec succès");
        }else {
            // sinon retourner un message d'erreur
            $this->addFlash('error',"La campagne est introuvable");
        }

        return $this->redirectToRoute('app_campagne_index', [], Response::HTTP_SEE_OTHER);
    }
}
