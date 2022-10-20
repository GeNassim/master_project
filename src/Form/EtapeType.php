<?php

namespace App\Form;

use App\Entity\Etape;
use App\Repository\EnvoisRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EtapeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user',TextType::class, [
                'label' => ' Nom utilisateur *',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email',TextType::class, [
                'label' => ' Email utilisateur *',
                'attr'  => [
                    'class' => 'form-control',
                ]
            ])
            ->add('sujet',TextType::class, [
                'label' => ' Sujet de l\'étape *',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('message',TextareaType::class, [
                'label' => ' Le contenu du message *',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('delai')
            ->add('temps',TextType::class, [
                'label' => ' temps',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('campagne',TextType::class, [
                'label' => ' campagnes',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('ordre',TextType::class, [
                'label' => ' Envoyer après cet e-mail',
                'required'=>false,
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email_envoyes')
            ->add('fichier',FileType::class,[
                'label' =>'Ajouter un fichier',
                'mapped'=>false,
                'constrainsts' => [
                    new File([
                        'maxSize' => '1024K',
                        'mimeTypes'=> [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpg',
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Ajouter un fichier valide svp !',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etape::class,
        ]);
    }
}
