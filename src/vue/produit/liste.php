<?php
use App\MonBeauVelo\Lib\ConnexionUtilisateur;

echo '<h1>Liste de tous les produits</h1>';
echo '<ul>';
foreach ($produits as $produit) {
    $idProduitHTML = htmlspecialchars($produit->getIdProduit());
    $idProduitURL = rawurlencode($produit->getIdProduit());
    $nomHTML = htmlspecialchars($produit->getNom());
    $marqueHTML = htmlspecialchars($produit->getMarque());
    $prixHTML = htmlspecialchars($produit->getPrix());
    $imagePath = htmlspecialchars($produit->getImage() ?? '');

    echo '<li>';
    echo '<img src="' . $imagePath . '" alt="Image du produit" style="max-width: 100px; max-height: 100px;">';
    echo '<strong><a href="controleurFrontal.php?controleur=produit&action=afficherDetail&idProduit=' . $idProduitURL . '" style="color: #008B8B;">' . $nomHTML . '</a></strong>';
    echo ' de la marque <strong style="color: #333;">' . $marqueHTML . '</strong>. ';
    echo '<span style="color: #D32F2F; font-weight: bold;">' . $prixHTML . ' €</span>';
    echo '</li>';
}
echo '</ul>';

if (ConnexionUtilisateur::estAdministrateur()) {
    echo '<div><a class="button back-to-list" href="controleurFrontal.php?action=afficherFormulaireCreation&controleur=produit">Ajouter un produit</a></div>';
}
?>

