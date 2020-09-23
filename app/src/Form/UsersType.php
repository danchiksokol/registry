<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['required' => false])
            ->add('middlename', TextType::class, ['required' => false])
            ->add('lastname', TextType::class, ['required' => false])
            ->add('job', TextType::class, ['required' => false])
            ->add('position', TextType::class, ['required' => false])
            ->add('phone', TextType::class, ['required' => false])
            ->add('email', EmailType::class, ['required' => false])
            ->add('city', TextType::class, ['required' => false])
            ->add('country', TextType::class, ['required' => false]);
        if ($options['show']) {
            $builder->add('active', CheckboxType::class, ['required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Users::class,
                'show' => false,
            ]
        );
    }
}
