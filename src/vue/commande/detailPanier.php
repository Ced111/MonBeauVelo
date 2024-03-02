<h1>Mon panier</h1>

<div class="panier-detail">
    <?php if (isset($commande) && !empty($details)) : ?>
        <p>ID Commande: <?= htmlspecialchars($commande->getIdCommande()) ?></p>
        <p>Date de la commande: <?= htmlspecialchars((new DateTime($commande->getDateCommande()))->format('d-m-Y')) ?></p>
        <p>Status de la commande: <?= htmlspecialchars($commande->getStatut()) ?></p>

        <?php $total = 0; ?>
        <?php foreach ($details as $detail) : ?>
            <div style="border: 1px solid #ddd; margin-bottom: 10px; padding: 10px; border-radius: 5px;">
                <p>ID Produit: <?= htmlspecialchars($detail->getIdProduit()) ?></p>
                <p>Nom: <?= htmlspecialchars($detail->getNomProduit()) ?></p>
                <p>Marque: <?= htmlspecialchars($detail->getMarque()) ?></p>
                <p>Description: <?= htmlspecialchars($detail->getDescription()) ?></p>
                <p>Catégorie: <?= htmlspecialchars($detail->getNomCategorie()) ?></p>
                <p>Prix unitaire: <?= htmlspecialchars($detail->getPrixUnitaire()) ?> €</p>
                <p>Quantité :
                <form method="post" action="controleurFrontal.php?controleur=commande&action=modifierQuantite">
                    <input type="number" name="quantite" value="<?= htmlspecialchars($detail->getQuantite()) ?>" min="0">
                    <input type="hidden" name="idProduit" value="<?= htmlspecialchars($detail->getIdProduit()) ?>">
                    <input type="hidden" name="idCommande" value="<?= htmlspecialchars($commande->getIdCommande()) ?>">
                    <button type="submit">Enregistrer</button>
                </form>
                </p>
                <p>Sous-total: <?= htmlspecialchars($detail->getQuantite() * $detail->getPrixUnitaire()) ?> €</p>
                <a class='button back-to-list' href="controleurFrontal.php?controleur=produit&action=afficherDetail&idProduit=<?= htmlspecialchars($detail->getIdProduit()) ?>">Voir la fiche produit</a><br>
                <a class='button back-to-list' href="controleurFrontal.php?controleur=Commande&action=supprimerProduit&idProduit=<?= htmlspecialchars($detail->getIdProduit()) ?>&idCommande=<?= htmlspecialchars($commande->getIdCommande()) ?>">Supprimer ce produit</a>
            </div>
            <?php $total += $detail->getQuantite() * $detail->getPrixUnitaire(); ?>
        <?php endforeach; ?>
        <p>Total: <?= htmlspecialchars($total) ?> €</p>
        <a class='button back-to-list' href="controleurFrontal.php?controleur=Commande&action=payer&idCommande=<?= htmlspecialchars($commande->getIdCommande()) ?>" style="background-color: #4CAF50; color: white; padding: 8px 15px">Payer</a>
    <?php else : ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</div>
<div style="margin-top: 20px;">
    <a class='button back-to-list' href="controleurFrontal.php?controleur=Produit&action=afficherListe">Retour à la liste des produits</a>
</div>