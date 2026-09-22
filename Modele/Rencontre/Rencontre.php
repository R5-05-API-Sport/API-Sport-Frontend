<?php

namespace r401_frontend\Modele\Rencontre;
use DateTime;
use r401_frontend\Modele\Rencontre\RencontreLieu;

class Rencontre {
    private int $rencontreId;
    private DateTime $dateHeure;
    private string $equipeAdverse;
    private string $adresse;
    private ?RencontreLieu $lieu;
    private ?RencontreResultat $resultat;

    public function __construct(
        DateTime $dateHeure,
        string $equipeAdverse,
        string $adresse,
        ?RencontreLieu $lieu,
        ?RencontreResultat $resultat = null,
        int $rencontreId = 0
    ) {
        $this->rencontreId = $rencontreId;
        $this->dateHeure = $dateHeure;
        $this->equipeAdverse = $equipeAdverse;
        $this->adresse = $adresse;
        $this->lieu = $lieu;
        $this->resultat = $resultat;
    }

    public static function JsonDeserialize(array $rencontreJson): Rencontre {
    $rencontreId = $rencontreJson["rencontreId"] ?? 0;

    $dateHeure = !empty($rencontreJson["dateHeure"])
        ? new DateTime($rencontreJson["dateHeure"])
        : new DateTime(); // or throw, depending on what makes sense

    $equipeAdverse = $rencontreJson["equipeAdverse"] ?? '';
    $adresse = $rencontreJson["adresse"] ?? '';

    $lieu = isset($rencontreJson["lieu"])
        ? RencontreLieu::fromName($rencontreJson["lieu"])
        : null;

    $resultat = null;
    if (!empty($rencontreJson["resultat"])) {
        $resultat = RencontreResultat::fromName($rencontreJson["resultat"]);
    }

    return new Rencontre($dateHeure, $equipeAdverse, $adresse, $lieu, $resultat, $rencontreId);
}

    public function getRencontreId(): int
    {
        return $this->rencontreId;
    }

    public function getdateHeure(): DateTime
    {
        return $this->dateHeure;
    }

    public function setdateHeure(DateTime $dateHeure): void {
        $this->dateHeure = $dateHeure;
    }

    public function getEquipeAdverse(): string
    {
        return $this->equipeAdverse;
    }

    public function setEquipeAdverse(string $equipeAdverse): void
    {
        $this->equipeAdverse = $equipeAdverse;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getLieu(): ?RencontreLieu
    {
        return $this->lieu;
    }

    public function setLieu(?RencontreLieu $lieu): void
    {
        $this->lieu = $lieu;
    }

    public function joue(): bool {
        return $this->resultat !== null;
    }

    public function gagne(): bool {
        return $this->resultat === RencontreResultat::VICTOIRE;
    }

    public function nul(): bool {
        return $this->resultat === RencontreResultat::NUL;
    }

    public function perdu(): bool {
        return $this->resultat === RencontreResultat::DEFAITE;
    }

    public function getResultat(): ?RencontreResultat
    {
        return $this->resultat;
    }

    public function setResultat(?RencontreResultat $resultat): void
    {
        $this->resultat = $resultat;
    }

    public function estPassee(): bool {
        return $this->dateHeure < new DateTime();
    }
}
