<?php

namespace App\Form;

use App\Entity\Exchange;
use App\Entity\Skill;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExchangeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('createdAt')
            ->add('offerer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('offeredSkill', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'id',
            ])
            ->add('requestedSkill', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exchange::class,
        ]);
    }
}
