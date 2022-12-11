<?php

declare(strict_types=1);

function testPartOne(): bool
{
    $input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input_test01.txt"));
    $errorMsg = "";

    $numberOfRounds = 20;
    $rounds = [];
    $prevRound = parseInput($input);
    for ($i = 0; $i < $numberOfRounds; $i++) {
        $rounds[$i] = playRound($prevRound, 1);
        $prevRound = $rounds[$i];
    }
    $monkeyBusiness = calculateMonkeyBusiness($rounds[$numberOfRounds-1]);
    $expected = '10605';
    if ($monkeyBusiness != $expected) {
        $errorMsg .= "expected: '{$expected}' monkey business\n";
        $errorMsg .= "received: '{$monkeyBusiness} monkey business'\n";
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
    $numberOfRounds = 10000;
    $result = parseInput($input);
    for ($i = 0; $i < $numberOfRounds; $i++) {
        $result = playRound($result, 2);
    }
    $monkeyBusiness = calculateMonkeyBusiness($result);
    $expected = '2713310158';
    if ($monkeyBusiness != $expected) {
        $errorMsg .= "expected: '{$expected}' monkey business\n";
        $errorMsg .= "received: '{$monkeyBusiness}' monkey business\n";
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

function parseInput(string $input)
{
    $lines = explode("\n", $input);
    $monkeys = [];
    for ($i = 0; $i < count($lines); $i++) {
        if (!trim($lines[$i])) {
            continue;
        }
        preg_match('/^Monkey (\d+):/', $lines[$i], $matches);
        if (isset($matches[1])) {
            $monkeyIndex = $matches[1];
            $monkeys[$monkeyIndex] = [];
            preg_match('/Starting items: (.*)/', $lines[$i+1], $matches);
            $monkeys[$monkeyIndex]['items'] = explode(', ', $matches[1]);
            preg_match('/Operation: new = (.*)/', $lines[$i+2], $matches);
            $monkeys[$monkeyIndex]['operation'] = $matches[1];
            preg_match('/Test: (.*)/', $lines[$i+3], $matches);
            $monkeys[$monkeyIndex]['test'] = $matches[1];
            preg_match('/If true: (.*)/', $lines[$i+4], $matches);
            $monkeys[$monkeyIndex]['testIfTrue'] = $matches[1];
            preg_match('/If false: (.*)/', $lines[$i+5], $matches);
            $monkeys[$monkeyIndex]['testIfFalse'] = $matches[1];
            $monkeys[$monkeyIndex]['inspectCount'] = 0;
        }
    }
    return $monkeys;
}

function playTurn(array &$monkey, array &$monkeys, int $part, $modFactor)
{
    if (empty($monkey['items'])) {
        return;
    }
    $c = count($monkey['items']);
    for ($i = 0; $i < $c; $i++) {
        $monkey['inspectCount']++;
        $item = array_shift($monkey['items']);
        // inspect item
        $operation = str_replace('old', $item, $monkey['operation']);
        preg_match('/(\d+) (\+|-|\*|\/) (\d+)/', $operation, $matches);
        switch ($matches[2]) {
            case '+':
                $item = bcadd($matches[1], $matches[3]);
                break;
            case '-':
                $item = bcsub($matches[1], $matches[3]);
                break;
            case '*':
                $item = bcmul($matches[1], $matches[3]);
                break;
            case '/':
                $item = bcdiv($matches[1], $matches[3]);
                break;
        }

        if ($part == 2) {
            $item = bcmod($item, $modFactor);
        }

        if ($part == 1) {
            $item = bcadd(bcdiv((string)$item, '3'), '0');
        }

        preg_match('/divisible by (\d+)/', $monkey['test'], $matches);
        if (bcmod($item, $matches[1]) === '0') {
            $throwToMonkeyIndex = substr($monkey['testIfTrue'], strlen('throw to monkey '));
        } else {
            $throwToMonkeyIndex = substr($monkey['testIfFalse'], strlen('throw to monkey '));
        }
        $monkeys[(int)$throwToMonkeyIndex]['items'][] = $item;
    }
}

function playRounds(string $input, int $numberOfRounds, int $part = 1)
{
    $result = parseInput($input);
    for ($i = 0; $i < $numberOfRounds; $i++) {
        $result = playRound($result, $part);
    }
    return $result;
}

function playRound(array $monkeys, int $part)
{
    $modFactor = '1';
    for ($i = 0; $i < count($monkeys); $i++) {
        preg_match('/divisible by (\d+)/', $monkeys[$i]['test'], $matches);
        $modFactor = bcmul($modFactor, $matches[1]);
    }
    for ($i = 0; $i < count($monkeys); $i++) {
        playTurn($monkeys[$i], $monkeys, $part, $modFactor);
    }
    return $monkeys;
}

function calculateMonkeyBusiness(array $lastRound)
{
    $monkeyActivity = [];
    foreach ($lastRound as $index => $monkey) {
        $monkeyActivity[$index] = $monkey['inspectCount'];
    }
    rsort($monkeyActivity);
    return bcmul((string)$monkeyActivity[0], (string)$monkeyActivity[1]);
}

function partOne(string $input)
{
    return calculateMonkeyBusiness(playRounds($input, 20));
}

function partTwo(string $input)
{
    return calculateMonkeyBusiness(playRounds($input, 10000, 2));
}

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));
if (tests()) {
    // part one code
    $result01 = partOne($input);
    echo "Part one: {$result01}\n"; // 78678

    // part two code
    $result02 = partTwo($input);
    echo "Couldn't get part two correct without looking up the solution :(\n";
    echo "Part two: {$result02}\n"; // 15333249714
}
