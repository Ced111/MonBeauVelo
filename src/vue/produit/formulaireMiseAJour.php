<?php
use App\MonBeauVelo\Configuration\ConfigurationSite;
?>
<form method="<?= ConfigurationSite::getDebug() ? 'get' : 'post' ?>" action="controleurFrontal.php">
    <fieldset>
        <legend>Formulaire de mise à jour :</legend>
        <p>
            <label for="idProduit_id">ID produit</label> :
            <input type="number" name="idProduit" id="idProduit_id" required readonly value="<?php echo htmlspecialchars($produit->getIdProduit()); ?>"/>
        </p>
        <p>
            <label for="idCategorie_id">ID catégorie</label> :
            <input type="number" name="idCategorie" id="idCategorie_id" required value="<?php echo htmlspecialchars($produit->getIdCategorie()); ?>"/>
        </p>
        <p>
            <label for="nom_id">Nom</label> :
            <input type="text" name="nom" id="nom_id" required value="<?php echo htmlspecialchars($produit->getNom()); ?>"/>
        </p>
        <p>
            <label for="marque_id">Marque</label> :
            <input type="text" name="marque" id="marque_id" value="<?php echo htmlspecialchars($produit->getMarque() ?? ''); ?>"/>
        </p>
        <p>
            <label for="description_id">Description</label> :
            <textarea name="description" id="description_id" required><?php echo htmlspecialchars($produit->getDescription()); ?></textarea>
        </p>
        <p>
            <label for="prix_id">Prix</label> :
            <input type="number" step="0.01" name="prix" id="prix_id" required value="<?php echo htmlspecialchars($produit->getPrix()); ?>"/>
        </p>
        <p>
            <label for="stock_id">Stock</label> :
            <input type="number" name="stock" id="stock_id" required value="<?php echo htmlspecialchars($produit->getStock()); ?>"/>
        </p>
        <p>
            <label for="image_id">Image URL</label> :
            <input type="text" name="image" id="image_id" value="<?php echo htmlspecialchars($produit->getImage() ?? ''); ?>"/>
        </p>
        <!-- champ caché pour l'action -->
        <input type="hidden" name="action" value="mettreAJour">

        <p>
            <input type="submit" value="Mettre à jour" />
        </p>
    </fieldset>
</form>
