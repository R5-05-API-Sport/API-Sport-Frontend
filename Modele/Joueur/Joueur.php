<?php

namespace r401_frontend\Modele\Joueur;

use DateTime;
use JsonSerializable;

class Joueur implements JsonSerializable {
    private int $joueurId;
    private string $nom;
    private string $prenom;
    private string $numeroLicence;
    private DateTime $dateNaissance;
    private int $taille;
    private int $poids;
    private ?JoueurStatut $statut;

    public function __construct(
        int $joueurId,
        string $nom,
        string $prenom,
        string $numeroLicence,
        DateTime $dateNaissance,
        int $taille,
        int $poids,
        ?JoueurStatut $statut
    ) {
        $this->joueurId = $joueurId;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->numeroLicence = $numeroLicence;
        $this->dateNaissance = $dateNaissance;
        $this->taille = $taille;
        $this->poids = $poids;
        $this->statut = $statut;
    }

    public function jsonSerialize(): mixed {
        return [
            'joueurId' => $this->joueurId,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'numeroLicence' => $this->numeroLicence,
            'dateNaissance' => $this->dateNaissance,
            'taille' => $this->taille,
            'poids' => $this->poids,
            'statut' => $this->statut,
        ];
    }

    public static function JsonDeserialize(array $joueurJson): Joueur {

        $joueurId = $joueurJson["joueurId"];
        $nom = $joueurJson["nom"];
        $prenom = $joueurJson["prenom"];
        $numeroLicence = $joueurJson["numeroLicence"];
        $dateNaissance = new DateTime($joueurJson["dateNaissance"]);
        $taille = $joueurJson["taille"];
        $poids = $joueurJson["poids"];
        $statut = JoueurStatut::fromName($joueurJson["statut"]);

        return new Joueur($joueurId, $nom, $prenom, $numeroLicence, $dateNaissance, $taille, $poids, $statut);
    }

    public function nomOuPrenomContient(string $recherche) : bool {
        return str_contains(strtolower($this->nom), strtolower($recherche))
            || str_contains(strtolower($this->prenom), strtolower($recherche));
    }

    public function toString() : string {
        $selectableString = "";
        $selectableString .= $this->getnumeroLicence() . ' : ' . $this->nom . ' ' . $this->prenom;

        if ($this->statut !== JoueurStatut::ACTIF) {
            $selectableString .= ' (' . $this->statut->name . ')';
        }
        return $selectableString;
    }

    public function getJoueurId(): int
    {
        return $this->joueurId;
    }

    public function setJoueurId(int $joueurId): void
    {
        $this->joueurId = $joueurId;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getnumeroLicence() {
        return $this->numeroLicence;
    }

    public function getdateNaissance() : DateTime {
        return $this->dateNaissance;
    }

    public function gettaille() {
        return $this->taille;
    }

    public function getpoids() {
        return $this->poids;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setnumeroLicence(string $numeroLicence): void
    {
        $this->numeroLicence = $numeroLicence;
    }

    public function setdateNaissance(DateTime $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function settaille(int $taille): void
    {
        $this->taille = $taille;
    }

    public function setpoids(int $poids): void
    {
        $this->poids = $poids;
    }

    public function setStatut(?JoueurStatut $statut): void
    {
        $this->statut = $statut;
    }
}

