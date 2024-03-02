<?php
use App\MonBeauVelo\Lib\ConnexionUtilisateur;
use App\MonBeauVelo\Configuration\ConfigurationSite;

// Utilisez directement les valeurs de session si elles existent, sinon initialisez à une chaîne vide
$nom = $_SESSION['form_values']['nom'] ?? '';
$prenom = $_SESSION['form_values']['prenom'] ?? '';
$email = $_SESSION['form_values']['email'] ?? '';
$adresse = $_SESSION['form_values']['adresse'] ?? '';
$telephone = $_SESSION['form_values']['telephone'] ?? '';
$estAdminChecked = isset($_SESSION['form_values']['estAdmin']) && $_SESSION['form_values']['estAdmin'] ? 'checked' : '';

// Supprimez les données de session
unset($_SESSION['form_values']);
?>

<form method="<?php echo ConfigurationSite::getDebug() ? 'get' : 'post'; ?>" action="controleurFrontal.php">

    <fieldset>
        <legend>Formulaire de création d'utilisateur :</legend>

        <!-- Nom -->
        <p>
            <label for="nom_id">Nom</label> :
            <input type="text" name="nom" id="nom_id" required value="<?= htmlspecialchars($nom) ?>"/>
        </p>

        <!-- Prénom -->
        <p>
            <label for="prenom_id">Prénom</label> :
            <input type="text" name="prenom" id="prenom_id" required value="<?= htmlspecialchars($prenom) ?>"/>
        </p>

        <!-- Email -->
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="email_id">Email&#42;</label>
            <input class="InputAddOn-field" type="email" name="email" id="email_id" required value="<?= htmlspecialchars($email) ?>" placeholder="toto@yopmail.com">
        </p>

        <!-- Adresse -->
        <p>
            <label for="adresse_id">Adresse</label> :
            <input type="text" name="adresse" id="adresse_id" required value="<?= htmlspecialchars($adresse) ?>"/>
        </p>

        <!-- Téléphone -->
        <p>
            <label for="telephone_id">Téléphone</label> :
            <input type="text" name="telephone" id="telephone_id" required value="<?= htmlspecialchars($telephone) ?>"/>
        </p>

        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp_id">Mot de passe&#42;</label>
            <input class="InputAddOn-field" type="password" name="mdp" id="mdp_id" required>
        </p>

        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp2_id">Vérification du mot de passe&#42;</label>
            <input class="InputAddOn-field" type="password" name="mdp2" id="mdp2_id" required>
        </p>

        <?php if (ConnexionUtilisateur::estAdministrateur()): ?>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="estAdmin_id">Administrateur</label>
                <input class="InputAddOn-field" type="checkbox" name="estAdmin" id="estAdmin_id" <?= isset($_SESSION['form_values']['estAdmin']) ? 'checked' : '' ?>>
            </p>
        <?php endif; ?>

        <input type="hidden" name="action" value="creerDepuisFormulaire">
        <input type="hidden" name="controleur" value="utilisateur">

        <p>
            <input type="submit" value="Créer utilisateur" />
        </p>
    </fieldset>
</form>