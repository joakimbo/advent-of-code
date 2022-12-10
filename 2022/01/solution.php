<?php

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt'));
$input = explode("\n\n", $input);

$totals = [];
foreach ($input as &$elf) {
    $elfTot = 0;
    $elf = explode("\n", $elf);
    foreach ($elf as $item) {
        $elfTot += $item;
    }
    $totals[] = $elfTot;
}
unset($elf);
rsort($totals, SORT_NUMERIC);
echo "Part one: {$totals[0]}\n"; // 71502
echo "Part two: " . $totals[0] + $totals[1] + $totals[2] . "\n"; // 208191
