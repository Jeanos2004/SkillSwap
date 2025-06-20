<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Exchange;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review')]
#[IsGranted('ROLE_USER')]
final class ReviewController extends AbstractController
{
    #[Route(name: 'app_review_index', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepository): Response
    {
        // Récupérer uniquement les avis liés à l'utilisateur connecté
        $user = $this->getUser();
        $reviews = $reviewRepository->findBy(['reviewer' => $user]);

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/exchange/{id}/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Exchange $exchange): Response
    {
        // Vérifier que l'utilisateur est autorisé à laisser un avis sur cet échange
        if ($exchange->getOfferer() !== $this->getUser() && $exchange->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à laisser un avis sur cet échange.');
        }
        
        // Vérifier que l'échange est terminé
        if ($exchange->getStatus() !== 'completed') {
            $this->addFlash('error', 'Vous ne pouvez laisser un avis que sur un échange terminé.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }

        $review = new Review();
        $review->setExchange($exchange);
        $review->setReviewer($this->getUser());
        
        // Déterminer automatiquement le destinataire de l'avis
        if ($exchange->getOfferer() === $this->getUser()) {
            $review->setReviewee($exchange->getReceiver());
        } else {
            $review->setReviewee($exchange->getOfferer());
        }
        
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été enregistré avec succès.');
            return $this->redirectToRoute('app_exchange_show', ['id' => $exchange->getId()]);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'exchange' => $exchange,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        // Vérifier que l'utilisateur est autorisé à voir cet avis
        if ($review->getReviewer() !== $this->getUser() && $review->getReviewee() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir cet avis.');
        }
        
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est l'auteur de l'avis
        if ($review->getReviewer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet avis.');
        }
        
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été modifié avec succès.');
            return $this->redirectToRoute('app_review_index');
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est l'auteur de l'avis
        if ($review->getReviewer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet avis.');
        }
        
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
            $this->addFlash('success', 'Votre avis a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_review_index');
    }
}
