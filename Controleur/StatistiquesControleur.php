<?php

namespace r401_frontend\Controleur;

use DateTime;
use r401_frontend\Modele\Joueur\Commentaire\Commentaire;
use r401_frontend\Modele\Joueur\Commentaire\CommentaireDAO;
use r401_frontend\Modele\Joueur\Joueur;
use r401_frontend\Modele\Joueur\JoueurDAO;
use r401_frontend\Modele\Joueur\JoueurStatut;
use r401_frontend\Modele\Statistiques\StatistiquesEquipe;
use r401_frontend\Modele\Statistiques\StatistiquesJoueurs;

class StatistiquesControleur {
    private static ?StatistiquesControleur $instance = null;
    private readonly RencontreControleur $rencontres;
    private readonly ParticipationControleur $participations;

    private function __construct() {
        $this->rencontres = RencontreControleur::getInstance();
        $this->participations = ParticipationControleur::getInstance();
    }

    public static function getInstance(): StatistiquesControleur {
        if (self::$instance == null) {
            self::$instance = new StatistiquesControleur();
        }
        return self::$instance;
    }

    public function getStatistiquesEquipe() : StatistiquesEquipe {
        return new StatistiquesEquipe($this->rencontres->listerToutesLesRencontres());
    }

    public function getStatistiquesJoueurs() : StatistiquesJoueurs {
        return new StatistiquesJoueurs($this->participations->listerToutesLesParticipations(), $this->rencontres->listerToutesLesRencontres());
    }
}