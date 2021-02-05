<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['placeholder' => 'Firstname'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a firstName',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['placeholder' => 'Lastname'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a lastName',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['placeholder' => 'Email'],
                'constraints' => [
                    new Email([
                        'message' => 'This is not the correct email format'
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a email'
                    ])
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Password'],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Repeat password'],
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
