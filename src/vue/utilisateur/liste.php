<?php
echo '<h1>Administrateur : Voici tous les comptes utilisateur</h1>';
echo '<ul>';
foreach ($utilisateurs as $utilisateur) {
    $idUtilisateurHTML = htmlspecialchars($utilisateur->getIdUtilisateur());
    $idUtilisateurURL = rawurlencode($utilisateur->getIdUtilisateur());
    $nomHTML = htmlspecialchars($utilisateur->getNom());
    $prenomHTML = htmlspecialchars($utilisateur->getPrenom());

    echo '<li style="margin-bottom: 10px;">';
    echo '<strong><a href="controleurFrontal.php?controleur=utilisateur&action=afficherDetail&idUtilisateur=' . $idUtilisateurURL . '" style="color: #007BFF;">' . $prenomHTML . ' ' . $nomHTML . '</a></strong>';
    echo ' (ID: ' . $idUtilisateurHTML . ')';
    echo '</li>';
}
echo '</ul>';
?>

