<?php

namespace App\Form;

use App\Entity\Exchange;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExchangeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        
        $builder
            ->add('offeredSkill', EntityType::class, [
                'class' => Skill::class,
                'label' => 'Votre compétence à échanger',
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('s')
                        ->where('s.user = :user')
                        ->setParameter('user', $user);
                },
                'attr' => ['class' => 'select2'],
                'placeholder' => 'Sélectionnez une de vos compétences',
                'required' => true,
            ])
            ->add('requestedSkill', EntityType::class, [
                'class' => Skill::class,
                'label' => 'Compétence que vous souhaitez',
                'choice_label' => function(Skill $skill) {
                    return $skill->getTitle() . ' (par ' . $skill->getUser()->getFullName() . ')';
                },
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('s')
                        ->where('s.user != :user')
                        ->setParameter('user', $user);
                },
                'attr' => ['class' => 'select2'],
                'placeholder' => 'Sélectionnez une compétence à apprendre',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'required' => true,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Expliquez pourquoi vous souhaitez faire cet échange...',
                    'class' => 'form-textarea mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'required' => true,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Expliquez pourquoi vous souhaitez faire cet échange...',
                    'class' => 'form-textarea mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exchange::class,
            'user' => null,
        ]);
    }
}