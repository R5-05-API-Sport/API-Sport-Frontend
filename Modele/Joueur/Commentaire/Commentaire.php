<?php
namespace r401_frontend\Modele\Joueur\Commentaire;

use DateTime;
use JsonSerializable;

class Commentaire implements JsonSerializable {
    private int $commentaireId;
    private readonly string $contenu;
    private readonly DateTime $date;

    public function __construct(int $commentaireId, string $contenu, DateTime $date)
    {
        $this->commentaireId = $commentaireId;
        $this->contenu = $contenu;
        $this->date = $date;
    }

    public function jsonSerialize(): mixed {
        return [
            'commentaireId' => $this->commentaireId,
            'contenu' => $this->contenu,
            'date' => $this->date,
        ];
    }

    public function getCommentaireId(): int
    {
        return $this->commentaireId;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }


}


