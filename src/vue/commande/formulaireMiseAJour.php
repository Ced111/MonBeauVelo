<?php

use App\MonBeauVelo\Configuration\ConfigurationSite;

?>
<form method="<?= ConfigurationSite::getDebug() ? 'get' : 'post' ?>" action="controleurFrontal.php">
    <fieldset>
        <legend>Formulaire de mise à jour :</legend>
        <p>
            <label for="idCommande_id">ID commande</label> :
            <input type="number" name="idCommande" id="idCommande_id" required readonly value="<?php echo htmlspecialchars($commande->getIdCommande()); ?>"/>
        </p>
        <p>
            <label for="idUtilisateur_id">ID utilisateur</label> :
            <input type="number" name="idUtilisateur" id="idUtilisateur_id" required readonly value="<?php echo htmlspecialchars($commande->getIdUtilisateur()); ?>"/>
        </p>
        <p>
            <label for="dateCommande_id">Date de commande</label> :
            <input type="date" name="dateCommande" id="dateCommande_id" required readonly value="<?php echo htmlspecialchars($commande->getDateCommande()); ?>"/>
        </p>
        <p>
            <label for="statut_id">Statut</label> :
            <select name="statut" id="statut_id" required>
                <option value="panier" <?php echo $commande->getStatut() == 'panier' ? 'selected' : ''; ?>>Panier</option>
                <option value="payee" <?php echo $commande->getStatut() == 'payee' ? 'selected' : ''; ?>>Payée</option>
                <option value="envoyee" <?php echo $commande->getStatut() == 'envoyee' ? 'selected' : ''; ?>>Envoyée</option>
                <option value="recue" <?php echo $commande->getStatut() == 'recue' ? 'selected' : ''; ?>>Reçue</option>
            </select>
        </p>
        <!-- champ caché pour l'action -->
        <input type="hidden" name="action" value="mettreAJour">
        <input type="hidden" name="controleur" value="commande">

        <p>
            <input type="submit" value="Mettre à jour" />
        </p>
    </fieldset>
</form>
