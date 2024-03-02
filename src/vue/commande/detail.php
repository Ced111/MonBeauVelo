<h1>Détails de la commande</h1>

<div class="commande-detail">
    <?php
    use App\MonBeauVelo\Lib\ConnexionUtilisateur;

    if (isset($commande)) {
        $idCommandeHTML = htmlspecialchars($commande->getIdCommande());
        $dateCommandeHTML = htmlspecialchars($commande->getDateCommande());
        $statutHTML = htmlspecialchars($commande->getStatut());
        ?>
        <ul>
            <li>ID Commande: <?= $idCommandeHTML ?></li>
            <li>Date de commande: <?= (new DateTime($dateCommandeHTML))->format('d-m-Y') ?></li>
            <li>Statut: <?= $statutHTML ?></li>
        </ul>
        <h2>Produits commandés:</h2>
        <ul>
            <?php
            $total = 0;
            foreach ($details as $detail) {
                $idProduit = htmlspecialchars($detail->getIdProduit());
                $nomProduit = htmlspecialchars($detail->getNomProduit());
                $marque = htmlspecialchars($detail->getMarque());
                $description = htmlspecialchars($detail->getDescription());
                $categorie = htmlspecialchars($detail->getNomCategorie());
                $prixUnitaire = htmlspecialchars($detail->getPrixUnitaire());
                $quantite = htmlspecialchars($detail->getQuantite());
                $sousTotal = $quantite * $prixUnitaire;
                $total += $sousTotal;
                ?>
                <li>
                    <p>ID Produit: <?= $idProduit ?><br>
                        Nom: <?= $nomProduit ?><br>
                        Marque: <?= $marque ?><br>
                        Description: <?= $description ?><br>
                        Catégorie: <?= $categorie ?><br>
                        Quantité: <?= $quantite ?><br>
                        Prix unitaire: <?= $prixUnitaire ?> €<br>
                        Sous-total: <?= $sousTotal ?> €</p>

                </li>
                <?php
            }
            ?>
        </ul>
        <p>Total: <?= $total ?> €</p>
        <?php
    } else {
        echo "Détails de la commande non disponibles.";
    }
    ?>
</div>
<?php if (ConnexionUtilisateur::estAdministrateur()): ?>
    <div>
        <a class='button back-to-list' href="controleurFrontal.php?controleur=Commande&action=afficherListe">Retour à la liste des commandes</a>
    </div>
    <div>
        <a class='button back-to-list' href="controleurFrontal.php?controleur=Commande&action=afficherFormulaireMiseAJour&idCommande=<?= $idCommandeHTML ?>">Mettre à jour cette commande</a>
    </div>
    <div>
        <a class='button back-to-list' href="controleurFrontal.php?controleur=Commande&action=supprimer&idCommande=<?= $idCommandeHTML ?>">Supprimer cette commande</a>
    </div>
<?php endif; ?>
