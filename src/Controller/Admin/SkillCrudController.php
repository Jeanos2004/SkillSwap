<?php

namespace App\Controller\Admin;

use App\Entity\Skill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class SkillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Skill::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description')->hideOnIndex(),
            AssociationField::new('category')
                ->setFormTypeOption('choice_label', 'name') // Spécifie la propriété à afficher
                ->setLabel('Catégorie'),
            AssociationField::new('user', 'Créé par')
                ->setFormTypeOption('choice_label', 'email') // ou 'fullName'
                ->setTemplatePath('admin/fields/user.html.twig'),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->hideOnForm(),
        ];

        if ($pageName === Crud::PAGE_EDIT) {
            $fields[] = DateTimeField::new('updatedAt', 'Mis à jour le')
                ->setFormTypeOption('disabled', true);
        }

        return $fields;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Compétences')
            ->setPageTitle('new', 'Créer une compétence')
            ->setPageTitle('edit', 'Modifier la compétence')
            ->setSearchFields(['title', 'description', 'user.email', 'user.firstName', 'user.lastName'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }
}
