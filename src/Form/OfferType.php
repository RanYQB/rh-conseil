<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Offer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
                [
                    'label' => 'Intitulé du poste'
                ])
            ->add('description', TextType::class,[
                'label' => 'Description'
            ])
            ->add('salary', NumberType::class , [
                'label' => 'Salaire mensuel',
                ])
            ->add('contrat_type', ChoiceType::class, [
                'label' => 'Type de contrat',
                'expanded' => false,
                'multiple' => false,
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                ],
            ])
            ->add('positions', NumberType::class, [
                'label' => 'Nombre de postes à pourvoir'
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
            'data_class' => Offer::class,
        ]);
    }
}
