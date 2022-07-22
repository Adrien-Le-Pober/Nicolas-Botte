<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                    'min' => 2,
                    'max' => 100,
                    'minMessage' => "{{ limit }} caractères au minimum",
                    'maxMessage' => "{{ limit }} caractères au maximum"
                    ]),
                    new NotBlank(message: "Cette information est requise"),
                ],
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                    'min' => 2,
                    'max' => 100,
                    'minMessage' => "{{ limit }} caractères au minimum",
                    'maxMessage' => "{{ limit }} caractères au maximum"
                    ]),
                    new NotBlank(message: "Cette information est requise")
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [new Length([
                    'min' => 4,
                    'max' => 20,
                    'minMessage' => "{{ limit }} chiffres au minimum",
                    'maxMessage' => "{{ limit }} chiffres au maximum"
                    ])],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                    'min' => 3,
                    'max' => 180,
                    'minMessage' => "{{ limit }} caractères au minimum",
                    'maxMessage' => "{{ limit }} caractères au maximum"
                    ]),
                    new Email(message: "{{ value }} n'est pas une adresse valide.")
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                    'min' => 3,
                    'max' => 10000,
                    'minMessage' => "{{ limit }} caractères au minimum",
                    'maxMessage' => "{{ limit }} caractères au maximum"
                    ]),
                    new NotBlank(message: "Cette information est requise")
                ],
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
