<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                    'label' => 'Quoi',
                    'placeholder' => 'mots-clés '
            ] )
            ->add('city', EntityType::class, [
                    'mapped' => false,
                    'class' => City::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Paris 1',
                    'autocomplete' => true,
                    'label' => 'Où',
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label',
                    ],
                    'attr' => [
                        'class' => ''
                    ],
                ]
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
