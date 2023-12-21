<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Service\PasswordValidator;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserType extends AbstractType
{
    private $passwordValidator;

    public function __construct(PasswordValidator $passwordValidator)
    {
        $this->passwordValidator = $passwordValidator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [
            'MEMBRE' => 'ROLE_USER',
	        'PRESTATAIRE' => 'ROLE_EMPLOYEE',
            'ADMINISTRATEUR' => 'ROLE_ADMIN',
        ];
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => $roles,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control'
                ],
                'constraints' => [
                   
                    new Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            $errors = $this->passwordValidator->validatePassword($value);
    
                            foreach ($errors as $error) {
                                $context->buildViolation($error)->addViolation();
                            }
                        },
                    ])
                ],
                'label' => 'Mot de passe',
            ])
            ->add('lastname')
            ->add('firstname')
            ->add('address')
            ->add('zipcode')
            ->add('city')
            ->add('matricule')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
