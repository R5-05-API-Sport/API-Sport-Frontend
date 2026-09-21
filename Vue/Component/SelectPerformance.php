<?php

namespace r401_frontend\Vue\Component;

use r401_frontend\Modele\Participation\Performance;

class SelectPerformance extends Select {

    public function __construct(
            ?string $description,
            ?string $selectedValue = null
    ) {
        $values = [];
        foreach (Performance::cases() as $performance) {
            $values[$performance->name] = $performance->name;
        }

        parent::__construct($values, "performance", $description, $selectedValue);
    }
}