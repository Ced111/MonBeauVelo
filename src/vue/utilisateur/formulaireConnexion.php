<?php use App\MonBeauVelo\Configuration\ConfigurationSite; ?>
<form action="controleurFrontal.php?action=connecter&controleur=utilisateur" method="<?php echo ConfigurationSite::getDebug() ? 'get' : 'post'; ?>">
    <div>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="motDePasse">Mot de passe :</label>
        <input type="password" id="motDePasse" name="motDePasse" required>
    </div>
    <input type="hidden" name="action" value="connecter">
    <input type="hidden" name="controleur" value="utilisateur">
    <div>
        <input type="submit" value="Se connecter">
    </div>
</form>
