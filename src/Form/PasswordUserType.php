<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => "Votre mot de passe actuel",
                'attr' => [
                    'placeholder' => 'Indiquez votre mot de passe actuel'
                ],
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 30
                    ])
                ],
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Nouveau mot de passe'
                    ],
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez le nouveau mot de passe'
                    ]
                ],
                'mapped' => false,
                'label' => 'Confirmer votre mot de passe'
            ])
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Mettre à jour mon mot de passe',
                    'attr' =>
                    ['class' => 'btn-success'
                    ]
                ]
            )
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $form->getConfig()->getOptions()['data'];
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];

                $isValide = $passwordHasher->isPasswordValid(
                    $user,
                    $form->get('actualPassword')->getData()
                );

                if (!$isValide) {
                    $form->get('actualPassword')->addError(new FormError("Votre mot de passe actuel n'est pas conforme"));
                };

            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
