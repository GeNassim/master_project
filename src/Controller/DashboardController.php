<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use App\Repository\DesabonneRepository;
use App\Repository\EnvoisRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[
        Route('/dashboard', name: 'app_dashboard'),
        IsGranted('ROLE_ADMIN')
    ]
    public function index(ContactRepository $contactRepository, DesabonneRepository $desabonneRepository, EnvoisRepository $envoisRepository): Response
    {
        //==============
        $aujourdhui = new DateTime();
        $_aujourdhui = date('Y-m-d');
        //==============
        $contacts = $contactRepository->findAll();
        if ($contacts) {
            $total = count($contacts);
            $nb = count($contactRepository->findBy(['created'=>$aujourdhui]));
        }else{
            $nb = 0;
            $total = 0;
        }
        $envois = $envoisRepository->findAll();
        if ($envois) {
            $nbe = count($envoisRepository->findBy(['date'=>$_aujourdhui,'envoyes'=>1]));
        }else{
            $nbe = 0;
        }
        $nbd = count($desabonneRepository->findAll());

        return $this->render('dashboard/index.html.twig', [
            'nb' => $nb,
            'nbe' => $nbe,
            'nbd' => $nbd,
            'total'=>$total
        ]);
    }

    #[
        Route('/dashboard/documentation', name: 'app_documentation'),
        IsGranted('ROLE_ADMIN')
    ]
    public function docs(ContactRepository $contactRepository, EnvoisRepository $envoisRepository): Response
    {
        return $this->render('dashboard/documentation.html.twig', [
            
        ]);
    }
}
