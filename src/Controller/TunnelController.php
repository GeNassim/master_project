<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Atterissage;
use App\Entity\Tunnel;
use App\Form\ActionType;
use App\Form\TunnelType;
use App\Repository\AtterissageRepository;
use App\Repository\TunnelRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/tunnel')]
class TunnelController extends AbstractController
{
    #[
        Route('-list/', name: 'app_tunnel_index', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function index(Request $request, ManagerRegistry $doctrine, AtterissageRepository $atterissageRepository, TunnelRepository $tunnelRepository): Response
    {
        $manager = $doctrine->getManager();

        $string = "";
        $chaine = "abcdefghijklmnopqrstuvwxy1234567890";
        srand((double)microtime()*1000000);
        for($i=0; $i < 9; $i++) {
            $string .= $chaine[rand()%strlen($chaine)];
        }

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $visuel = "https"; 
        }else{
            $visuel = "http"; 
            
            // Ajoutez // à l'URL.
            $visuel .= "://"; 
                
            // Ajoutez l'hôte (nom de domaine, ip) à l'URL.
            $visuel .= $_SERVER['HTTP_HOST']; 
                
            // Ajouter l'emplacement de la ressource demandée à l'URL
            $visuel .= "/inscription".$string; 
            //dd($visuel);
        }

        $atterissage = new Atterissage;
        $atterissage->setName("Page de capture");
        $atterissage->setUrl($string);
        $atterissage->setVisuel($visuel);
        $atterissage->setSlug($string);

        $tunnels = $atterissageRepository->findAll();
        $nb      = count($tunnels);

        $tunnel  = new Tunnel();
        $form    = $this->createForm(TunnelType::class, $tunnel);
        $tunnel->setCreatedAt(new DateTime());
        $tunnel->setUpdatedAt(new DateTimeImmutable());
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tunnelRepository->save($tunnel, true);

            $tunnel->addAtterissage($atterissage);
            $manager->persist($atterissage);
            $manager->flush();

            $this->addFlash('success','Votre nouveau tunnel vient d\'être crée avec succès !');
            return $this->redirectToRoute('app_tunnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tunnel/index.html.twig', [
            'tunnels'    => $tunnels,
            'nb'         => $nb,
            'tunnel'     => $tunnel,
            'form'       => $form,
            'tunnelname' => $tunnel->getName(),
        ]);
    }
    #[
        Route('tunnel-list/', name: 'app_tunnel_list', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function Tunnel_list(ManagerRegistry $doctrine,AtterissageRepository $atterissageRepository,TunnelRepository $tunnelRepository): Response
    {
        $repository = $doctrine->getRepository(Atterissage::class);
        $tunnels    = $repository->findBy([],[]);
        dd($tunnels);
        $tunnel = new Tunnel();
        
        return $this->render('atterissage/index.html.twig', [
            'atterissages' => $atterissageRepository->findAll(),
        ]);
    }

    #[
        Route('/new', name: 'app_tunnel_new', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function new(Request $request, TunnelRepository $tunnelRepository, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $tunnel = new Tunnel();

        $string = "";
        $chaine = "abcdefghijklmnopqrstuvwxy1234567890";
        srand((double)microtime()*1000000);
        for($i=0; $i < 9; $i++) {
            $string .= $chaine[rand()%strlen($chaine)];
        }

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $visuel = "https"; 
        }else{
            $visuel = "http"; 
            
            // Ajoutez // à l'URL.
            $visuel .= "://"; 
                
            // Ajoutez l'hôte (nom de domaine, ip) à l'URL.
            $visuel .= $_SERVER['HTTP_HOST']; 
                
            // Ajouter l'emplacement de la ressource demandée à l'URL
            $visuel .= "/inscription".$string; 
            //dd($visuel);
        }

        
        $atterissage = new Atterissage;
        $atterissage->setName("Page de capture");
        $atterissage->setUrl($string);
        $atterissage->setVisuel($visuel);
        $atterissage->setSlug($string);

        $form = $this->createForm(TunnelType::class, $tunnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tunnelRepository->save($tunnel, true);

            $tunnel->addAtterissage($atterissage);
            $manager->persist($atterissage);
            $manager->flush();

            return $this->redirectToRoute('app_atterissage_edit', ['id'=>$atterissage->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tunnel/new.html.twig', [
            'tunnel' => $tunnel,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_tunnel_show', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function show(Tunnel $tunnel): Response
    {
        return $this->render('tunnel/show.html.twig', [
            'tunnel' => $tunnel,
        ]);
    }

    #[
        Route('/{id}/edit', name: 'app_tunnel_edit', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function edit(Request $request, Tunnel $tunnel, TunnelRepository $tunnelRepository): Response
    {
        /*$action = new Action($id);
        $action->setAtterissage($id);
        $action_form = $this->createForm(ActionType::class, $action);
        $action_form->handleRequest($request);*/
        $form = $this->createForm(TunnelType::class, $tunnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tunnelRepository->save($tunnel, true);

            return $this->redirectToRoute('app_tunnel_index', [], Response::HTTP_SEE_OTHER);
            //return $this->redirectToRoute('app_atterissage_edit', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tunnel/edit.html.twig', [
            'tunnel' => $tunnel,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_tunnel_delete', methods: ['POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function delete(Request $request, Tunnel $tunnel, TunnelRepository $tunnelRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tunnel->getId(), $request->request->get('_token'))) {
            $tunnelRepository->remove($tunnel, true);
        }

        return $this->redirectToRoute('app_tunnel_index', [], Response::HTTP_SEE_OTHER);
    }
}
