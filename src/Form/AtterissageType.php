<?php

namespace App\Form;

use App\Entity\Atterissage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AtterissageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => array('class' => 'form-control'),
                'label' => 'Nom de l\'étape *'
            ])
            ->add('url', TextType::class, [
                'label' => 'Chemin d\'accès *',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('visuel')
            ->add('slug')
            ->add('tunnel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Atterissage::class,
        ]);
    }
}
