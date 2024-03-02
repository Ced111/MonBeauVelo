<?php
use App\MonBeauVelo\Configuration\ConfigurationSite;
?>
<form method="<?= ConfigurationSite::getDebug() ? 'get' : 'post' ?>" action="controleurFrontal.php">
    <fieldset>
        <legend>Formulaire de création de commande :</legend>
        <p>
            <label for="idUtilisateur_id">ID utilisateur</label> :
            <input type="number" name="idUtilisateur" id="idUtilisateur_id" required/>
        </p>
        <p>
            <label for="dateCommande_id">Date de commande</label> :
            <input type="date" name="dateCommande" id="dateCommande_id" required/>
        </p>
        <p>
            <label for="statut_id">Statut</label> :
            <select name="statut" id="statut_id" required>
                <option value="panier">Panier</option>
                <option value="payee">Payée</option>
                <option value="envoyee">Envoyée</option>
                <option value="recue">Reçue</option>
            </select>
        </p>
        <!-- champ caché pour l'action -->
        <input type="hidden" name="action" value="creerDepuisFormulaire">

        <p>
            <input type="submit" value="Envoyer" />
        </p>
    </fieldset>
</form>
