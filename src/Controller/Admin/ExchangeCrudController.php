<?php

namespace App\Controller\Admin;

use App\Entity\Exchange;
use App\Entity\User;
use App\Entity\Skill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ExchangeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exchange::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Échange')
            ->setEntityLabelInPlural('Échanges')
            ->setSearchFields(['status', 'offerer.email', 'receiver.email', 'offeredSkill.name', 'requestedSkill.name'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            
            // Statut
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'En attente' => 'pending',
                    'Accepté' => 'accepted',
                    'Refusé' => 'rejected',
                    'Terminé' => 'completed',
                    'Annulé' => 'cancelled'
                ])
                ->renderAsBadges([
                    'pending' => 'warning',
                    'accepted' => 'success',
                    'rejected' => 'danger',
                    'completed' => 'info',
                    'cancelled' => 'secondary'
                ]),
            
            // Relations
            AssociationField::new('offerer', 'Offreur')
                ->setCrudController(UserCrudController::class)
                ->formatValue(function ($value, $entity) {
                    return $entity->getOfferer() ? $entity->getOfferer()->getEmail() : '';
                }),
                
            AssociationField::new('receiver', 'Bénéficiaire')
                ->setCrudController(UserCrudController::class)
                ->formatValue(function ($value, $entity) {
                    return $entity->getReceiver() ? $entity->getReceiver()->getEmail() : '';
                }),
                
            AssociationField::new('offeredSkill', 'Compétence proposée')
                ->setCrudController(SkillCrudController::class)
                ->formatValue(function ($value, $entity) {
                    return $entity->getOfferedSkill() ? $entity->getOfferedSkill()->getTitle() : '';
                }),
                
            AssociationField::new('requestedSkill', 'Compétence demandée')
                ->setCrudController(SkillCrudController::class)
                ->formatValue(function ($value, $entity) {
                    return $entity->getRequestedSkill() ? $entity->getRequestedSkill()->getTitle() : '';
                }),
            
            // Date de création
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->hideOnForm(),
            
            // Collection des avis (en lecture seule)
            AssociationField::new('reviews', 'Avis')
                ->setTemplatePath('admin/fields/reviews.html.twig')
                ->hideOnForm(),
        ];
    }



    
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel(false);
            });
    }
}