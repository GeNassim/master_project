<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Contact;
use App\Entity\Desabonne;
use App\Entity\Envois;
use App\Entity\Tag;
use App\Form\ClientsType;
use App\Repository\ActionRepository;
use App\Repository\AtterissageRepository;
use App\Repository\ClientsRepository;
use App\Repository\ContactRepository;
use App\Repository\DesabonneRepository;
use App\Repository\EnvoisRepository;
use App\Repository\EtapeRepository;
use App\Repository\TagRepository;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/nouveaux')]
class ClientsController extends AbstractController
{
    #[Route('/inscription/{id}-reussie', name: 'app_clients_index', methods: ['GET'])]
    public function index(ClientsRepository $clientsRepository,$id): Response
    {
        return $this->render('clients/thanks.html.twig', [
            'clients' => $clientsRepository->findAll(),
            'id_client'=>$id,
        ]);
    }

    #[Route('/inscription/{url}', name: 'app_clients_new', methods: ['GET', 'POST'])]
    public function new(Tag $tag=null, ManagerRegistry $doctrine, 
    ContactRepository $contactRepository, TagRepository $tagRepository, 
    ActionRepository $actionRepository, Request $request, 
    ClientsRepository $clientsRepository, 
    AtterissageRepository $atterissageRepository, 
    $url, EtapeRepository $etapeRepository, EnvoisRepository $envoisRepository): Response
    {
        $tags = $tagRepository->findAll();

        $dernier_contact = $contactRepository->findOneBy([], ['id' => 'desc'],1,0);
        //$dernier_date    = $dernier_contact->getCreatedAt();
        //$dernier_date = $dernier_date->format('d-m-Y');
        $date_d_jour = new DateTime();
        
        // Format Date
        $day = new \DateTime();
        $date_errors = \DateTime::getLastErrors();

        if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
            throw $this->createNotFoundException('Input date does not exist.');
        }

        $next_day = clone $day;
        $next_day->add(new \DateInterval('P1D'));

        $previous_day = clone $day;
        $pr_jr = $previous_day->sub(new \DateInterval('P1D'));

        $url     = $atterissageRepository->findOneBy(['url' => $url]);
        $action  = $actionRepository->findBy(['atterissage'=>$url]);
        $actions = $actionRepository->findByAtrAndTag($url,2);

        //$tags = $tagRepository->findAll();
        $actions = $actionRepository->findBy(['atterissage'=>$url,'tag'=>$tags]);
        foreach ($actions as $action) {
            $tag = $action->getTag();
        }
        if($tag){
            $inscrits_aujourdhui = count($contactRepository->findBy(['created'=>$date_d_jour,'tag'=>$tag]));
            
            $total               = count($contactRepository->findBy(['tag'=>$tag]));
            $manager = $doctrine->getManager();
            $tag->setInscritsAujourdhui($inscrits_aujourdhui + 1);
            $tag->setTotalInscrits($total + 1);
        }

        

        $client = new Clients($url);
        $client->setAtterissage($url);
        $form = $this->createForm(ClientsType::class, $client);
        $form->remove('atterissage');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientsRepository->save($client, true);
            
            $manager = $doctrine->getManager();
            $contact = new Contact;
            $contact->setTag($tag);
            $contact->setClients($client);
            $contact->setCreated(new \DateTime());
            $contact->setAt(new \DateTime());
            $contactRepository->save($contact,true);

            $manager->persist($contact);
            $manager->flush();

            //========================
            $date_inscrie = $contact->getCreated();
            $heure_inscrit = $contact->getAt();
            $format_heure   = $heure_inscrit->format('H:i');
            $string_heure   = strtotime($format_heure);
            //========================

