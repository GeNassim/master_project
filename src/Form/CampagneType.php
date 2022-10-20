<?php

namespace App\Form;

use App\Entity\Campagne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampagneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => ' Nom de la campagne *',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            //rgba(13, 110, 253, 0.25)
            ->add('description',TextareaType::class, [
                'label' => 'Description * ',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('active',CheckboxType::class, [
                'label' => 'Activer la campagne ',
                'required' => false
            ])
            ->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campagne::class,
        ]);
    }
}
