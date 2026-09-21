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
    && isset($_POST['numeroDeLicence'])
    && isset($_POST['dateDeNaissance'])
    && isset($_POST['tailleEnCm'])
    && isset($_POST['poidsEnKg'])
    && isset($_POST['statut'])
) {

    $joueurControleur = JoueurControleur::getInstance();

    $ajout = $joueurControleur->ajouterJoueur(
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['numeroDeLicence'],
        new DateTime($_POST['dateDeNaissance']),
        $_POST['tailleEnCm'],
        $_POST['poidsEnKg'],
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
    $formulaire->setText("Numéro de license", "numeroDeLicence", "00042");
    $formulaire->setDate("Date de naissance", "dateDeNaissance");
    $formulaire->setText("Taille (en cm)", "tailleEnCm");
    $formulaire->setText("Poids (en kg)", "poidsEnKg");
    $formulaire->setSelect("Statut", array_map(function($statut) { return $statut->name; } ,JoueurStatut::cases()), "statut");
    $formulaire->addButton("Submit", "create", "valider", "Valider");
    echo $formulaire;
}
