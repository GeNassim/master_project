<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du tag *'
            ])
            ->add('inscrits_aujourdhui', TextType::class, [
                'label' => 'inscrits aujourdhui'
            ])
            ->add('inscrits_hier', TextType::class, [
                'label' => 'inscrits hier *'
            ])
            ->add('total_inscrits', TextType::class, [
                'label' => 'Total inscrit *'
            ])
            ->add('total_desinscrits', TextType::class, [
                'label' => 'Total desinscrits *'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
