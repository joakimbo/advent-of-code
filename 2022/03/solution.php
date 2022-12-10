<?php

declare(strict_types=1);

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));
$rucksacks = explode("\n", $input);
$lowerAndUpper = array_flip(str_split(strtolower(implode(range('a', 'z'))) . strtoupper(implode(range('a', 'z')))));

// part one
$totPrio = 0;
foreach ($rucksacks as $rucksack) {
    $compSize = strlen($rucksack) / 2;
    $intersect = array_values(
        array_unique(
            array_intersect(
                str_split(substr($rucksack, 0, $compSize)),
                str_split(substr($rucksack, $compSize *-1))
            )
        )
    );
    $totPrio += isset($lowerAndUpper[$intersect[0]]) ? $lowerAndUpper[$intersect[0]] + 1 : 0;
}
echo "Part one: {$totPrio}\n";

// part two
$groups = array_chunk($rucksacks, 3);
$totPrio = 0;
foreach ($groups as $group) {
    $intersect = array_unique(array_values(
        array_intersect(
            str_split($group[0]),
            str_split($group[1]),
            str_split($group[2])
        )
    ));
    $totPrio += isset($lowerAndUpper[$intersect[0]]) ? $lowerAndUpper[$intersect[0]] + 1 : 0;
}
echo "Part two: {$totPrio}\n";
