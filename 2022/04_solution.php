<?php

declare(strict_types=1);

$input = trim(file_get_contents("./04_input.txt"));
$pairs = explode("\n", $input);

$c1 = 0;
$c2 = 0;
foreach ($pairs as $pair) {
    $separated = explode(',', $pair);
    $first = explode('-', $separated[0]);
    $second = explode('-', $separated[1]);
    if (
        $first[0] >= $second[0] && $first[1] <= $second[1]
        || $second[0] >= $first[0] && $second[1] <= $first[1]
    ) {
        $c1++;
    }
    if ($first[0] <= $second[1] && $first[1] >= $second[0]) {
        $c2++;
    }
}
echo "Part one: {$c1}\n";
echo "Part two: {$c2}\n";
