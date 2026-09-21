<?php
namespace r401_frontend\Modele\Participation;

enum Poste
{
    case TOPLANE;
    case JUNGLE;
    case MIDLANE;
    case ADCARRY;
    case SUPPORT;

    public static function fromName(string $name): ?Poste
    {
        foreach (self::cases() as $poste) {
            if( $name === $poste->name ){
                return $poste;
            }
        }

        return null;
    }
}
