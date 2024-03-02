<?php use App\MonBeauVelo\Configuration\ConfigurationSite; ?>
<form method="<?php echo ConfigurationSite::getDebug() ? 'get' : 'post'; ?>" action="controleurFrontal.php">

    <fieldset>
        <legend>Formulaire de création d'utilisateur :</legend>

        <p>
            <label for="nom_id">Nom</label> :
            <input type="text" name="nom" id="nom_id" required/>
        </p>

        <p>
            <label for="prenom_id">Prénom</label> :
            <input type="text" name="prenom" id="prenom_id" required/>
        </p>

        <p class="InputAddOn">
            <label class="InputAddOn-item" for="email_id">Email&#42;</label>
            <input class="InputAddOn-field" type="email" value="" placeholder="toto@yopmail.com" name="email" id="email_id" required>
        </p>

        <p>
            <label for="adresse_id">Adresse</label> :
            <input type="text" name="adresse" id="adresse_id" required/>
        </p>

        <p>
            <label for="telephone_id">Téléphone</label> :
            <input type="text" name="telephone" id="telephone_id" required/>
        </p>

        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp_id">Mot de passe&#42;</label>
            <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp" id="mdp_id" required>
        </p>

        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp2_id">Vérification du mot de passe&#42;</label>
            <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp2" id="mdp2_id" required>
        </p>

        <?php use App\MonBeauVelo\Lib\ConnexionUtilisateur;
        if (ConnexionUtilisateur::estAdministrateur()): ?>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="estAdmin_id">Administrateur</label>
                <input class="InputAddOn-field" type="checkbox" placeholder="" name="estAdmin" id="estAdmin_id">
            </p>
        <?php endif; ?>

        <input type="hidden" name="action" value="creerDepuisFormulaire">
        <input type="hidden" name="controleur" value="utilisateur">

        <p>
            <input type="submit" value="Créer utilisateur" />
        </p>
    </fieldset>
</form>
