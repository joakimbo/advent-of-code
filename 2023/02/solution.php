<?php

declare(strict_types=1);

function solve($part, $input, $totalNumberOfCubes)
{
    $games = [];
    $lines = explode("\n", $input);
    foreach ($lines as $line) {
        preg_match('/Game (\d+):/', $line, $matches);
        $gameId = $matches[1];
        $games[$gameId] = [
            'red'   => 0,
            'green' => 0,
            'blue'  => 0,
        ];
        $power = 1;
        $isValid = true;
        foreach (array_keys($games[$gameId]) as $color) {
            preg_match_all('/ (\d+) ' . $color . '/', $line, $matches);
            for ($i = 0; $i < count($matches[1]); $i++) {
                if ((int)$matches[1][$i] > $totalNumberOfCubes[$color]) {
                    $isValid = false;
                }
                if ((int)$matches[1][$i] > $games[$gameId][$color]) {
                    $games[$gameId][$color] = (int)$matches[1][$i];
                }
            }
            $power *= $games[$gameId][$color];
        }
        $games[$gameId]['power'] = $power;
        $games[$gameId]['isValid'] = $isValid;
    }
    $validGames = array_filter(
        $games,
        function ($element) {
            return $element['isValid'];
        }
    );
    return $part === 'one' ? array_sum(array_keys($validGames)) : array_sum(array_column($games, 'power'));
}

function tests()
{
    $input = "Game 1: 3 blue, 4 red; 1 red, 2 green, 6 blue; 2 green\nGame 2: 1 blue, 2 green; 3 green, 4 blue, 1 red; 1 green, 1 blue\nGame 3: 8 green, 6 blue, 20 red; 5 blue, 4 red, 13 green; 5 green, 1 red\nGame 4: 1 green, 3 red, 6 blue; 3 green, 6 red; 3 green, 15 blue, 14 red\nGame 5: 6 red, 1 blue, 3 green; 2 blue, 1 red, 2 green";
    $totalNumberOfCubes = [
        'red'   => 12,
        'green' => 13,
        'blue'  => 14,
    ];

    if (solve('one', $input, $totalNumberOfCubes) === 8) {
        echo "TEST ONE OK!\n";
    } else {
        echo "TEST ONE FAIL\n";
    }

    if (solve('two', $input, $totalNumberOfCubes) === 2286) {
        echo "TEST TWO OK!\n";
    } else {
        echo "TEST TWO FAIL\n";
    }
}

tests();

$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
$resultPart1 = solve('one', $input1, ['red' => 12, 'green' => 13, 'blue' => 14]);
echo "Part 1: {$resultPart1}\n"; // 2727

$resultPart2 = solve('two', $input1, ['red' => 12, 'green' => 13, 'blue' => 14]);
echo "Part 2: {$resultPart2}\n"; // 56580
