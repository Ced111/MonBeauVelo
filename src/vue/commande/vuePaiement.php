<?php
if (!isset($panier, $detailsPanier)) {
    echo "<p class='erreur'>Informations sur le panier indisponibles. Veuillez réessayer.</p>";
    echo "<div class='retour'><a href='controleurFrontal.php?controleur=Commande&action=afficherPanier'>Retour au panier</a></div>";
    echo "<div class='retour'><a href='controleurFrontal.php?controleur=Produit&action=afficherListe'>Retour à la liste des produits</a></div>";
    return;
}
?>

<h1>Page de paiement</h1>

<div class="panier">
    <h2>Votre panier</h2>
    <?php foreach ($detailsPanier as $detail) : ?>
        <div style="border: 1px solid #ddd; margin-bottom: 10px; padding: 10px; border-radius: 5px;">
            <p>Nom du produit: <strong><?= htmlspecialchars($detail->getNomProduit()) ?></strong></p>
            <p>Quantité: <strong><?= htmlspecialchars($detail->getQuantite()) ?></strong></p>
            <p>Prix unitaire: <strong><?= htmlspecialchars($detail->getPrixUnitaire()) ?> €</strong></p>
            <p>Sous-total: <strong><?= htmlspecialchars($detail->getQuantite() * $detail->getPrixUnitaire()) ?> €</strong></p>
        </div>
    <?php endforeach; ?>
    <p class="total-panier">Total du panier: <strong><?= htmlspecialchars($panier->calculerTotal()) ?> €</strong></p>
</div>

<div class="paiement">
    <h2>Informations de paiement</h2>
    <form action="controleurFrontal.php?controleur=Commande&action=traiterPaiement" method="post">
        <!-- traiterPaiement n'existe pas -->
        <p>
            <label for="nom_porteur">Nom du porteur de la carte :</label>
            <input type="text" id="nom_porteur" name="nom_porteur" placeholder="Jean Dupont" required>
        </p>
        <p>
            <label for="num_carte">Numéro de la carte :</label>
            <input type="text" id="num_carte" name="num_carte" placeholder="1111 2222 3333 4444" required pattern="\d{4} \d{4} \d{4} \d{4}">
        </p>
        <p>
            <label for="date_expiration">Date d'expiration :</label>
            <input type="month" id="date_expiration" name="date_expiration" required>
        </p>
        <p>
            <label for="cvv">CVV :</label>
            <input type="text" id="cvv" name="cvv" required pattern="\d{3}" placeholder="123">
        </p>
        <p>
            <input type="submit" value="Valider le paiement">
        </p>
    </form>
    <form action="controleurFrontal.php?controleur=Commande&action=traiterPaiementFictif&idCommande=<?= $panier->getIdCommande() ?>" method="post">
        <button type="submit" style="background-color: #4CAF50;">
            Payer avec un sourire 😊
        </button>
    </form>
</div>

<div class="navigation">
    <a class='button back-to-list'  href="controleurFrontal.php?controleur=Commande&action=afficherPanier">Retour au panier</a>
</div>
