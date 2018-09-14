<?php

namespace App\Form;

use App\Entity\Bottles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BottlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('author')
            // ->add('date')
            ->add('title')
            ->add('content')
            ->add('sent',ChoiceType::class)
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
