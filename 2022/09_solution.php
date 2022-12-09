<?php

declare(strict_types=1);

$testInput = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "09_input_test.txt"));
$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "09_input.txt"));

function testPartOne(string $input)
{
    $data = calculatePositions($input, false);
    $expected = 13;
    $tVisited = [
        ['x' => 0, 'y' => 0],
        ['x' => 1, 'y' => 0],
        ['x' => 2, 'y' => 0],
        ['x' => 3, 'y' => 0],
        ['x' => 4, 'y' => 1],
        ['x' => 4, 'y' => 2],
        ['x' => 4, 'y' => 3],
        ['x' => 3, 'y' => 4],
        ['x' => 2, 'y' => 4],
        ['x' => 3, 'y' => 3],
        ['x' => 4, 'y' => 3],
        ['x' => 3, 'y' => 2],
        ['x' => 2, 'y' => 2],
        ['x' => 1, 'y' => 2],
    ];
    if ($tVisited !== $data['tPositions']) {
        echo "Tests: ERROR, intersection is incorrect!\n";
        return false;
    }
    if ($data['hPos']['x'] != 2 || $data['hPos']['y'] != 2) {
        echo "Tests: ERROR, endpos of head is incorrect!\n";
        return false;
    }
    if ($data['tPos']['x'] != 1 || $data['tPos']['y'] != 2) {
        echo "Tests: ERROR, endpos of tail is incorrect!\n";
        return false;
    }
    $tPosCount = count(array_unique($data['tPositions'], SORT_REGULAR));
    if ($tPosCount != $expected) {
        echo "Tests: ERROR, expected {$expected} got {$tPosCount}\n";
        return false;
    }
    return true;
}

function testPartTwo(string $input)
{
    return true;
}

function tests(string $input): bool
{
    if (!testPartOne($input)) {
        return false;
    }
    if (!testPartTwo($input)) {
        return false;
    }
    return true;
}

function calculatePositions(string $input, $debug = false)
{
    $snakeLength = 2;
    $snake = [];
    for ($i = 0; $i < $snakeLength; $i++) {
        $snake[] = ['x' => 0, 'y' => 0];
    }
    $hPositions = [['x' => 0, 'y' => 0]];
    $tPositions = [['x' => 0, 'y' => 0]];
    $moves = explode("\n", $input);
    $c = count($moves);
    $xDirections = ['R', 'L'];
    $yDirections = ['U', 'D'];
    for ($i = 0; $i < $c; $i++) {
        if ($debug) {
            echo "=== " . $moves[$i] . " ===\n";
        }
        preg_match('/(R|L|U|D) (\d+)/', $moves[$i], $matches);
        for ($x = 0; $x < $matches[2]; $x++) {
            switch ($matches[1]) {
                case 'R':
                    $snake[0]['x']++;
                    break;
                case 'L':
                    $snake[0]['x']--;
                    break;
                case 'U':
                    $snake[0]['y']++;
                    break;
                case 'D':
                    $snake[0]['y']--;
                    break;
            }
            $hPositions[] = $snake[0];
            if ($debug) {
                echo "hPos\n";
                print_r($snake[0]);
            }
            $movesInX = in_array($matches[1], $xDirections);
            $xDiff = abs($snake[0]['x'] - $snake[1]['x']);
            $movesInY = in_array($matches[1], $yDirections);
            $yDiff = abs($snake[0]['y'] - $snake[1]['y']);
            if (
                ($movesInX && $xDiff > 1 || $movesInY && $yDiff > 1)
            ) {
                // move y
                $countHeadPosition = count($hPositions);
                $snake[1] = $hPositions[$countHeadPosition-2];
                $tPositions[] = $snake[1];
                if ($debug) {
                    echo "tPos\n";
                    print_r($snake[1]);
                }
            }
        }
    }
    return [
        'hPos'          => $snake[0],
        'hPositions'    => $hPositions,
        'tPos'          => $snake[1],
        'tPositions'    => $tPositions,
    ];
}

if (tests($testInput)) {
    $data = calculatePositions($input);
    $c = count(array_unique($data['tPositions'], SORT_REGULAR)); // 5930
    echo "Part one: {$c}\n";
}
