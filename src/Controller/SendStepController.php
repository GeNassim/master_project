<?php

namespace App\Controller;

use App\Entity\Envois;
use App\Entity\Etape;
use App\Form\EnvoisType;
use App\Form\EtapeType;
use App\Repository\ActionRepository;
use App\Repository\ClientsRepository;
use App\Repository\ContactRepository;
use App\Repository\EnvoisRepository;
use App\Repository\EtapeRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Time;

class SendStepController extends AbstractController
{
    #[
        Route('/send/step', name: 'app_send_step'),
        IsGranted('ROLE_ADMIN')
    ]
    public function index(Request $request, EtapeRepository $etapeRepository, 
                        ActionRepository $actionRepository,
                        ClientsRepository $clientsRepository,
                        EnvoisRepository $envoisRepository, MailerInterface $mailer,
                        ManagerRegistry $doctrine, ContactRepository $contactRepository): Response
    {
        echo("<meta http-equiv='refresh' content='60'>");
        //================
        error_reporting(E_ALL);
                ini_set("display_errors", 1);
                date_default_timezone_set('Europe/Paris');
        $_aujourdhui = date('Y-m-d');
        $_heure      = date('H:i');
        //================
        $envoi_des_etapes = $envoisRepository->findBy(['date'=>$_aujourdhui,'heure'=>$_heure]);
        foreach ($envoi_des_etapes as $envoi_des_etape) {
            if($envoi_des_etape) {
                $id_envoi   = $envoi_des_etape->getId();
                $id_etape   = $envoi_des_etape->getEtape();
                $id_clients = $envoi_des_etape->getClient();
                $clients    = $clientsRepository->findBy(['id'=>$id_clients]);
                foreach ($clients as $_clients) {
                    $atterissage = $_clients->getAtterissage();
                    $actions     = $actionRepository->findBy(['atterissage'=>$atterissage]);
                    foreach ($actions as $action) {
                        $campagne = $action->getCampagne();
                        $etapes    =  $etapeRepository->findBy(['campagne'=>$campagne,'id'=>$id_etape]);
                        foreach ($etapes as $etape) {

                            $exp_name  = $etape->getUser();
                            $exp_mail  = $etape->getEmail();
                            $subject   = $etape->getSujet();
                            $corps     = $etape->getMessage();
                            $dest_mail = $_clients->getEmail();
                            $dest_name = $_clients->getFirstname();

                            $mail = (new TemplatedEmail())
                            ->from($exp_mail)
                            ->to($dest_mail)
                            ->subject($subject)
                            ->htmlTemplate('sendstep/mail.html.twig')
                            ->context([
                                'prenom'   => $dest_name,
                                'sujet'    => $subject,
                                'corps'    => $corps,
                            ]);

                            $mailer->send($mail);
                            if($mailer->send($mail)) {
                                $manager = $doctrine->getManager();
                                $envoi_des_etape->setEnvoyes(1);
                                $manager->persist($envoi_des_etape);
                                $manager->flush();

                                //Nombre d'etapes envoyes
                                $nb_envoyes = $envoisRepository->findBy(['etape'=>$id_etape,'envoyes'=>1]);
                                $nbe = count($nb_envoyes);
                                $etape->setEmailEnvoyes($nbe);
                                $manager->persist($etape);
                                $manager->flush();
                            }
                            
                        }
                        
                    }
                }
            }
        }

        $actuel_heure = strtotime(date('H:i'));
        $normal_heure = date('H:i',$actuel_heure);
        $date_du_jour = date('d/m/Y',strtotime("today"));

        #TA DATE MAINTENANT
        $my_date=date("Y-m-d H:i:s");

        #TA DATE EN TIME
       // $my_date_time=time("Y-m-d H:i:s");
        $my_date_time = time();

        #TU AJOUTES LE NOMBRE DE SECONDE DESIRE (ici 1h30 = 5400 secondes)
        $my_new_date_time=$my_date_time+5400;

        #TU REPASSE EN FORMAT DATE
        $my_new_date=date("Y-m-d H:i:s",$my_new_date_time);

        dump($my_new_date);

        $one_year = new DateInterval('P1Y');
        $one_year_ago = new DateTime();
        $one_year_ago->sub($one_year);

        $one_year_ago = new DateInterval( "P1Y" );
        $one_year_ago->invert = 1;
        $one_year_ago = new DateTime();
        $g = $one_year_ago->add($one_year);
        dump($g);

        $day = new DateTime();
        $next_day = clone $day;
        $l = $next_day->add(new \DateInterval('P1D'));
        dump($l);

        $delai = 3;
        $etapes = $etapeRepository->findBy(['delai'=>$delai]);
        foreach ($etapes as $etape) {
            $campagnes    = $etape->getCampagne();
            $actions = $actionRepository->findBy(['campagne'=>$campagnes]);
            foreach ($actions as $action) {
                $atterissages = $action->getAtterissage();
                die();
                $clients      = $clientsRepository->findBy(['atterissage'=>$atterissages]);
                
                foreach($clients as $client){
                    
                    $envoi = new Envois;
                    $form = $this->createForm(EnvoisType::class, $envoi);
                    $envoi->setEtape($etape);
                    $envoi->setClient($client);
                    //$envoi->setCreatedAt(new DateTimeImmutable());
                    $form->handleRequest($request);
                    $manager = $doctrine->getManager();
                    $manager->persist($envoi);
                    $manager->flush();

                    //Recuperer le nombre d'envois
                    $nombres = $envoisRepository->findBy(['etape'=>$etape->getId()]);
                    $nb      = count($nombres);

                    $etape->setEmailEnvoyes($nb);
                    $manager = $doctrine->getManager();
                    $manager->persist($etape);
                    $manager->flush();
                }
            }
        }
        //dump($etapes);
        return $this->render('send_step/index.html.twig', [
            'controller_name' => 'SendStepController',
        ]);
    }
}
