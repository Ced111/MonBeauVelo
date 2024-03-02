<h1>Détails du produit</h1>

<div class="produit-detail">
    <?php
    use App\MonBeauVelo\Lib\ConnexionUtilisateur;

    if (isset($produit)) {
        /** @var string $idProduit */
        $idProduitHTML = htmlspecialchars($produit->getIdProduit());
        $nomHTML = htmlspecialchars($produit->getNom());
        $marqueHTML = htmlspecialchars($produit->getMarque());
        $descriptionHTML = htmlspecialchars($produit->getDescription());
        $prixHTML = htmlspecialchars($produit->getPrix());
        $imagePath = htmlspecialchars($produit->getImage() ?? '');

        echo "<div><img src='{$imagePath}' alt='Image du produit' style='max-width: 100px; max-height: 100px;'></div>";
        echo "<div>ID Produit: {$idProduitHTML}</div>";
        echo "<div>Nom: {$nomHTML}</div>";
        echo "<div>Marque: {$marqueHTML}</div>";
        echo "<div>Description: {$descriptionHTML}</div>";
        echo "<div>Prix: {$prixHTML} €</div>";
        echo "<div><a class='button add-to-cart' href='controleurFrontal.php?controleur=Commande&action=ajouterPanier&idProduit={$idProduitHTML}'>Ajouter au panier</a></div>";
    } else {
        echo "Détails du produit non disponibles.";
    }
    ?>
</div>
<a class='button back-to-list' href='controleurFrontal.php?controleur=Produit&action=afficherListe'>Retour à la liste des produits</a>

<?php if (ConnexionUtilisateur::estAdministrateur()): ?>
    <div>
        <a href="controleurFrontal.php?controleur=Produit&action=afficherFormulaireMiseAJour&idProduit=<?= $idProduitHTML ?>">Mettre à jour ce produit</a>
    </div>
    <div>
        <a href="controleurFrontal.php?controleur=Produit&action=supprimer&idProduit=<?= $idProduitHTML ?>">Supprimer ce produit</a>
    </div>
<?php endif; ?>
