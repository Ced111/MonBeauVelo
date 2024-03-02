<h1>Détails de l'utilisateur</h1>

<div class="utilisateur-detail">
    <?php use App\MonBeauVelo\Lib\ConnexionUtilisateur;

    if (isset($utilisateur)): ?>
        <?php
        $idUtilisateurHTML = htmlspecialchars($utilisateur->getIdUtilisateur());
        $nomHTML = htmlspecialchars($utilisateur->getNom());
        $prenomHTML = htmlspecialchars($utilisateur->getPrenom());
        ?>
        <div>ID Utilisateur: <?= $idUtilisateurHTML ?></div>
        <div>Nom: <?= $nomHTML ?></div>
        <div>Prénom: <?= $prenomHTML ?></div>
    <?php else: ?>
        <p>Détails de l'utilisateur non disponibles.</p>
    <?php endif; ?>
</div>
<?php if (ConnexionUtilisateur::estAdministrateur() || ConnexionUtilisateur::estUtilisateur($idUtilisateurHTML)): ?>
    <div>
        <a href="controleurFrontal.php?controleur=utilisateur&action=afficherFormulaireMiseAJour&idUtilisateur=<?= $idUtilisateurHTML ?>">Mettre à jour cet utilisateur</a>
    </div>
    <div>
        <a href="controleurFrontal.php?controleur=utilisateur&action=supprimer&idUtilisateur=<?= $idUtilisateurHTML ?>">Supprimer cet utilisateur</a>
    </div>
<?php endif; ?>
<div>
    <a href="controleurFrontal.php?controleur=utilisateur&action=afficherListe">Retour à la liste des utilisateurs</a>
</div>
