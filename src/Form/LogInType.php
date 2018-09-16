<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LogInType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->setAction($this->generateUrl('users_login'))
            // ->setMethod('POST')
            
            // ->add('username')
            // ->add('country')
            ->add('email')
            ->add('password', PasswordType::class)
            // ->add('bio')
            // ->add('confirmed')
            // ->add('avatar')
            // ->add('bottlesSents')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
