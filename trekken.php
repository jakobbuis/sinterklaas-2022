<?php

// Deelnemers
$people = [
    'Jakob',
    'Alies',
    'Paul',
    'Hermien',
    'Tim',
    'David',
    'Rosa',
];

// Mensen die elkaar niet mogen trekken
$forbiddenMatches = [
    'Jakob' => ['Jakob'],
    'Alies' => ['Alies', 'Paul'],
    'Paul' => ['Paul', 'Alies'],
    'Hermien' => ['Hermien', 'Tim'],
    'Tim' => ['Tim', 'Hermien'],
    'David' => ['David', 'Rosa'],
    'Rosa' => ['Rosa', 'David'],
];

// Aantal lootjes p.p.
$lotsPerPerson = 3;

restart: // marker voor als we overnieuw moeten beginnen

// bijhouden wie welke lootjes heeft
$draws = array_fill_keys($people, []);

// Genereer een pot met alle loodjes 1x voor de primaire trekking
$lots = $people;

// Neem alle lootjes in de pot
foreach ($lots as $lot) {
    // Bepaal wie dit lootje kunnen trekken: heeft nog geen lootje en is geen verboden match
    $potentials = array_filter($draws, function ($lotsDrawn, $name) use ($lot, $forbiddenMatches) {
        $isForbiddenMatch = in_array($lot, $forbiddenMatches[$name]);
        $hasLot = count($lotsDrawn) === 1;
        return !$isForbiddenMatch && !$hasLot;
    }, ARRAY_FILTER_USE_BOTH);

    // Als er niemand is die dit lootje mag trekken, hebben we een probleem: begin overnieuw
    if (count($potentials) === 0) {
        echo "Ongeldige oplossing, herstart" . PHP_EOL . PHP_EOL;
        goto restart;
    }

    // Wijs lootje toe aan een willekeurig persoon van de personen die het mogen trekken
    $drawee = array_rand($potentials);
    echo "{$drawee} trekt {$lot} als primair lootje" . PHP_EOL;
    array_push($draws[$drawee], $lot);
}

// Genereer een pot met alle lootjes (elke naam zit 2x in de pot)
$lots = [];
for ($i = 0; $i < $lotsPerPerson - 1; $i++) {
    $lots = array_merge($lots, $people);
}

// Neem alle lootjes in de pot
foreach ($lots as $lot) {
    // Bepaal wie dit lootje kunnen trekken: heeft nog geen drie lootjes, en is niet een verboden match
    $potentials = array_filter($draws, function ($lotsDrawn, $name) use ($lot, $lotsPerPerson, $forbiddenMatches) {
        $hasFewerLotsThanLimit = (count($lotsDrawn) < $lotsPerPerson);
        $isForbiddenMatch = in_array($lot, $forbiddenMatches[$name]);
        $hasLotAlready = in_array($lot, $lotsDrawn);
        return $hasFewerLotsThanLimit && !$isForbiddenMatch && !$hasLotAlready;
    }, ARRAY_FILTER_USE_BOTH);

    // Als er niemand is die dit lootje mag trekken, hebben we een probleem: begin overnieuw
    if (count($potentials) === 0) {
        echo "Ongeldige oplossing, herstart" . PHP_EOL . PHP_EOL;
        goto restart;
    }

    // Wijs lootje toe aan een willekeurig persoon van de personen die het mogen trekken
    $drawee = array_rand($potentials);
    echo "{$drawee} trekt {$lot} als secundair lootje" . PHP_EOL;
    array_push($draws[$drawee], $lot);
}
echo PHP_EOL;

// toon uitkomst
foreach ($draws as $drawee => $lots) {
    echo "{$drawee} heeft {$lots[0]} primair, secundair {$lots[1]} en {$lots[2]}" . PHP_EOL;
}