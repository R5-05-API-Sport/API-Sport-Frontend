<h1>Ajouter un joueur</h1>
<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use r401_frontend\Modele\Joueur\JoueurStatut;
use r401_frontend\Vue\Component\Formulaire;
use r401_frontend\Controleur\JoueurControleur;

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['nom'])
    && isset($_POST['prenom'])
    && isset($_POST['numeroLicence'])
    && isset($_POST['dateNaissance'])
    && isset($_POST['taille'])
    && isset($_POST['poids'])
    && isset($_POST['statut'])
) {

    $joueurControleur = JoueurControleur::getInstance();

    $ajout = $joueurControleur->ajouterJoueur(
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['numeroLicence'],
        new DateTime($_POST['dateNaissance']),
        $_POST['taille'],
        $_POST['poids'],
        $_POST['statut'],
    );

    if ($ajout) {
        header('Location: /joueur');
    } else {
        error_log("Erreur lors de la création du joueur");
    }

} else {
    $formulaire = new Formulaire("/joueur/ajouter");
    $formulaire->setText("Nom", "nom");
    $formulaire->setText("Prenom", "prenom");
    $formulaire->setText("Numéro de license", "numeroLicence", "00042");
    $formulaire->setDate("Date de naissance", "dateNaissance");
    $formulaire->setText("Taille (en cm)", "taille");
    $formulaire->setText("Poids (en kg)", "poids");
    $formulaire->setSelect("Statut", array_map(function($statut) { return $statut->name; } ,JoueurStatut::cases()), "statut");
    $formulaire->addButton("Submit", "create", "valider", "Valider");
    echo $formulaire;
}
