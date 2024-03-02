<div class="recu-paiement">
    <h1>Reçu de Paiement</h1>
    <?php if (isset($commande)) : ?>
        <p>Commande ID: <?= htmlspecialchars($commande->getIdCommande()) ?></p>
        <p>Date de la commande: <?= htmlspecialchars((new DateTime($commande->getDateCommande()))->format('d/m/Y H:i')) ?></p>
        <p>Status de la commande: <?= htmlspecialchars($commande->getStatut()) ?></p>
        <p>Total payé: <?= htmlspecialchars($commande->calculerTotal()) ?> €</p>

        <h2>Produits achetés</h2>
        <ul>
            <?php foreach ($commande->getDetailsCommande() as $detail) : ?>
                <li>
                    <p>Produit: <?= htmlspecialchars($detail->getNomProduit()) ?></p>
                    <p>Quantité: <?= htmlspecialchars($detail->getQuantite()) ?></p>
                    <p>Prix unitaire: <?= htmlspecialchars($detail->getPrixUnitaire()) ?> €</p>
                    <p>Sous-total: <?= htmlspecialchars($detail->getQuantite() * $detail->getPrixUnitaire()) ?> €</p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Erreur : Aucune commande à afficher.</p>
    <?php endif; ?>
</div>

<a class='button back-to-list' href="controleurFrontal.php?controleur=Produit&action=afficherListe">Retour à la liste des produits</a>
