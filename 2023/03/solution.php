<?php

declare(strict_types=1);

function solve($input, $part = 'one')
{
    $lineLength = strpos($input, "\n");
    preg_match_all('/[^\d|a-z|A-Z|\.|\n]/', $input, $matches, PREG_OFFSET_CAPTURE);
    $symbolPositions = [];
    foreach ($matches[0] as $symbolMatch) {
        $symbolPositions[$symbolMatch[1]] = $symbolMatch[0];
    }

    $valuesToSum = [];

    $gears = [];

    preg_match_all('/\d+/', $input, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches[0] as $match) {
        $value = $match[0];
        $valuePosition =  $match[1];
        $valueLength = strlen($value);

        for ($i = $valuePosition; $i < $valuePosition + $valueLength; $i++) {
            if ($i === $valuePosition && $i - 1 >= 0 && $input[$i - 1] != "\n" && in_array($input[$i - 1], $symbolPositions)) {
                if ($symbolPositions[$i - 1] == '*') {
                    $gears[$i - 1][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            if ($i === ($valuePosition + $valueLength - 1) && ($valuePosition + $valueLength) < strlen($input) && $input[$valuePosition + $valueLength] != "\n" && in_array($input[$valuePosition + $valueLength], $symbolPositions)) {
                if ($symbolPositions[$valuePosition + $valueLength] == '*') {
                    $gears[$valuePosition + $valueLength][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            // row above
            if ($i - ($lineLength + 2) >= 0 && in_array($input[$i - ($lineLength + 2)], $symbolPositions)) {
                if ($symbolPositions[$i - ($lineLength + 2)] == '*') {
                    $gears[$i - ($lineLength + 2)][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            if ($i - ($lineLength + 1) >= 0 && in_array($input[$i - ($lineLength + 1)], $symbolPositions)) {
                if ($symbolPositions[$i - ($lineLength + 1)] == '*') {
                    $gears[$i - ($lineLength + 1)][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            if ($i - $lineLength >= 0 && in_array($input[$i - $lineLength], $symbolPositions)) {
                if ($symbolPositions[$i - $lineLength] == '*') {
                    $gears[$i - $lineLength][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            // row below
            if ($i + ($lineLength + 2) < strlen($input) && in_array($input[$i + ($lineLength + 2)], $symbolPositions)) {
                if ($symbolPositions[$i + ($lineLength + 2)] == '*') {
                    $gears[$i + ($lineLength + 2)][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            if ($i + ($lineLength + 1) < strlen($input) && in_array($input[$i + ($lineLength + 1)], $symbolPositions)) {
                if ($symbolPositions[$i + ($lineLength + 1)] == '*') {
                    $gears[$i + ($lineLength + 1)][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
            if ($i + ($lineLength) < strlen($input) && in_array($input[$i + ($lineLength)], $symbolPositions)) {
                if ($symbolPositions[$i + ($lineLength)] == '*') {
                    $gears[$i + ($lineLength)][] = $value;
                }
                $valuesToSum[] = $value;
                break;
            }
        }
    }

    $gears = array_filter($gears, function ($element) {
        if (count($element) == 2) {
            return true;
        }
    });

    $gears = array_map(function ($element) {
        return array_product($element);
    }, $gears);

    $sum = array_sum($gears);

    return $part == 'one' ? array_sum($valuesToSum) : $sum;
}

function tests()
{
    $input = <<<INPUT
    467..114..
    ...*......
    ..35..633.
    ......#...
    617*......
    .....+.58.
    ..592.....
    ......755.
    ...$.*....
    .664.598..
    INPUT;

    $result = solve($input);
    if ($result == 4361) {
        echo "TEST ONE OK: {$result}\n";
    } else {
        echo "TEST ONE FAIL: {$result}\n";
    }

    $result = solve($input, 'two');
    if ($result == 467835) {
        echo "TEST TWO OK: {$result}\n";
    } else {
        echo "TEST TWO FAIL: {$result}\n";
    }
}

tests();

$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
$resultPart1 = solve($input1);
echo "Part 1: {$resultPart1}\n";

$resultPart2 = solve($input1, 'two');
echo "Part 2: {$resultPart2}\n";
