<?php

namespace App\Entity;

class ActionChange
{
    public static function createFromEntity (Action $action) {
        $truck = new static;
        $truck->campagne    = $action->getCampagne();
        $truck->atterissage = $action->getAtterissage();

        return $truck;
    }
}
