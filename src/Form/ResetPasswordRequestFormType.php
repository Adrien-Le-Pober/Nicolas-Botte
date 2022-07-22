<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 180,
                        'minMessage' => "{{ limit }} caractères au minimum",
                        'maxMessage' => "{{ limit }} caractères au maximum"
                        ]),
                    new NotBlank([
                        'message' => 'Cette information est requise',
                    ]),
                    new Email(message: "{{ value }} n'est pas une adresse valide.")
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
