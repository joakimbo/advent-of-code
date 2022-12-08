<?php

declare(strict_types=1);

$testInput = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "08_input_test.txt"));
$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "08_input.txt"));

function calculateViewScore(string $row, int $col, array $views)
{
    $totScore = 1;
    foreach ($views as $key => $view) {
        $viewScore = 0;
        foreach ($view as $number) {
            $viewScore++;
            if ($row[$col] <= $number) {
                break;
            }
        }
        if ($viewScore > 0) {
            $totScore *= $viewScore;
        }
    }
    return $totScore;
}

function isVisible(string $row, int $col, array $views): bool
{
    foreach ($views as $key => $view) {
        if (max($view) >= $row[$col]) {
            unset($views[$key]);
            continue;
        }
    }
    return !empty($views);
}

function parseInputToData(string $input)
{
    $rows = explode("\n", $input);
    $maxScore = 0;
    $visibleCount = 0;
    $rowsCount = count($rows);
    for ($i = 0; $i < $rowsCount; $i++) {
        $currentRow = $rows[$i];
        $charCount = strlen($currentRow);
        for ($x = 0; $x < $charCount; $x++) {
            if ($i == 0 || $i == $rowsCount - 1 || $x == 0 || $x == $charCount - 1) {
                $visibleCount++;
                continue;
            }
            $views = [
                'left'  => str_split(strrev(substr($currentRow, 0, $x))),
                'right' => str_split(substr($currentRow, $x+1)),
                'up'    => [],
                'down'  => [],
            ];
            for ($y = $i-1; $y >= 0; $y--) {
                $views['up'][] = $rows[$y][$x];
            }
            for ($y = $i+1; $y < $rowsCount; $y++) {
                $views['down'][] = $rows[$y][$x];
            }
            if (isVisible($currentRow, $x, $views)) {
                $visibleCount++;
            }
            $viewScore = calculateViewScore($currentRow, $x, $views);
            if ($viewScore > $maxScore) {
                $maxScore = $viewScore;
            }
        }
    }
    return [
        'visibleTreeCount'  => $visibleCount,
        'maxViewScore'      => $maxScore,
    ];
}

function tests(string $testInput)
{
    $data = parseInputToData($testInput);
    $expected = 21;
    if ($data['visibleTreeCount'] != $expected) {
        echo "Tests P1: ERROR! expected {$expected} got {$data['visibleTreeCount']}\n";
    }
    $expected = 8;
    if ($data['maxViewScore'] != $expected) {
        echo "Tests P2: ERROR! expected {$expected} got {$data['maxViewScore']}\n";
    }
}

tests($testInput);
$data = parseInputToData($input);

// part one
echo "Part one: {$data['visibleTreeCount']}\n";  // 1832
// part two
echo "Part one: {$data['maxViewScore']}\n"; // 157320
