<?php

   namespace App\Command;

   use App\Entity\User;
   use Doctrine\ORM\EntityManagerInterface;
   use Symfony\Component\Console\Attribute\AsCommand;
   use Symfony\Component\Console\Command\Command;
   use Symfony\Component\Console\Input\InputInterface;
   use Symfony\Component\Console\Output\OutputInterface;
   use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

   #[AsCommand(name: 'app:create-admin')]
   class CreateAdminCommand extends Command
   {
       private $entityManager;
       private $passwordHasher;

       public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
       {
           $this->entityManager = $entityManager;
           $this->passwordHasher = $passwordHasher;
           parent::__construct();
       }

       protected function execute(InputInterface $input, OutputInterface $output): int
       {
           $user = new User();
           $user->setEmail('admin@skillswap.com');
           $user->setFullName('Administrateur');
           $user->setRoles(['ROLE_ADMIN']);
           $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin123');
           $user->setPassword($hashedPassword);

           $this->entityManager->persist($user);
           $this->entityManager->flush();

           $output->writeln('Administrateur créé avec succès !');
           return Command::SUCCESS;
       }
   }