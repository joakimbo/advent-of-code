<?php

declare(strict_types=1);

function testPartOne()
{
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "09_input_test.txt"));
    $data = calculatePositions($input, 2);
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
    $errorCount = 0;
    $errorMsg = "";
    if ($tVisited !== $data['tPositions']) {
        $errorMsg .= "Tests: ERROR, intersection is incorrect!\n";
        $errorCount++;
    }
    if ($data['hPos']['x'] != 2 || $data['hPos']['y'] != 2) {
        $errorMsg .= "Tests: ERROR, endpos of head is incorrect!\n";
        $errorCount++;
    }
    if ($data['tPos']['x'] != 1 || $data['tPos']['y'] != 2) {
        $errorMsg .= "Tests: ERROR, endpos of tail is incorrect!\n";
        $errorCount++;
    }
    $expected = 13;
    $tPosCount = count(array_unique($data['tPositions'], SORT_REGULAR));
    if ($tPosCount != $expected) {
        $errorMsg .= "Tests: ERROR, expected {$expected} got {$tPosCount}\n";
        $errorCount++;
    }
    if ($errorCount > 0) {
        echo "== TESTS PART 1 ==\n";
        echo $errorMsg;
        return false;
    }
    return true;
}

function testPartTwo()
{
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "09_input_test2.txt"));
    $data = calculatePositions($input, 10, false);
    $errorCount = 0;
    $errorMsg = "";
    $expected = 36;
    $tPosCount = count(array_unique($data['tPositions'], SORT_REGULAR));
    if ($tPosCount != $expected) {
        $errorMsg .= "Tests: ERROR, expected {$expected} got {$tPosCount}\n";
        $errorCount++;
    }
    if ($errorCount > 0) {
        echo "== TESTS PART 2 ==\n";
        echo $errorMsg;
        return false;
    }
    return true;
}

function tests(): bool
{
    if (!testPartOne()) {
        return false;
    }
    if (!testPartTwo()) {
        return false;
    }
    echo "Tests: OK!\n";
    return true;
}

function moveDiagonal(&$snake, $nodeIndex, $nodeBeforeChange, $axis)
{
    if ($nodeBeforeChange[$axis] < $snake[$nodeIndex-1][$axis]) {
        $snake[$nodeIndex][$axis]++;
    } elseif ($nodeBeforeChange[$axis] > $snake[$nodeIndex-1][$axis]) {
        $snake[$nodeIndex][$axis]--;
    }
}

function moveInRelationToParent($snake, $nodeIndex)
{
    $xDiff = abs($snake[$nodeIndex-1]['x'] - $snake[$nodeIndex]['x']);
    $yDiff = abs($snake[$nodeIndex-1]['y'] - $snake[$nodeIndex]['y']);
    $nodeBeforeChange = $snake[$nodeIndex];
    if ($xDiff > 1 && $nodeBeforeChange['x'] < $snake[$nodeIndex-1]['x']) {
        $snake[$nodeIndex]['x']++;
        moveDiagonal($snake, $nodeIndex, $nodeBeforeChange, 'y');
    } elseif ($xDiff > 1 && $nodeBeforeChange['x'] > $snake[$nodeIndex-1]['x']) {
        $snake[$nodeIndex]['x']--;
        moveDiagonal($snake, $nodeIndex, $nodeBeforeChange, 'y');
    } elseif ($yDiff > 1 && $nodeBeforeChange['y'] < $snake[$nodeIndex-1]['y']) {
        $snake[$nodeIndex]['y']++;
        moveDiagonal($snake, $nodeIndex, $nodeBeforeChange, 'x');
    } elseif ($yDiff > 1 && $nodeBeforeChange['y'] > $snake[$nodeIndex-1]['y']) {
        $snake[$nodeIndex]['y']--;
        moveDiagonal($snake, $nodeIndex, $nodeBeforeChange, 'x');
    }
    return $snake[$nodeIndex];
}

function calculatePositions(string $input, int $snakeLength)
{
    $snake = [];
    $positions = [];
    for ($i = 0; $i < $snakeLength; $i++) {
        $snake[$i] = ['x' => 0, 'y' => 0];
        $positions[$i] = [['x' => 0, 'y' => 0]];
    }
    $moves = explode("\n", $input);
    $c = count($moves);
    for ($i = 0; $i < $c; $i++) {
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
            $positions[0][] = $snake[0];
            for ($y = 1; $y < $snakeLength; $y++) {
                $childBefore = $snake[$y];
                $snake[$y] = moveInRelationToParent(
                    $snake,
                    $y
                );
                if ($childBefore != $snake[$y]) {
                    $positions[$y][] = $snake[$y];
                }
            }
        }
    }
    return [
        'hPos'          => $snake[0],
        'hPositions'    => $positions[0],
        'tPos'          => $snake[$snakeLength-1],
        'tPositions'    => $positions[$snakeLength-1],
    ];
}

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "09_input.txt"));

if (tests()) {
    $data = calculatePositions($input, 2);
    $c = count(array_unique($data['tPositions'], SORT_REGULAR)); // 5930
    echo "Part one: {$c}\n";

    $data = calculatePositions($input, 10);
    $c = count(array_unique($data['tPositions'], SORT_REGULAR)); // 2443
    echo "Part two: {$c}\n";
}
