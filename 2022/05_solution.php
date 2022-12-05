<?php

declare(strict_types=1);

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "05_input.txt"));
$input = explode("\n\n", $input);

$alphabet = range('A', 'Z');
$lines = explode("\n", $input[0]);
$stacks = [];
foreach ($lines as $line) {
    $stackIndex = 0;
    for ($pos = 1; $pos < strlen($line); $pos += 4) {
        if (isset($line[$pos]) && in_array($line[$pos], $alphabet)) {
            $stacks[$stackIndex][] = $line[$pos];
        }
        $stackIndex++;
    }
}

function getOrder(array $stacks)
{
    $order = '';
    for ($i = 0; $i < count($stacks); $i++) {
        if (isset($stacks[$i][0])) {
            $order .= $stacks[$i][0];
        }
    }
    return $order;
}

function moveCrates($procedureInput, $stacks, string $crateMover = '9000')
{
    $lines = explode("\n", $procedureInput);
    foreach ($lines as $line) {
        preg_match("/(\d+) from (\d+) to (\d+)/", $line, $matches);
        $values = [];
        for ($i = 0; $i < $matches[1]; $i++) {
            $values[] = array_shift($stacks[$matches[2]-1]);
        }
        if ($crateMover == '9001') {
            array_unshift($stacks[$matches[3]-1], ...$values);
        } else {
            array_unshift($stacks[$matches[3]-1], ...array_reverse($values));
        }
    }
    return getOrder($stacks);
}

echo "Part one: " . moveCrates($input[1], $stacks) . "\n"; // GRTSWNJHH
echo "Part two: " . moveCrates($input[1], $stacks, '9001') . "\n"; // QLFQDBBHM
