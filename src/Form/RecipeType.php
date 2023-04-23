<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('instructions', TextareaType::class, [
                'label' => 'Ingredients ?',
                'attr' => [
                    'placeholder' => 'Ex: potatoes, ham,',
                    'rows' => 3,
                ],
            ])
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Get your recipe',
                    'attr' => [
                        'hx-post' => '/',
                        'hx-target' => '#response',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
