<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Atterissage;
use App\Form\ActionType;
use App\Form\AtterissageType;
use App\Repository\ActionRepository;
use App\Repository\AtterissageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('dashboard/atterissage'),
    IsGranted('ROLE_ADMIN')
]
class AtterissageController extends AbstractController
{
    #[Route('/', name: 'app_atterissage_index', methods: ['GET'])]
    public function index(AtterissageRepository $atterissageRepository): Response
    {
        return $this->render('atterissage/index.html.twig', [
            'atterissages' => $atterissageRepository->findAll(),
        ]);
    }

    #[
        Route('/new', name: 'app_atterissage_new', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function new(Request $request, AtterissageRepository $atterissageRepository): Response
    {
        $atterissage = new Atterissage();
        $form = $this->createForm(AtterissageType::class, $atterissage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $atterissageRepository->save($atterissage, true);

            return $this->redirectToRoute('app_atterissage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('atterissage/new.html.twig', [
            'atterissage' => $atterissage,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_atterissage_show', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function show(Atterissage $atterissage): Response
    {
        return $this->render('atterissage/show.html.twig', [
            'atterissage' => $atterissage,
        ]);
    }

    #[
        Route('/{id}/edit', name: 'app_atterissage_edit', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function edit($id, Request $request, Atterissage $atterissage=null, AtterissageRepository $atterissageRepository, ActionRepository $actionRepository): Response
    {
        if($atterissage) {
            //Ajouter une action : START
            $action = new Action($atterissage);
            $action->setAtterissage($atterissage);
            $action_form = $this->createForm(ActionType::class, $action);
            $action_form->remove('atterissage');
            $action_form->handleRequest($request);
            

            if ($action_form->isSubmitted() && $action_form->isValid()) {
                $actionRepository->save($action, true);

                return $this->redirectToRoute('app_atterissage_edit', ['id' => $id], Response::HTTP_SEE_OTHER);
            }
            //Ajouter une action : FIN
            //LES ETAPES : DEBUT
            $atterissage = $atterissageRepository->find($atterissage);
            $tunnel      = $atterissage->getTunnel();
            $atterissages = $atterissageRepository->findBy(['tunnel'=>$tunnel]);
            foreach ($atterissages as $atterissage) {
                $at = $atterissage->getUrl();
            }
            
            //LES ETAPES : FIN

            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $visuel = "https"; 
            }else{
                $visuel = "http"; 
                $visuel .= "://"; 
                $visuel .= $_SERVER['HTTP_HOST']; 
                $visuel .= "/inscription/";
            }

            $form = $this->createForm(AtterissageType::class, $atterissage);
            //$form->remove("visuel");
            $form->remove("slug");
            $form->remove("tunnel");


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $slug   = $atterissage->getUrl();
                $atterissage->setVisuel($visuel.=$slug);
                $visuel = $atterissage->getVisuel();
                $atterissage->setSlug($slug);
                $atterissageRepository->save($atterissage, true);

                $this->addFlash('success', 'Paramàtres enregistrés avec succès');
                return $this->redirectToRoute('app_atterissage_edit', ['id' => $atterissage->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('atterissage/auto.html.twig', [
                'atterissage'  => $atterissage,
                'tunnel'       => $atterissage->getTunnel()->getName(),
                'form'         => $form,
                'action'       => $action_form,
                'atterissages' => $atterissages,
            ]);

        }else{
            $this->addFlash('error', 'Tunnel introuvable');
            return $this->redirectToRoute('app_tunnel_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[
        Route('/{id}', name: 'app_atterissage_delete', methods: ['POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function delete(Request $request, Atterissage $atterissage, AtterissageRepository $atterissageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$atterissage->getId(), $request->request->get('_token'))) {
            $atterissageRepository->remove($atterissage, true);
        }

        return $this->redirectToRoute('app_atterissage_index', [], Response::HTTP_SEE_OTHER);
    }
}
