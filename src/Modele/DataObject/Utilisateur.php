<?php
namespace App\MonBeauVelo\Modele\DataObject;
require_once 'AbstractDataObject.php';

class Utilisateur extends AbstractDataObject {

    private int $idUtilisateur;
    private string $nom;
    private string $prenom;
    private ?string $adresse;
    private ?string $telephone;
    private string $mdpHache;
    private bool $estAdmin;
    private ?string $email;
    private string $emailAValider;
    private string $nonce;

    public function __construct(
        int $idUtilisateur,
        string $nom,
        string $prenom,
        string $adresse,
        ?string $telephone,
        string $mdpHache,
        bool $estAdmin,
        ?string $email,
        ?string $emailAValider,
        ?string $nonce
    ) {
        $this->idUtilisateur = $idUtilisateur;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->telephone = $telephone;
        $this->mdpHache = $mdpHache;
        $this->estAdmin = $estAdmin;
        $this->email = $email;
        $this->emailAValider = $emailAValider ?? ''; // remplace null par une chaîne de caractères vide
        $this->nonce = $nonce ?? ''; // remplace null par une chaîne de caractères vide
    }

    public function formatTableau(): array {
        return array(
            "idUtilisateurTag" => $this->getIdUtilisateur(),
            "nomTag" => $this->getNom(),
            "prenomTag" => $this->getPrenom(),
            "adresseTag" => $this->getAdresse(),
            "telephoneTag" => $this->getTelephone(),
            "mdpHacheTag" => $this->getMdpHache(),
            "estAdminTag" => $this->getEstAdmin() ? 1 : 0,
            "emailTag" => $this->getEmail(),
            "emailAValiderTag" => $this->getEmailAValider(),
            "nonceTag" => $this->getNonce(),
        );
    }


    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void {
        $this->prenom = $prenom;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void {
        $this->adresse = $adresse;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void {
        $this->telephone = $telephone;
    }

    public function getMdpHache(): string {
        return $this->mdpHache;
    }

    public function setMdpHache(string $mdpHache): void {
        $this->mdpHache = $mdpHache;
    }

    public function getEstAdmin(): bool {
        return $this->estAdmin;
    }

    public function setEstAdmin(bool $estAdmin): void {
        $this->estAdmin = $estAdmin;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getEmailAValider(): ?string {
        return $this->emailAValider;
    }

    public function setEmailAValider(?string $emailAValider): void {
        $this->emailAValider = $emailAValider;
    }

    public function getNonce(): string {
        return $this->nonce;
    }

    public function setNonce(string $nonce): void {
        $this->nonce = $nonce;
    }

}
?>

