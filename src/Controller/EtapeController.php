<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Entity\Envois;
use App\Entity\Etape;
use App\Form\EnvoisType;
use App\Form\EtapeType;
use App\Repository\ActionRepository;
use App\Repository\CampagneRepository;
use App\Repository\ClientsRepository;
use App\Repository\ContactRepository;
use App\Repository\EnvoisRepository;
use App\Repository\EtapeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/etape')]
class EtapeController extends AbstractController
{
    #[
        Route('/', name: 'app_etape_index', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function index(EtapeRepository $etapeRepository): Response
    {
        return $this->render('etape/index.html.twig', [
            'etapes' => $etapeRepository->findAll(),
        ]);
    }

    #[
        Route('-list/campagne-{id}', name: 'app_etape_campagne', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function EtapeByCampagne(Campagne $campagne=null, CampagneRepository $campagneRepository, 
            EtapeRepository $etapeRepository, ActionRepository $actionRepository, 
            ClientsRepository $clientsRepository, EnvoisRepository $envoisRepository
    ): Response
    {

        if($campagne) {
            
            //$etapes = $etapeRepository->findBy(['campagne' => $campagne]);
            $etapes = $etapeRepository->findEtapeByOrdre($campagne);
            return $this->render('etape/index.html.twig', [
                'etapes'   => $etapes,
                'nbe'      => count($etapes),
                'campagne' => $campagne,
            ]);

        }else{
            $this->addFlash('error', ' La campagne est Introuvable');
            return $this->redirectToRoute('app_campagne_index', [], Response::HTTP_SEE_OTHER);
        }
        
    }

    #[
        Route('/new/{id}', name: 'app_etape_new', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function new(Campagne $campagne=null, Request $request, 
    EtapeRepository $etapeRepository, CampagneRepository $campagneRepository, 
    ManagerRegistry $doctrine, ActionRepository $actionRepository, ClientsRepository $clientsRepository,
    ContactRepository $contactRepository): Response
    {
        if($campagne) {
            $etapes_ordonnees = $etapeRepository->findEtapeByOrdre($campagne);
            
            $etape = new Etape($campagne);
            $etape->setCampagne($campagne);

            if(isset($_POST['submit'])) {
                $username = trim($_POST['_username']);
                $usermail = trim($_POST['_useremail']);
                $sujet    = trim($_POST['_sujet']);
                $corps    = trim($_POST['_corps']);
                $delai    = trim($_POST['_delai']);
                $temps    = trim($_POST['_temps']);
                $fichier  = $_FILES["fichier"]["name"];
                
                for($i=0; $i < count($fichier); $i++) {
                    $file_tmp = $_FILES["fichier"]["tmp_name"][$i];
                    $file_name = $_FILES["fichier"]["name"][$i];
                    move_uploaded_file($file_tmp, "assets/images/mes_offres/" . $file_name);
                    
                }

                if($delai == 0 && $temps == 'minutes'){
                    $delai = $delai + 1;
                }else{
                    $delai    = trim($_POST['_delai']);
                }

                if(empty($etapes_ordonnees)){
                    $ordre_etape_suivant    = 1;
                }elseif(!empty($etapes_ordonnees) && empty($_POST['ordre'])){
                    $email_error = 0;
                    
                    $this->addFlash('ch_error','Selectionner l\'e-mail à envoyer avant ce dernier ?');
                    //return $this->redirectToRoute('app_etape_new', ['id'=>$campagne->getId()], Response::HTTP_SEE_OTHER);
                    return $this->renderForm('etape/new.html.twig', [
                        'etape'    => $etape,
                        'etapes'   => $etapes_ordonnees,
                        'campagne' => $campagne,
                        'sujet'    => $sujet,
                        'corps'    => $corps,
                        'delai'    => $delai,
                        'temps'    => $temps,
                        'email_error'    => $email_error,
                    ]);
                    
                }else{
                    $step = trim($_POST['ordre']);
                    $step = $etapeRepository->find($step);

                    if(empty($step)){
                        $email_error = 0;
                        $this->addFlash('ch_error','Selectionner l\'e-mail à envoyer avant ce dernier ?');
                        //return $this->redirectToRoute('app_etape_new', ['id'=>$campagne->getId()], Response::HTTP_SEE_OTHER);
                        return $this->renderForm('etape/new.html.twig', [
                            'etape'    => $etape,
                            'etapes'   => $etapes_ordonnees,
                            'campagne' => $campagne,
                            'sujet'    => $sujet,
                            'corps'    => $corps,
                            'delai'    => $delai,
                            'temps'    => $temps,
                            'email_error'    => $email_error,
                        ]);
                    }

                    $etape_precedent = $step->getOrdre();
                    $ordre_etape_suivant = $etape_precedent + 1;

                    //Verifier si le delai saisi correspond
                    if($delai < 0){ 
                        $this->addFlash('error',"Une étape ne peut pas être programmée avec une valeur négative");
                        //return $this->redirectToRoute('app_etape_new', ['id'=>$campagne->getId()], Response::HTTP_SEE_OTHER);
                        return $this->renderForm('etape/new.html.twig', [
                            'etape'    => $etape,
                            'etapes'   => $etapes_ordonnees,
                            'campagne' => $campagne,
                            'sujet'    => $sujet,
                            'corps'    => $corps,
                            'delai'    => $delai,
                            'temps'    => $temps,
                            'step'     =>$step,
                            'email_error' =>'',
                        ]);
                    }
                    

                    $ordre_superieurs = $etapeRepository->findEtapeByOrdreSup($campagne, $etape_precedent);
                    foreach ($ordre_superieurs as $ordre_superieur) {
                        $ordre_add = $ordre_superieur->getOrdre();
                        $ordre_superieur->setOrdre($ordre_add+1);
                        $manager = $doctrine->getManager();
                        $manager->persist($ordre_superieur);
                        $manager->flush();
                    }
                }

                $etape->setUser($username);
                $etape->setEmail($usermail);
                $etape->setSujet($sujet);
                $etape->setMessage($corps);
                $etape->setDelai($delai);
                $etape->setTemps($temps);
                $etape->setOrdre($ordre_etape_suivant);
                $etape->setEmailEnvoyes(0);
                $etape->setFile($file_name);

                $etapeRepository->save($etape, true);

                $nb_campagne = count($etapeRepository->findBy(['campagne'=>$campagne]));
                $campagne->setNbEmails($nb_campagne);
                $manager = $doctrine->getManager();
                $manager->persist($campagne);
                $manager->flush();


                $this->addFlash('success','votre étape est créee avec succès !');
                return $this->redirectToRoute('app_etape_campagne', ['id'=>$campagne->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('etape/new.html.twig', [
                'etape'    => $etape,
                'etapes'   => $etapes_ordonnees,
                'campagne' => $campagne,
                'sujet' => '',
                'corps' => '',
                'delai' => '',
                'temps' => '',
                'email_error' =>'',
                'step'     =>"",
            ]);

        }else{
            $this->addFlash('error', ' La campagne est Introuvable');
            return $this->redirectToRoute('app_campagne_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[
        Route('/{id}', name: 'app_etape_show', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function show(Etape $etape): Response
    {
        return $this->render('etape/show.html.twig', [
            'etape' => $etape,
        ]);
    }

    #[
        Route('/{id}/edit', name: 'app_etape_edit', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function edit(Request $request, Etape $etape, EtapeRepository $etapeRepository): Response
    {
        $form = $this->createForm(EtapeType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etapeRepository->save($etape, true);

            return $this->redirectToRoute('app_etape_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etape/edit.html.twig', [
            'etape' => $etape,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_etape_delete', methods: ['POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function delete(Request $request, Etape $etape, EtapeRepository $etapeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etape->getId(), $request->request->get('_token'))) {
            $etapeRepository->remove($etape, true);
        }

        return $this->redirectToRoute('app_etape_index', [], Response::HTTP_SEE_OTHER);
    }
}
