<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\ActionRepository;
use App\Repository\AtterissageRepository;
use App\Repository\ClientsRepository;
use App\Repository\ContactRepository;
use App\Repository\TagRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/tag')]
class TagController extends AbstractController
{
    #[
        Route('-list/', name: 'app_tag_index', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function index(ManagerRegistry $doctrine,Tag $tag=null, 
    Request $request, TagRepository $tagRepository, 
    ContactRepository $contactRepository, AtterissageRepository $atterissageRepository, 
    ActionRepository $actionRepository, ClientsRepository $clientsRepository): Response
    {
        $tag_new = new Tag();
        $form = $this->createForm(TagType::class, $tag_new);
        $form->remove('inscrits_aujourdhui');
        $form->remove('inscrits_hier');
        $form->remove('total_inscrits');
        $form->remove('total_desinscrits');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagname = $tagRepository->findBy(['name'=>$tag_new->getName()]);
            if($tagname){
                $this->addFlash('error', "<<".$tag_new->getName().">>".' est déjà enregistré dans le système');
                return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $tagRepository->save($tag_new, true);
                $this->addFlash('success', 'vous venez de créer un nouveau tag');
                return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
            }
            
        }

        $date_d_jour = new DateTime();
        $date_du_jour         = $date_d_jour->format('d-m-Y');

        $day = $date_d_jour;
        $previous_day = clone $day;
        $pr_jr = $previous_day->sub(new \DateInterval('P1D'));

        $tags  = $tagRepository->findAll();
        foreach ($tags as $tag) {
            //echo("<meta http-equiv='refresh' content='86280'>");
            echo("<meta http-equiv='refresh' content='43200'>");
            /*$inscrits_aujourdhui = count($contactRepository->findBy(['createdAt'=>$date_d_jour,'tag'=>$tag]));
            $inscrits_hier       = count($contactRepository->findBy(['createdAt'=>$pr_jr,'tag'=>$tag]));
            $total               = count($contactRepository->findBy(['tag'=>$tag]));*/

            $actions = $actionRepository->findBy(['tag'=>$tag]);
            foreach ($actions as $action) {
                $action = $action->getAtterissage();
                $clients = $clientsRepository->findBy(['atterissage'=>$action]);
                $contacts = $contactRepository->findBy(['tag'=>$tag,'clients'=>$clients]);

                $inscrits_aujourdhui = count($contactRepository->findBy(['created'=>$date_d_jour,'tag'=>$tag,'clients'=>$clients]));
                $inscrits_hier       = count($contactRepository->findBy(['created'=>$pr_jr,'tag'=>$tag,'clients'=>$clients]));
                $total               = count($contacts);
            
                $manager = $doctrine->getManager();
                $tag->setInscritsAujourdhui($inscrits_aujourdhui);
                $tag->setInscritsHier($inscrits_hier);
                $tag->setTotalInscrits($total);
                $manager->persist($tag);
                $manager->flush();
                
            }
        }
        
        if(!empty($tag)){
            $tag_name = $tag->getName();
        }else{
            $tag_name = '';
        }

        return $this->renderForm('tag/index.html.twig', [
            'tags'     => $tags,
            'nb'       => count($tags),
            'tag'      => $tag,
            'form'     => $form,
            'tagname'  =>$tag_name,
        ]);
    }

    #[
        Route('/new', name: 'app_tag_new', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function new(Request $request, TagRepository $tagRepository): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagRepository->save($tag, true);

            return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tag_show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[
        Route('/{id}/edit', name: 'app_tag_edit', methods: ['GET', 'POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function edit(
        Request $request, Tag $tag=null, TagRepository $tagRepository, 
        ContactRepository $contactRepository, ActionRepository $actionRepository,
        ClientsRepository $clientsRepository,$id
    ): Response
    {   
        $date_d_jour = new DateTime();
        $day = $date_d_jour;
        $previous_day = clone $day;
        $pr_jr = $previous_day->sub(new \DateInterval('P1D'));

        if($tag) {
            $actions = $actionRepository->findBy(['tag'=>$tag]);
            foreach ($actions as $action) {
                $action = $action->getAtterissage();
                $clients = $clientsRepository->findBy(['atterissage'=>$action]);
                $contacts = $contactRepository->findBy(['tag'=>$tag,'clients'=>$clients]);

                $inscrits_aujourdhui = count($contactRepository->findBy(['created'=>$date_d_jour,'tag'=>$tag,'clients'=>$clients]));
                $inscrits_hier       = count($contactRepository->findBy(['created'=>$pr_jr,'tag'=>$tag,'clients'=>$clients]));
                $total               = count($contacts);

                $form = $this->createForm(TagType::class, $tag);
                $form->remove('inscrits_aujourdhui');
                $form->remove('inscrits_hier');
                $form->remove('total_inscrits');
                $form->remove('total_desinscrits');
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $tagRepository->save($tag, true);

                    $this->addFlash('success',' vient d\'etre modifié avec succès');
                    return $this->redirectToRoute('app_tag_edit', ['id'=>$tag->getId()], Response::HTTP_SEE_OTHER);
                }

                return $this->renderForm('tag/edit.html.twig', [
                    'tag'      => $tag,
                    'form'     => $form,
                    'contacts' =>$contacts,
                    'jour'     =>$inscrits_aujourdhui,
                    'hier'     =>$inscrits_hier,
                    'total'    =>$total,
                    'tagname'  =>$tag->getName(),
                ]);
                
            }
        }else{
            $this->addFlash('error','Le tag d\'id '.$id.' est Introuvable');
            return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(TagType::class, $tag);
        $form->remove('inscrits_aujourdhui');
        $form->remove('inscrits_hier');
        $form->remove('total_inscrits');
        $form->remove('total_desinscrits');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagRepository->save($tag, true);

            $this->addFlash('success',' vient d\'etre modifié avec succès');
            return $this->redirectToRoute('app_tag_edit', ['id'=>$tag->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tag/edit.html.twig', [
            'tag'      => $tag,
            'form'     => $form,
            'contacts' =>'',
            'jour'     =>'',
            'hier'     =>'',
            'total'    =>'',
            'tagname'  =>$tag->getName(),
        ]);
    }

    #[
        Route('/{id}', name: 'app_tag_delete', methods: ['POST']),
        IsGranted('ROLE_ADMIN')
    ]
    public function delete(Request $request, Tag $tag=null, TagRepository $tagRepository, ActionRepository $actionRepository): Response
    {
        if($tag) {
            $action_tag = $actionRepository->findBy(['tag'=>$tag]);
            if(!empty($action_tag)){
                $this->addFlash('info', 'vous ne pouvez pas supprimer ce tag car des contacts, ce sont déjà inscrits');
                return $this->redirectToRoute('app_tag_edit', ['id'=>$tag->getId()], Response::HTTP_SEE_OTHER);
            }else{

                if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
                    $tagRepository->remove($tag, true);
                }
                
                $this->addFlash('success', 'votre tag a été supprimé du système avec succès');
                return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        
    }
}
