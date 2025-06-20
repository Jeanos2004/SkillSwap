<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['POST'])]
    public function sendContact(Request $request, LoggerInterface $logger): JsonResponse
    {
        $logger->info('Début de la méthode sendContact');
        
        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true) ?? $request->request->all();
        
        // Valider les champs requis
        if (empty($data['email']) || empty($data['message'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'L\'email et le message sont obligatoires'
            ], 400);
        }
        
        try {
            // Configuration de Mailtrap
            $dsn = 'smtp://38e8c03b38a98c:522303bf4e3142@sandbox.smtp.mailtrap.io:2525';
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);
            
            // Préparer l'email
            $email = (new Email())
                ->from('noreply@skillswap.fr')
                ->to('contact@skillswap.fr') // Votre adresse de réception
                ->replyTo($data['email'])
                ->subject($data['subject'] ?? 'Nouveau message de contact')
                ->html($this->renderView('emails/contact.html.twig', [
                    'data' => [
                        'first_name' => $data['first_name'] ?? '',
                        'last_name' => $data['last_name'] ?? '',
                        'email' => $data['email'],
                        'subject' => $data['subject'] ?? 'Sans objet',
                        'message' => $data['message']
                    ]
                ]));
            
            // Envoyer l'email
            $mailer->send($email);
            
            $logger->info('Email de contact envoyé avec succès');
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Votre message a été envoyé avec succès !',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = sprintf(
                "Erreur lors de l'envoi de l'email: %s\nTrace: %s",
                $e->getMessage(),
                $e->getTraceAsString()
            );
            
            $logger->error($errorMessage);
            
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'envoi du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}