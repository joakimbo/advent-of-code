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
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input_test01.txt"));
    $errorMsg = "";
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

function runInstructions(string $input, array $crt = [])
{
    $lines = explode("\n", $input);
    $x = 1; // register
    $cycle = 0;
    $lastCycle = 0;

    $signalStrength = [];

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        if (substr($line, 0, 4) == 'noop') {
            $cycle++;
            addSignalStrength($cycle, $lastCycle, $x, $signalStrength);
            // after cycle
        } elseif (substr($line, 0, 4) == 'addx') {
            $cycle++;
            // during cycle
            addSignalStrength($cycle, $lastCycle, $x, $signalStrength);
            $cycle++;
            addSignalStrength($cycle, $lastCycle, $x, $signalStrength);
            // after cycle
            $x += (int)trim(substr($line, 4));
        }
    }

    return [
        'register'          => $x,
        'signalStrength'    => $signalStrength,
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

function createScreen()
{
    $crt = [];
    for ($i = 0; $i < 6; $i++) {
        $row = [];
        for ($y = 0;$y < 40; $y++) {
            $row[] = '.';
        }
        $crt[] = $row;
    }
    return $crt;
}

function drawScreen(array $screen)
{
    foreach ($screen as $row) {
        foreach ($row as $col) {
            echo $col;
        }
        echo "\n";
    }
}

function partTwo(string $input)
{
    $crt = createScreen();
    drawScreen($crt);
}


$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));

if (tests()) {
    // part one code
    $result01 = partOne($input);
    echo "Part one: {$result01}\n";

    // part two code
    echo "Part two\n";
    partTwo($input);
}
