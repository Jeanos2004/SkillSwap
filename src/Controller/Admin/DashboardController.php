<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Skill;
use App\Entity\Category;
use App\Entity\Exchange;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SkillSwap Admin')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        
        // Section Utilisateurs
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        
        // Section Compétences
        yield MenuItem::section('Compétences');
        yield MenuItem::linkToCrud('Compétences', 'fas fa-tags', Skill::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-folder', Category::class);

        

        yield MenuItem::section('Échanges');
        yield MenuItem::linkToCrud('Échanges', 'fas fa-exchange', Exchange::class); 
        
        // Section Administration
        yield MenuItem::section('Administration');
        yield MenuItem::linkToUrl('Retour au site', 'fas fa-arrow-left', '/');
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-sign-out');
    }
}