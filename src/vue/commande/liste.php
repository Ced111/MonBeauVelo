<?php
echo '<h1>Administrateur : Voici toutes les commandes</h1>';
echo '<ul>';
foreach ($commandes as $commande) {
    $idCommandeHTML = htmlspecialchars($commande->getIdCommande());
    $idCommandeURL = rawurlencode($commande->getIdCommande());
    $dateCommandeHTML = htmlspecialchars((new DateTime($commande->getDateCommande()))->format('d-m-Y'));
    $statutHTML = htmlspecialchars($commande->getStatut());

    echo '<li>';
    echo '<strong><a href="controleurFrontal.php?controleur=commande&action=afficherDetail&idCommande=' . $idCommandeURL . '" style="color: #007BFF;">Commande n°' . $idCommandeHTML . '</a></strong>';
    echo ' passée le ' . $dateCommandeHTML . '. ';
    echo 'Statut : <span style="color: #D32F2F; font-weight: bold;">' . $statutHTML . '</span>';
    echo '</li>';
}
echo '</ul>';
?>
