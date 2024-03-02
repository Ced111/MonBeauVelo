<?php
namespace App\MonBeauVelo\Modele\Repository;

use App\MonBeauVelo\Configuration\Configuration as Conf;
use PDO;

class ConnexionBaseDeDonnee {

    /** @var PDO */
    private static ?ConnexionBaseDeDonnee $instance = null;

    /** @var PDO */
    private PDO $pdo;

    public function __construct()
    {
        // Récupérez les informations de la classe Conf
        $hostname = Conf::getHostname();
        $port = Conf::getPort();
        $databaseName = Conf::getDatabase();
        $login = Conf::getLogin();
        $password = Conf::getPassword();

        // Connexion à la base de données
        // Le dernier argument sert à ce que toutes les chaines de caractères
        // en entrée et sortie de MySql soit dans le codage UTF-8
        $this->pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$databaseName", $login, $password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        // On active le mode d'affichage des erreurs, et le lancement d'exception en cas d'erreur
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    /** @return PDO */
    public static function getPdo(): PDO {
        return ConnexionBaseDeDonnee::getInstance()->pdo;
    }

    // getInstance s'assure que le constructeur ne sera appelé qu'une seule fois.
    // L'unique instance crée est stockée dans l'attribut $instance
    private static function getInstance() : ?ConnexionBaseDeDonnee {
        // L'attribut statique $pdo s'obtient avec la syntaxe Model::$pdo
        // au lieu de $this->pdo pour un attribut non statique
        if (is_null(ConnexionBaseDeDonnee::$instance))
            // Appel du constructeur
            ConnexionBaseDeDonnee::$instance = new ConnexionBaseDeDonnee();
        return ConnexionBaseDeDonnee::$instance;
    }

}
?>
