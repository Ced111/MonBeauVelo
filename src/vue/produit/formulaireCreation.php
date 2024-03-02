<?php
use App\MonBeauVelo\Configuration\ConfigurationSite;
?>
<form method="<?= ConfigurationSite::getDebug() ? 'get' : 'post' ?>" action="controleurFrontal.php">
    <fieldset>
        <legend>Formulaire de création de produit :</legend>
        <p>
            <label for="idCategorie_id">ID catégorie</label> :
            <input type="number" name="idCategorie" id="idCategorie_id" required/>
        </p>
        <p>
            <label for="nom_id">Nom</label> :
            <input type="text" name="nom" id="nom_id" required/>
        </p>
        <p>
            <label for="marque_id">Marque</label> :
            <input type="text" name="marque" id="marque_id"/>
        </p>
        <p>
            <label for="description_id">Description</label> :
            <textarea name="description" id="description_id" required></textarea>
        </p>
        <p>
            <label for="prix_id">Prix</label> :
            <input type="number" step="0.01" name="prix" id="prix_id" required/>
        </p>
        <p>
            <label for="stock_id">Stock</label> :
            <input type="number" name="stock" id="stock_id" required/>
        </p>
        <p>
            <label for="image_id">URL de l'image</label> :
            <input type="text" name="image" id="image_id"/>
        </p>
        <!-- champ caché pour l'action -->
        <input type="hidden" name="action" value="creerDepuisFormulaire">

        <p>
            <input type="submit" value="Envoyer" />
        </p>

    </fieldset>
</form>


