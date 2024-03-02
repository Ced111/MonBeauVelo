<?php use App\MonBeauVelo\Configuration\ConfigurationSite;
use App\MonBeauVelo\Lib\ConnexionUtilisateur; ?>
<form method="<?php echo ConfigurationSite::getDebug() ? 'get' : 'post'; ?>" action="controleurFrontal.php">

    <fieldset>
        <legend>Mettre à jour l'utilisateur :</legend>

        <?php if (ConnexionUtilisateur::estAdministrateur()): ?>
            <p>
                <label for="idUtilisateur_id">ID Utilisateur</label> :
                <input type="text" name="idUtilisateur" id="idUtilisateur_id" required
                       value="<?php echo htmlspecialchars($utilisateur->getIdUtilisateur()); ?>"/>
            </p>
        <?php else: ?>
            <input type="hidden" name="idUtilisateur" value="<?php echo htmlspecialchars($utilisateur->getIdUtilisateur()); ?>"/>
        <?php endif; ?>

        <p>
            <label for="nom_id">Nom</label> :
            <input type="text" name="nom" id="nom_id" required
                   value="<?php echo htmlspecialchars($utilisateur->getNom()); ?>"/>
        </p>

        <p>
            <label for="prenom_id">Prénom</label> :
            <input type="text" name="prenom" id="prenom_id" required
                   value="<?php echo htmlspecialchars($utilisateur->getPrenom()); ?>"/>
        </p>

        <p>
            <label for="email_id">Email</label> :
            <input type="email" name="email" id="email_id" required
                   value="<?php echo htmlspecialchars($utilisateur->getEmail()); ?>"/>
        </p>

        <p>
            <label for="adresse_id">Adresse</label> :
            <textarea name="adresse" id="adresse_id" required><?php echo htmlspecialchars($utilisateur->getAdresse()); ?></textarea>
        </p>

        <p>
            <label for="telephone_id">Téléphone</label> :
            <input type="text" name="telephone" id="telephone_id" required
                   value="<?php echo htmlspecialchars($utilisateur->getTelephone()); ?>"/>
        </p>

        <?php $utilisateurConnecte = ConnexionUtilisateur::getIdUtilisateurConnecte(); ?>
        <!-- Champ pour l'ancien mot de passe -->
        <?php if (!ConnexionUtilisateur::estAdministrateur() || $utilisateurConnecte == $utilisateur->getIdUtilisateur()): ?>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="mdp_old_id">Ancien mot de passe&#42;</label>
                <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp_old" id="mdp_old_id" required>
            </p>
        <?php endif; ?>

        <!-- Champ pour le nouveau mot de passe -->
        <?php if (!ConnexionUtilisateur::estAdministrateur() || $utilisateurConnecte == $utilisateur->getIdUtilisateur()): ?>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="mdp_id">Nouveau mot de passe&#42;</label>
                <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp" id="mdp_id" required>
            </p>
        <?php endif; ?>

        <!-- Champ pour la vérification du nouveau mot de passe -->
        <?php if (!ConnexionUtilisateur::estAdministrateur() || $utilisateurConnecte == $utilisateur->getIdUtilisateur()): ?>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="mdp2_id">Vérification du nouveau mot de passe&#42;</label>
                <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp2" id="mdp2_id" required>
            </p>
        <?php endif; ?>

        <!-- Champ pour le statut d'administrateur -->
        <?php if (ConnexionUtilisateur::estAdministrateur()): ?>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="estAdmin_id">Administrateur</label>
                <input class="InputAddOn-field" type="checkbox" name="estAdmin" id="estAdmin_id" <?php echo $utilisateur->getEstAdmin() ? 'checked' : ''; ?>>
            </p>
        <?php endif; ?>

        <input type="hidden" name="action" value="mettreAJour">
        <input type="hidden" name="controleur" value="utilisateur">
        <p>
            <input type="submit" value="Mettre à jour"/>
        </p>
    </fieldset>
</form>