            //Chercher le tunnel correspondant au client
            $atterissage = $atterissageRepository->find($url);
            $actions = $actionRepository->findBy(['atterissage'=>$atterissage]);
            foreach ($actions as $action) {
                $campagne = $action->getCampagne();
                $etapes = $etapeRepository->findBy(['campagne'=>$campagne]);
                foreach ($etapes as $etape) {
                    $order = $etape->getOrdre();
                    $delai = $etape->getDelai();
                    $temps = $etape->getTemps();
                    $pre = $etape->getPreEtape();

                    if($temps == 'minutes'){
                        //$date = new DateTime();
                        $date = date('Y-m-d');
                        $heure_envoi = $delai * 60;
                        $envoi = $string_heure + $heure_envoi;
                        $envois_date   = date('H:i',$envoi);
                    }elseif($temps == 'heures'){
                        $date = date('Y-m-d');
                        $heure_envoi = $delai * 3600;
                        $envoi = $string_heure + $heure_envoi;
                        $envois_date   = date('H:i',$envoi);
                    }elseif($temps == 'jours'){
                        //$envois_date   = date_add($register_date, date_interval_create_from_date_string($delai."1 day"));
                        //$envois_date   = date_add($register_date, date_interval_create_from_date_string("1 day"));
                        //dump("JOURS ".$etape->getSujet()." ".$client->getId()." ".$client->getEmail());
                    }

                    $manager = $doctrine->getManager();
                    $envois = new Envois();
                    $envois->setEtape($etape);
                    $envois->setClient($client);
                    $envois->setDate($date);
                    $envois->setHeure($envois_date);
                    $envois->setEnvoyes(0);
                    $manager->persist($envois);
                    $envoisRepository->save($envois, true);
                 
                }
            }

            return $this->redirectToRoute('app_clients_index', ['id'=>$client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clients/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[
        Route('/{id}', name: 'app_clients_show', methods: ['GET']),
        IsGranted('ROLE_ADMIN')
    ]
    public function show(Clients $client): Response
    {
        return $this->render('clients/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[
        Route('/{id}/edit', name: 'app_clients_edit', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function edit(Request $request, Clients $client, ClientsRepository $clientsRepository): Response
    {
        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientsRepository->save($client, true);

            return $this->redirectToRoute('app_clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clients/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[
        Route('/ky/{id}-desabonner', name: 'app_clients_delete', methods: ['GET', 'POST'])
    ]
    public function delete(Clients $clients=null,Request $request, Clients $client, ManagerRegistry $doctrine, 
    ClientsRepository $clientsRepository, ContactRepository $contactRepository,
    EnvoisRepository $envoisRepository,AtterissageRepository $atterissageRepository,
    DesabonneRepository $desabonneRepository, $id): Response
    {
        //recuperer le client
        $clients = $clientsRepository->findBy(['id'=>$id]);
        foreach ($clients as $client) {
            //incrementer le le client desinscrit
            $id_cli = $client->getId();
            $desabonne = new Desabonne;
            $desabonne->setClient($id_cli);
            $manager = $doctrine->getManager();
            $manager->persist($desabonne);
            $manager->flush();
            //Recuperer le client à desinscrir dans Contact et le supprimer
            $desabonnes = $desabonneRepository->findBy(['client'=>$id]);
            foreach ($desabonnes as $desabonne) {
                $cli = $desabonne->getClient();
                $contact = $contactRepository->findBy(['clients'=>$cli]);
                foreach ($contact as $_contact) {
                    if($_contact){
                        //decrementer le contact desinscrit dans contact
                        $manager = $doctrine->getManager();
                        $manager->remove($_contact);
                        $manager->flush();
                    }
                    
                }

                 //Recuperer le contact à desinscrir dans envois et le supprimer
                $envois = $envoisRepository->findBy(['client'=>$cli]);
                foreach ($envois as $_envois) {
                    if($_envois){
                        //decrementer le contact desinscrit dans contact
                        $manager = $doctrine->getManager();
                        $manager->remove($_envois);
                        $manager->flush();
                    }
                    
                }

                 //Recuperer le contact à desinscrir dans clients et le supprimer
                 $clients = $clientsRepository->findBy(['id'=>$cli]);
                 foreach ($clients as $_clients) {
                     if($_clients){
                         //decrementer le contact desinscrit dans contact
                         $manager = $doctrine->getManager();
                         $manager->remove($_clients);
                         $manager->flush();
                     }
                     
                 }

                 //return $this->redirectToRoute('app_clients_describe', [], Response::HTTP_SEE_OTHER);
            }
                    
               
        }

        
        /*if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $clientsRepository->remove($client, true);
        }*/
        //return $this->redirectToRoute('app_clients_describe', [], Response::HTTP_SEE_OTHER);
        return $this->render('clients/describe.html.twig', [
            'url' => '$url',
        ]);
        
    }
}
