<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'required' => true,
                'constraints' => [new Length([
                    'min' => 2,
                    'max' => 100
                    ])],
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'constraints' => [new Length([
                    'min' => 2,
                    'max' => 100
                    ])],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [new Length([
                    'min' => 4,
                    'max' => 20
                    ])],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [new Length([
                    'min' => 3,
                    'max' => 1000
                    ])],
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'constraints' => [new Length([
                    'min' => 3,
                    'max' => 10000
                    ])],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
