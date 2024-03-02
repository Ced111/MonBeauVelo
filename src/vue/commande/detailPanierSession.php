<h1>Votre Panier</h1>

<div class="panier-detail">
    <?php
    use App\MonBeauVelo\Lib\PanierSession;
    use App\MonBeauVelo\Modele\Repository\ProduitRepository;

    $sessionPanier = new PanierSession();
    $detailsPanier = $sessionPanier->getDetailsPanier();

    if (!empty($detailsPanier)) :
        $total = 0;
        $produitRepository = new ProduitRepository();
        foreach ($detailsPanier as $idProduit => $details) :
            $produitInfo = $produitRepository->recupererProduitAvecCategorie($idProduit);
            $sousTotal = $details['quantite'] * $details['prixUnitaire'];
            $total += $sousTotal;
            ?>
            <div style="border: 1px solid #ddd; margin-bottom: 10px; padding: 10px; border-radius: 5px;">
                <p>ID Produit: <?= htmlspecialchars($idProduit) ?></p>
                <p>Nom: <?= htmlspecialchars($produitInfo['nom']) ?></p>
                <p>Marque: <?= htmlspecialchars($produitInfo['marque']) ?></p>
                <p>Description: <?= htmlspecialchars($produitInfo['description']) ?></p>
                <p>Catégorie: <?= htmlspecialchars($produitInfo['nomCategorie']) ?></p>
                <p>Prix unitaire: <?= htmlspecialchars($details['prixUnitaire']) ?> €</p>
                <form method="post" action="controleurFrontal.php?controleur=Commande&action=modifierQuantiteSession">
                    <input type="hidden" name="idProduit" value="<?= htmlspecialchars($idProduit) ?>">
                    <input type="number" name="quantite" value="<?= htmlspecialchars($details['quantite']) ?>" min="1">
                    <button type="submit">Enregistrer</button>
                </form>
                <p>Sous-total: <?= htmlspecialchars($sousTotal) ?> €</p>
                <a  class='button back-to-list'href="controleurFrontal.php?controleur=produit&action=afficherDetail&idProduit=<?= htmlspecialchars($idProduit) ?>">Voir la fiche produit</a><br>
                <a  class='button back-to-list'href="controleurFrontal.php?controleur=Commande&action=supprimerProduitSession&idProduit=<?= htmlspecialchars($idProduit) ?>">Supprimer</a>
            </div>
        <?php endforeach; ?>
        <p>Total: <?= htmlspecialchars($total) ?> €</p>
    <?php else : ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</div>

<div style="margin-top: 20px;">
    <a class='button back-to-list' href="controleurFrontal.php?controleur=Produit&action=afficherListe">Continuer vos achats</a>
    <a class='button back-to-list' href="controleurFrontal.php?controleur=Utilisateur&action=afficherFormulaireCreation">Créer un compte pour sauver votre commande !</a>
</div>
