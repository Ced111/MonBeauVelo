# MonBeauVelo - Projet PHP

## Informations Générales
- **Nom du Projet**: MonBeauVelo
- **Classe**: Q4
- **Université**: IUT Montpellier-Sète
- **Année Universitaire**: 2023-2024
- **Étudiants**:
    - Raphael Prevost
    - Leretour Cedric

## Accès et lancement du Site Web
Le site est accessible via le lien suivant : [MonBeauVelo](http://webinfo.iutmontp.univ-montp2.fr/~leretourc/projetphp/web/controleurFrontal.php?action=afficherListe&controleur=produit).

Un script de création de table est disponible dans le fichier CreationBD.sql à la racine du projet afin de tester les fonctionnalités du site.
Les utilisateurs ajoutés ainsi ne bénéficieront pas d'un mot de passe sécurisé avec un poivre et un sel.
Il faudra pour cela créer un compte utilisateur via le site.

## Fonctionnalités Administratives
### Nomination des Administrateurs
Les administrateurs sont nommés exclusivement par le super administrateur, seule personne ayant accès à phpMyAdmin. Pour nommer un administrateur, changez la valeur de `0` à `1` dans la colonne `estAdmin` de la table `p_utilisateur`.

### Droits des Administrateurs
- **Utilisateurs** : Consulter, modifier et supprimer les utilisateurs (à l'exception de leurs mots de passe).
- **Commandes** : Consulter et supprimer toutes les commandes, ainsi que la modification du statut.
- **Produits** : Consulter, ajouter, modifier et supprimer tous les produits.

## Parcours Utilisateur
- Consulter et ajouter des produits au panier.
- Consulter le panier.
- Créer un compte utilisateur.
- Valider l'adresse email.
- Se connecter.
- Continuer ou valider les achats dans le panier.
- Payer (de manière fictive).
- Consulter l'historique des commandes personnelles.
- Se déconnecter.


## Section Technique

### Environnement de Développement
- **Serveur local**: MAMP
    - PHP
    - Apache
    - MySQL
- **Base de données**: MySQL, gérée via phpMyAdmin

### Structure du Projet
- **Architecture MVC** (Modèle-Vue-Contrôleur) pour une organisation claire et une maintenance aisée du code.
- **Session PHP** pour la gestion des paniers d'utilisateurs et le suivi des sessions utilisateurs.
- **Sécurité** :
    - Hashage des mots de passe. (poivre et sel)
    - Protection contre les injections SQL et les attaques XSS.

### Déploiement
- Le projet est actuellement hébergé sur le serveur de l'université, accessible via un sous-domaine IUT.

### Collaboration et Versioning
- **Git** pour le contrôle de version et la gestion du code source.
- **GitLab** pour le partage du code, la collaboration et le suivi des problèmes (issues) et des fonctionnalités (features).
