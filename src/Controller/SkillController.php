<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Form\SkillForm;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/skill')]
final class SkillController extends AbstractController
{
    #[Route(name: 'app_skill_index', methods: ['GET'])]
    public function index(SkillRepository $skillRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Récupérer uniquement les compétences de l'utilisateur connecté
        $user = $this->getUser();
        $skills = $skillRepository->findBy(['user' => $user]);

        return $this->render('skill/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route('/new', name: 'app_skill_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $skill = new Skill();
        $form = $this->createForm(SkillForm::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'Vous devez être connecté pour créer une compétence.');
                return $this->redirectToRoute('app_login');
            }
            
            // Définir l'utilisateur connecté comme propriétaire de la compétence
            $skill->setUser($user);
            
            $entityManager->persist($skill);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compétence a été créée avec succès !');
            return $this->redirectToRoute('app_skill_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('skill/new.html.twig', [
            'skill' => $skill,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_skill_show', methods: ['GET'])]
    public function show(Skill $skill): Response
    {
        return $this->render('skill/show.html.twig', [
            'skill' => $skill,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_skill_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Skill $skill, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier que l'utilisateur est le propriétaire de la compétence
        if ($this->getUser() !== $skill->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette compétence.');
            return $this->redirectToRoute('app_skill_index');
        }

        $form = $this->createForm(SkillForm::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La compétence a été mise à jour avec succès !');
            return $this->redirectToRoute('app_skill_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('skill/edit.html.twig', [
            'skill' => $skill,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_skill_delete', methods: ['POST'])]
    public function delete(Request $request, Skill $skill, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier que l'utilisateur est le propriétaire de la compétence
        if ($this->getUser() !== $skill->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette compétence.');
            return $this->redirectToRoute('app_skill_index');
        }

        if ($this->isCsrfTokenValid('delete'.$skill->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($skill);
            $entityManager->flush();
            $this->addFlash('success', 'La compétence a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Jeton de sécurité invalide.');
        }

        return $this->redirectToRoute('app_skill_index', [], Response::HTTP_SEE_OTHER);
    }
}
