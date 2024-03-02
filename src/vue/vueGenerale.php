<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php use App\MonBeauVelo\Lib\ConnexionUtilisateur;
        use App\MonBeauVelo\Lib\PanierSession;

        echo $pagetitle; ?></title>
    <link rel="stylesheet" href="../ressources/css/style3.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <?php if (ConnexionUtilisateur::estAdministrateur()): ?>
                <li>
                    <a href="controleurFrontal.php?action=afficherListe&controleur=utilisateur" class="button">Gestion des utilisateurs</a>
                </li>
                <li>
                    <a href="controleurFrontal.php?action=afficherListe&controleur=commande" class="button">Voir toutes les commandes</a>
                </li>
            <?php endif; ?>
            <li>
                <a href="controleurFrontal.php?action=afficherListe&controleur=produit" class="button">Voir les produits</a>
            </li>
            <?php if (ConnexionUtilisateur::estConnecte()): ?>
                <li>
                    <a href="controleurFrontal.php?action=afficherListeCommandesUtilisateur&controleur=commande" class="button">Voir mes commandes</a>
                </li>
                <li>
                    <a href="controleurFrontal.php?action=afficherPanier&controleur=commande" class="button">Voir mon panier</a>
                </li>
            <?php endif; ?>
            <?php if (!ConnexionUtilisateur::estConnecte() && PanierSession::aDesArticles()): ?>
                <li>
                    <a href="controleurFrontal.php?action=afficherPanierSession&controleur=commande" class="button">Voir mon panier</a>
                </li>
            <?php endif; ?>
            <?php if (ConnexionUtilisateur::estAdministrateur()): ?>
                <li>
                    <a href="controleurFrontal.php?action=formulairePreference" class="button">
                        <img src="../ressources/img/heart.png" alt="Préférences" title="Aller aux préférences" />
                    </a>
                </li>
            <?php endif; ?>
            <?php if (ConnexionUtilisateur::estConnecte()): ?>
                <li>
                    <a href="controleurFrontal.php?action=afficherDetail&controleur=utilisateur&idUtilisateur=<?php echo \App\MonBeauVelo\Lib\ConnexionUtilisateur::getIdUtilisateurConnecte(); ?>" class="button">
                        <img src="../ressources/img/user.png" alt="Voir profil" title="Voir profil" />
                    </a>
                </li>
                <li>
                    <a href="controleurFrontal.php?action=deconnecter&controleur=utilisateur" class="button">
                        <img src="../ressources/img/logout.png" alt="Se déconnecter" title="Se déconnecter" />
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="controleurFrontal.php?action=afficherFormulaireCreation&controleur=utilisateur">
                        <img src="../ressources/img/addUser.png" alt="S'inscrire" title="S'inscrire" />
                    </a>
                </li>
                <li>
                    <a href="controleurFrontal.php?action=afficherFormulaireConnexion&controleur=utilisateur">
                        <img src="../ressources/img/enter.png" alt="Se connecter" title="Se connecter" />
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</header>
<div>
    <?php
    /** @var string[][] $messagesFlash */
    foreach ($messagesFlash as $type => $messagesFlashPourUnType) {
        // $type est l'une des valeurs suivantes : "success", "info", "warning", "danger"
        // $messagesFlashPourUnType est la liste des messages flash d'un type
        foreach ($messagesFlashPourUnType as $messageFlash) {
            echo <<<HTML
            <div class="alert alert-$type">
               $messageFlash
            </div>
            HTML;
        }
    }
    ?>
</div>
<main>
    <?php
    require __DIR__ . "/{$cheminVueBody}";
    ?>
</main>
<footer>
    <p>&copy; 2023 - Mon Beau Vélo</p>
</footer>
</body>
</html>
