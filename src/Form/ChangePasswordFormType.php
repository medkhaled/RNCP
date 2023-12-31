<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\PasswordValidator;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangePasswordFormType extends AbstractType
{
    private $passwordValidator;

    public function __construct(PasswordValidator $passwordValidator)
    {
        $this->passwordValidator = $passwordValidator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'first_options' => [
                'label' => 'New password',
                'constraints' => [
                    new Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            $errors = $this->passwordValidator->validatePassword($value);
        
                            foreach ($errors as $error) {
                                $context->buildViolation($error)->addViolation();
                            }
                        },
                    ]),
                ],
            ],
            'second_options' => [
                'label' => 'Repeat Password',
            ],
            'invalid_message' => 'The password fields must match.',
            // Instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
