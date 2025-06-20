# SkillSwap - Plateforme d'échange de compétences

## 👤 Auteur
**Nom** : Jean Keloua OUAMOUNO  
**Niveau** : Licence 3 en Développement Logiciel  
**Matricule** : 664126142282  
**Date** : Juin 2025

[![Symfony](https://img.shields.io/badge/Symfony-7.x-2C8EBB?logo=symfony)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://www.php.net/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.0+-06B6D4?logo=tailwindcss)](https://tailwindcss.com/)

## 📋 Description

SkillSwap est une plateforme innovante qui permet aux utilisateurs d'échanger des compétences et des services sans transaction monétaire. Les utilisateurs peuvent proposer leurs compétences et en échanger avec d'autres membres de la communauté, tandis que les administrateurs supervisent et valident les échanges pour garantir la qualité des interactions.

## ✨ Fonctionnalités

### Utilisateurs
- Inscription et authentification sécurisée
- Profil utilisateur personnalisable
- Gestion des compétences proposées
- Système de messagerie entre utilisateurs
- Tableau de bord personnel
- Édition du profil et changement de mot de passe
- Suppression de compte sécurisée

### Compétences
- Création et gestion de fiches de compétences
- Catégorisation des compétences
- Recherche et filtrage des compétences
- Affichage détaillé avec informations du créateur

### Échanges
- Proposition d'échange de compétences
- Suivi des échanges en cours
- Validation des échanges par les administrateurs
- Système de notation et d'avis

### Administration
- Interface d'administration complète (EasyAdmin)
- Gestion des utilisateurs et des rôles
- Modération des compétences et échanges
- Tableaux de bord analytiques

## 👨‍💻 Administration

### Interface EasyAdmin

L'administration du site est gérée via EasyAdmin, offrant une interface complète pour la gestion du contenu :

- **Gestion des utilisateurs** :
  - Visualisation de la liste des utilisateurs
  - Modification des rôles et permissions
  - Désactivation de comptes

- **Gestion des compétences** :
  - Liste complète des compétences avec filtre par catégorie
  - Affichage du créateur de chaque compétence
  - Possibilité de modérer le contenu
  - Filtres de recherche avancés

- **Gestion des échanges** :
  - Suivi des échanges en cours
  - Validation des échanges
  - Gestion des litiges

- **Tableaux de bord** :
  - Statistiques d'utilisation
  - Activité récente
  - Graphiques d'évolution

### Sécurité d'accès
- Accès restreint aux utilisateurs avec le rôle `ROLE_ADMIN`
- Journalisation des actions administratives
- Protection CSRF sur tous les formulaires
- Vérification des permissions avant chaque action

## 🏗️ Modèle de données

Le projet utilise 5 entités principales pour gérer les données de l'application :

### 1. User (Utilisateur)
- **Rôle** : Gère les comptes utilisateurs et leurs informations personnelles
- **Attributs clés** :
  - `id` : Identifiant unique
  - `email` : Adresse email (unique)
  - `password` : Mot de passe (hashé)
  - `fullName` : Nom complet de l'utilisateur
  - `roles` : Rôles de l'utilisateur (ROLE_USER, ROLE_ADMIN)
  - `createdAt` : Date de création du compte
- **Relations** :
  - `skills` : Compétences proposées par l'utilisateur
  - `offeredExchanges` : Échanges où l'utilisateur est l'offreur
  - `receivedExchanges` : Échanges où l'utilisateur est le receveur

### 2. Skill (Compétence)
- **Rôle** : Représente une compétence ou un service proposé
- **Attributs clés** :
  - `id` : Identifiant unique
  - `title` : Titre de la compétence
  - `description` : Description détaillée
  - `createdAt` : Date de création
- **Relations** :
  - `user` : Utilisateur propriétaire de la compétence
  - `category` : Catégorie de la compétence
  - `offeredInExchanges` : Échanges où cette compétence est proposée
  - `requestedInExchanges` : Échanges où cette compétence est demandée

### 3. Category (Catégorie)
- **Rôle** : Classifie les compétences par catégories
- **Attributs clés** :
  - `id` : Identifiant unique
  - `name` : Nom de la catégorie
  - `description` : Description de la catégorie
- **Relations** :
  - `skills` : Compétences appartenant à cette catégorie

### 4. Exchange (Échange)
- **Rôle** : Gère les propositions d'échange entre utilisateurs
- **Attributs clés** :
  - `id` : Identifiant unique
  - `status` : Statut de l'échange (en attente/accepté/refusé)
  - `message` : Message accompagnant la demande
  - `createdAt` : Date de création de l'échange
- **Relations** :
  - `offerer` : Utilisateur qui propose l'échange
  - `receiver` : Utilisateur qui reçoit la proposition
  - `offeredSkill` : Compétence proposée en échange
  - `requestedSkill` : Compétence demandée
  - `reviews` : Avis liés à cet échange

### 5. Review (Avis)
- **Rôle** : Permet d'évaluer les échanges effectués
- **Attributs clés** :
  - `id` : Identifiant unique
  - `rating` : Note attribuée (1-5)
  - `comment` : Commentaire
  - `createdAt` : Date de création
- **Relations** :
  - `exchange` : Échange évalué
  - `author` : Auteur de l'avis

### Relations principales
- Un **User** peut avoir plusieurs **Skills** (OneToMany)
- Une **Skill** appartient à un **User** et une **Category** (ManyToOne)
- Un **Exchange** relie deux **Users** (offreur et receveur) et deux **Skills**
- Un **Exchange** peut avoir plusieurs **Reviews** (OneToMany)

## 🚀 Prérequis

- PHP 8.2 ou supérieur
- Composer 2.0 ou supérieur
- Node.js 16+ et npm/yarn
- Base de données (MySQL/PostgreSQL/SQLite)

## 🛠 Installation

1. **Cloner le dépôt** :
   ```bash
   git clone https://github.com/Jeanos2004/SkillSwap.git
   cd skillswap
   ```

2. **Installer les dépendances PHP** :
   ```bash
   composer install
   ```

3. **Installer les dépendances JavaScript** :
   ```bash
   npm install
   npm run build
   ```

4. **Configurer l'environnement** :
   - Copier le fichier `.env` vers `.env.local`
   - Configurer les variables d'environnement (base de données, mailer, etc.)

5. **Créer la base de données** :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. **Charger les données de test (optionnel)** :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

7. **Lancer le serveur** :
   ```bash
   symfony server:start
   ```

## 🔒 Sécurité

- Protection CSRF sur tous les formulaires
- Hachage sécurisé des mots de passe
- Validation des entrées utilisateur
- Gestion des rôles et permissions
- Protection contre les attaques XSS et injections SQL

## 🧪 Tests

Pour exécuter les tests :

```bash
php bin/phpunit
```

## 📚 Documentation technique

### Structure du projet

```
src/
├── Controller/
│   ├── Admin/         # Contrôleurs d'administration
│   └── ...            # Autres contrôleurs
├── Entity/            # Entités Doctrine
├── Form/              # Formulaires Symfony
├── Repository/        # Repository des entités
└── Security/          # Configuration de sécurité

templates/
├── admin/           # Templates d'administration
├── profile/         # Pages de profil utilisateur
├── skill/           # Gestion des compétences
└── exchange/        # Gestion des échanges
```

### Technologies utilisées

- **Backend** : Symfony 7, PHP 8.2+
- **Frontend** : Twig, Tailwind CSS, JavaScript
- **Base de données** : Doctrine ORM, Migrations
- **Sécurité** : Symfony Security, CSRF Protection
- **Outils** : Composer, Webpack Encore, PHPUnit

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment contribuer :

1. Forkez le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🙋‍♂️ Support

Pour toute question ou problème, veuillez ouvrir une issue sur le dépôt GitHub.

## 📧 Contact

Jean Keloua OUAMOUNO - [jeankelouaouamouno@gmail.com](mailto:jeankelouaouamouno@gmail.com) - 664126142282

Lien du projet : [https://github.com/Jeanos2004/SkillSwap](https://github.com/Jeanos2004/SkillSwap)