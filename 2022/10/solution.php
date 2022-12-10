<?php

declare(strict_types=1);

function testPartOne(): bool
{
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input_test01.txt"));
    $errorMsg = "";
    $result = runInstructions($input);
    if ($result['register'] !== -1) {
        $errorMsg .= "Error, register '{$result['register']}' not equal to expected '-1'";
    }
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input_test02.txt"));
    $result = runInstructions($input);
    $expectedSignalStrength = [
        '20'  => 420,
        '60'  => 1140,
        '100' => 1800,
        '140' => 2940,
        '180' => 2880,
        '220' => 3960
    ];
    if ($result['signalStrength'] !== $expectedSignalStrength) {
        $errorMsg .= "Signalstrength does not match expected values!";
    }
    if ($errorMsg) {
        echo "== TESTS PART 1 ==\n";
        echo $errorMsg;
        return false;
    }
    return true;
}

function testPartTwo(): bool
{
    $errorMsg = "";
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input_test02.txt"));
    $result = runInstructions($input);
    $expectedScreen = "##..##..##..##..##..##..##..##..##..##.."
    . "###...###...###...###...###...###...###."
    . "####....####....####....####....####...."
    . "#####.....#####.....#####.....#####....."
    . "######......######......######......####"
    . "#######.......#######.......#######.....";
    if ($result['screen'] != $expectedScreen) {
        $errorMsg .= "Screen does not match expected screen!\n";
    }
    if ($errorMsg) {
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

function addSignalStrength(int $cycle, int &$lastCycle, int $register, array &$signalStrength)
{
    if ($cycle == 20 || $cycle === $lastCycle+40) {
        $lastCycle = $cycle;
        $signalStrength[$cycle] = $cycle * $register;
    }
}

function lightScreenPixel($cycle, $pos, &$screen)
{
    if ($screen) {
        $cyclePos = $cycle -1;
        $offset = floor($cyclePos/40) * 40;
        $pos += $offset;
        if (in_array($cyclePos, range($pos-1, $pos+1))) {
            $screen[$cyclePos] = '#';
        }
    }
}

function drawScreen(string $screen)
{
    if ($screen) {
        for ($i = 0; $i < 6; $i++) {
            echo substr($screen, $i*40, 40) . "\n";
        }
    }
}

function runInstructions(string $input)
{
    $lines = explode("\n", $input);
    $x = 1; // register
    $cycle = 0;
    $lastCycle = 0;
    $screen = str_repeat('.', 240);

    $signalStrength = [];

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        if (substr($line, 0, 4) == 'noop') {
            $cycle++;
            lightScreenPixel($cycle, $x, $screen);
            addSignalStrength($cycle, $lastCycle, $x, $signalStrength);
            // after cycle
        } elseif (substr($line, 0, 4) == 'addx') {
            $cycle++;
            // during cycle
            lightScreenPixel($cycle, $x, $screen);
            addSignalStrength($cycle, $lastCycle, $x, $signalStrength);
            $cycle++;
            lightScreenPixel($cycle, $x, $screen);
            addSignalStrength($cycle, $lastCycle, $x, $signalStrength);
            // after cycle
            $x += (int)trim(substr($line, 4));
        }
    }

    return [
        'register'          => $x,
        'signalStrength'    => $signalStrength,
        'screen'            => $screen,
    ];
}

function partOne(string $input)
{
    $result = runInstructions($input);
    $sum = 0;
    foreach ($result['signalStrength'] as $cycle => $value) {
        if (in_array($cycle, ['20', '60', '100', '140', '180', '220'])) {
            $sum += $value;
        }
    }
    return $sum;
}

function partTwo(string $input)
{
    $result = runInstructions($input);
    drawScreen($result['screen']);
}

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));
if (tests()) {
    // part one code
    $result01 = partOne($input);
    echo "Part one: {$result01}\n"; // 12980

    // part two code
    echo "Part two:\n";
    partTwo($input); // BRJLFULP
}
