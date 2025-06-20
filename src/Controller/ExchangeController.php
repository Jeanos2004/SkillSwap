<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Form\ExchangeForm;
use App\Repository\ExchangeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Review;
use App\Form\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/exchange')]
#[IsGranted('ROLE_USER')]
final class ExchangeController extends AbstractController
{
    #[Route(name: 'app_exchange_index', methods: ['GET'])]
    public function index(ExchangeRepository $exchangeRepository): Response
    {
        $user = $this->getUser();
        
        // Si l'utilisateur est un admin, afficher tous les échanges
        if ($this->isGranted('ROLE_ADMIN')) {
            $exchanges = $exchangeRepository->findAll();
        } else {
            // Sinon, afficher uniquement les échanges où l'utilisateur est impliqué
            $exchanges = $exchangeRepository->findByUser($user);
        }
        
        return $this->render('exchange/index.html.twig', [
            'exchanges' => $exchanges,
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/new', name: 'app_exchange_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exchange = new Exchange();
        $exchange->setOfferer($this->getUser());
        $exchange->setStatus('pending');
        $exchange->setCreatedAt(new \DateTime());
        
        $form = $this->createForm(ExchangeForm::class, $exchange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exchange);
            $entityManager->flush();

            $this->addFlash('success', 'Votre proposition d\'échange a été créée avec succès.');
            return $this->redirectToRoute('app_exchange_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exchange/new.html.twig', [
            'exchange' => $exchange,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exchange_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        // Vérification des autorisations
        if ($exchange->getOfferer() !== $this->getUser() && 
            $exchange->getReceiver() !== $this->getUser() && 
            !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cet échange.');
        }

        // Création du formulaire d'avis
        $review = new Review();
        $reviewForm = $this->createForm(ReviewType::class, $review);
        $reviewForm->handleRequest($request);
        
        // Traitement du formulaire si soumis
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            // Vérifier que l'utilisateur peut laisser un avis
            if ($exchange->getStatus() !== 'completed') {
                $this->addFlash('error', 'Vous ne pouvez pas laisser d\'avis sur un échange non terminé.');
                return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
            }
            
            // Vérifier que l'utilisateur n'a pas déjà laissé un avis
            $existingReview = $entityManager->getRepository(Review::class)->findOneBy([
                'exchange' => $exchange,
                'reviewer' => $this->getUser()
            ]);
            
            if ($existingReview) {
                $this->addFlash('error', 'Vous avez déjà laissé un avis pour cet échange.');
                return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
            }
            
            // Enregistrer l'avis
            $review->setExchange($exchange);
            $review->setReviewer($this->getUser());
            $review->setCreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($review);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre avis a été enregistré avec succès !');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }

        return $this->render('exchange/show.html.twig', [
            'exchange' => $exchange,
            'reviewForm' => $reviewForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exchange_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est autorisé à modifier cet échange
        if (!$this->isGranted('ROLE_ADMIN') && $exchange->getOfferer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet échange.');
        }
        
        // Vérifier que l'échange est toujours en attente
        if ($exchange->getStatus() !== 'pending' && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier un échange qui a déjà été accepté ou refusé.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        $form = $this->createForm(ExchangeForm::class, $exchange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'échange a été modifié avec succès.');
            return $this->redirectToRoute('app_exchange_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exchange/edit.html.twig', [
            'exchange' => $exchange,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_exchange_delete', methods: ['POST'])]
    public function delete(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est autorisé à supprimer cet échange
        if (!$this->isGranted('ROLE_ADMIN') && $exchange->getOfferer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet échange.');
        }
        
        // Vérifier que l'échange est toujours en attente
        if ($exchange->getStatus() !== 'pending' && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un échange qui a déjà été accepté ou refusé.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        if ($this->isCsrfTokenValid('delete'.$exchange->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($exchange);
            $entityManager->flush();
            $this->addFlash('success', 'L\'échange a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_exchange_index', [], Response::HTTP_SEE_OTHER);
    }
    #[isGranted('ROLE_USER')]
    #[Route('/{id}/review', name: 'app_exchange_review', methods: ['POST'])]
    public function addReview(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour laisser un avis.');
        }
        
        // Vérifier que l'utilisateur est soit l'offreur soit le receveur
        if ($exchange->getOfferer() !== $user && $exchange->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas laisser d\'avis sur cet échange.');
        }
        
        // Vérifier que l'échange est bien terminé
        if ($exchange->getStatus() !== 'completed') {
            $this->addFlash('error', 'Vous ne pouvez pas laisser d\'avis sur un échange non terminé.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        // Vérifier que l'utilisateur n'a pas déjà laissé un avis
        $existingReview = $entityManager->getRepository(Review::class)->findOneBy([
            'exchange' => $exchange,
            'reviewer' => $user
        ]);
        
        if ($existingReview) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour cet échange.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setExchange($exchange);
            $review->setReviewer($user);
            $review->setCreatedAt(new \DateTime());
            
            $entityManager->persist($review);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre avis a été enregistré avec succès !');
        } else {
            // Afficher les erreurs de validation
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
    }
    #[Route('/{id}/accept', name: 'app_exchange_accept', methods: ['POST'])]
    public function accept(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est autorisé à accepter cet échange
        if (!$this->isGranted('ROLE_ADMIN') && $exchange->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accepter cet échange.');
        }
        
        // Vérifier que l'échange est toujours en attente
        if ($exchange->getStatus() !== 'pending') {
            $this->addFlash('error', 'Cet échange a déjà été traité.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        if ($this->isCsrfTokenValid('accept'.$exchange->getId(), $request->getPayload()->getString('_token'))) {
            $exchange->setStatus('accepted');
            $entityManager->flush();
            $this->addFlash('success', 'L\'échange a été accepté avec succès.');
        }

        return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
    }
    
    #[Route('/{id}/reject', name: 'app_exchange_reject', methods: ['POST'])]
    public function reject(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est autorisé à refuser cet échange
        if (!$this->isGranted('ROLE_ADMIN') && $exchange->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à refuser cet échange.');
        }
        
        // Vérifier que l'échange est toujours en attente
        if ($exchange->getStatus() !== 'pending') {
            $this->addFlash('error', 'Cet échange a déjà été traité.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        if ($this->isCsrfTokenValid('reject'.$exchange->getId(), $request->getPayload()->getString('_token'))) {
            $exchange->setStatus('rejected');
            $entityManager->flush();
            $this->addFlash('success', 'L\'échange a été refusé.');
        }

        return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
    }
    
    #[Route('/{id}/complete', name: 'app_exchange_complete', methods: ['POST'])]
    public function complete(Request $request, Exchange $exchange, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est impliqué dans cet échange ou est un admin
        if (!$this->isGranted('ROLE_ADMIN') && 
            $exchange->getOfferer() !== $this->getUser() && 
            $exchange->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à marquer cet échange comme terminé.');
        }
        
        // Vérifier que l'échange est accepté
        if ($exchange->getStatus() !== 'accepted') {
            $this->addFlash('error', 'Seul un échange accepté peut être marqué comme terminé.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }
        
        if ($this->isCsrfTokenValid('complete'.$exchange->getId(), $request->getPayload()->getString('_token'))) {
            $exchange->setStatus('completed');
            $entityManager->flush();
            $this->addFlash('success', 'L\'échange a été marqué comme terminé. Vous pouvez maintenant laisser un avis.');
        }

        return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
    }
}
