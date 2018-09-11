<?php

namespace App\Form;

use App\Entity\Bottles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BottlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('author')
            // ->add('date')
            ->add('title')
            ->add('content')
            // ->add('sent')
            // ->add('tags')
            // ->add('bottlesSent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bottles::class,
        ]);
    }
}
