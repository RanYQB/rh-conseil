<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Recruiter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecruiterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'entreprise'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('zipcode', EntityType::class, [
                    'mapped' => false,
                    'class' => City::class,
                    'choice_label' => 'zipcode',
                    'placeholder' => '75001',
                    'autocomplete' => true,
                    'label' => 'Code postal',
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label',
                    ],
                    'attr' => [
                        'class' => ''
                    ],
                ]
            )
            ->add('city', EntityType::class, [
                    'mapped' => false,
                    'class' => City::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Paris 1',
                    'autocomplete' => true,
                    'label' => 'Commune',
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
            'data_class' => Recruiter::class,
        ]);
    }
}
