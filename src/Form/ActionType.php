<?php

namespace App\Form;

use App\Entity\Action;
use App\Entity\Campagne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campagne',EntityType::class, [
                'class' => 'App\Entity\Campagne',
                'placeholder' => 'Selectionner une campagne'
            ])
            ->add('atterissage')
            ->add('tag',EntityType::class, [
                'class' => 'App\Entity\Tag',
                'placeholder' => 'Selectionner un tag'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
        ]);
    }
}
