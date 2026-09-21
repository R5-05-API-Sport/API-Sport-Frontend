<h1>Modifier un joueur</h1>
<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use r401_frontend\Controleur\JoueurControleur;
use r401_frontend\Modele\Joueur\JoueurStatut;
use r401_frontend\Vue\Component\Formulaire;

$controleur = JoueurControleur::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['id'])
    && isset($_POST['nom'])
    && isset($_POST['prenom'])
    && isset($_POST['dateNaissance'])
    && isset($_POST['taille'])
    && isset($_POST['poids'])
    && isset($_POST['statut'])
) {

    if (
        $controleur->modifierJoueur(
            $_GET['id'],
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['numeroLicence'],
            new DateTime($_POST['dateNaissance']),
            $_POST['taille'],
            $_POST['poids'],
            $_POST['statut']
        )
    ) {
        header('Location: /joueur');
    }else{
        error_log("Erreur lors de la modification du joueur");
    }
} else {
    if (!isset($_GET['id'])) {
        header("Location: /joueur");
    } else {
        $joueur = $controleur->getJoueurById($_GET['id']);

        $formulaire = new Formulaire("/joueur/modifier?id=".$joueur->getJoueurId());
        $formulaire->setText("Nom", "nom", "", $joueur->getNom());
        $formulaire->setText("Prenom", "prenom", "", $joueur->getPrenom());
        $formulaire->setText("Numéro de license", "numeroLicence", "00042", $joueur->getnumeroLicence());
        $formulaire->setDate("Date de naissance", "dateNaissance", $joueur->getdateNaissance()->format('Y-m-d'));
        $formulaire->setText("Taille (en cm)", "taille", "", $joueur->gettaille());
        $formulaire->setText("Poids (en Kg)", "poids", "", $joueur->getpoids());
        $formulaire->setSelect("Statut", array_map(function($statut) { return $statut->name; }, JoueurStatut::cases()), "statut");
        $formulaire->addButton("Submit", "update", "modifier","Modifier");
        echo $formulaire;
    }
}