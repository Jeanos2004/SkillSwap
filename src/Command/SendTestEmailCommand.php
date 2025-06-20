<?php

   namespace App\Command;

   use Symfony\Component\Console\Attribute\AsCommand;
   use Symfony\Component\Console\Command\Command;
   use Symfony\Component\Console\Input\InputInterface;
   use Symfony\Component\Console\Output\OutputInterface;
   use Symfony\Component\Mailer\MailerInterface;
   use Symfony\Component\Mime\Email;

   #[AsCommand(name: 'app:send-test-email')]
   class SendTestEmailCommand extends Command
   {
       private $mailer;

       public function __construct(MailerInterface $mailer)
       {
           $this->mailer = $mailer;
           parent::__construct();
       }

       protected function execute(InputInterface $input, OutputInterface $output): int
       {
           $email = (new Email())
               ->from('jeankelouaouamouno71@gmail.com')
               ->to('jeanalena14@gmail.com') // Remplacez par votre email pour tester
               ->subject('Test Email from SkillSwap')
               ->text('Ceci est un email de test envoyé depuis Symfony via Gmail !');

           $this->mailer->send($email);

           $output->writeln('Email de test envoyé avec succès !');
           return Command::SUCCESS;
       }
   }