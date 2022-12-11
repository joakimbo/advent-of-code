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
    $expectedResults = [
        0 => [
            ['20', '23', '27', '26'],
            ['2080', '25' ,'167' ,'207', '401', '1046'],
            [],
            [],
        ],
        19 => [
            ['10', '12', '14', '26', '34'],
            ['245', '93' ,'53' ,'199', '115'],
            [],
            [],
        ],
    ];
    foreach ($expectedResults as $roundIndex => $expectedResult) {
        $result = $rounds[$roundIndex];
        foreach ($expectedResult as $monkeyIndex => $values) {
            if ($result[$monkeyIndex]['items'] !== $values) {
                $errorMsg .= "Error, expected items not matching!\n";
                break 2;
            }
        }
    }
    $monkeyBusiness = calculateMonkeyBusiness($rounds[$numberOfRounds-1]);
    if ($monkeyBusiness < 10605) {
        $errorMsg .= "Error, not enough monkeybusiness\n";
    } elseif ($monkeyBusiness > 10605) {
        $errorMsg .= "Error, too much monkeybusiness!\n";
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
    echo "memory used: " . memory_get_usage(true) . "\n";
    $result = parseInput($input);
    for ($i = 0; $i < $numberOfRounds; $i++) {
        $result = playRound($result, 2);
    }
    if(calculateMonkeyBusiness($result) !== 2713310158) {
        $errorMsg .= "Error, amount of monkey business incorrect!\n";
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
    /* if (!testPartTwo()) {
        return false;
    } */
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

function bcround(string $value)
{
    $decimalPos = strpos($value, '.');
    if (!$decimalPos) {
        return $value;
    }
    $decimals = strlen(substr($value, $decimalPos+1));
    $add = '0.' . str_repeat('0', $decimals - 1) . '5';
    if (bcadd($value, $add) > substr($value, 0, $decimals * -1)) {
        $value = bcadd($value, $add);
    } else {
        $value = bcadd($value, '0');
    }
    return $value;
}

function inspectItem(string $item, string $operation): string
{
    $search = [
        'old'
    ];
    $replace = [
        $item,
    ];
    $operation = str_replace($search, $replace, $operation);
    preg_match('/(\d+) (\+|-|\*|\/) (\d+)/', $operation, $matches);
    switch ($matches[2]) {
        case '+':
            $item = bcadd($matches[1], $matches[3]);
            break;
        case '-':
            $item = bcsub($matches[1], $matches[3]);
            break;
        case '*':
            $item = bcround(bcmul($matches[1], $matches[3]));
            break;
        case '/':
            $item = bcround(bcdiv($matches[1], $matches[3]));
            break;
    }
    return $item;
}

function postItemInspectionRelief(string $item): string
{
    return bcadd(bcdiv($item, '3'), '0');
}

function itemTest(string $item, $matches): bool
{
    switch ($matches[1]) {
        case 'divisible':
            return bcmod($item, $matches[2]) === '0';
            break;
    }
}

function playTurn(array &$monkey, array &$monkeys, int $part)
{
    if (empty($monkey['items'])) {
        return;
    }
    $c = count($monkey['items']);
    for ($i = 0; $i < $c; $i++) {
        $item = array_shift($monkey['items']);
        preg_match('/(\w+) .* (\d+)/', $monkey['test'], $matches);
        $item = inspectItem($item, $monkey['operation'], $matches);
        $monkey['inspectCount']++;
        if ($part == 1) {
            $item = postItemInspectionRelief($item);
        }
        if (itemTest($item, $matches)) {
            $throwToMonkeyIndex = substr($monkey['testIfTrue'], strlen('throw to monkey '));
        } else {
            $throwToMonkeyIndex = substr($monkey['testIfFalse'], strlen('throw to monkey '));
        }
        $monkeys[(int)$throwToMonkeyIndex]['items'][] = $item;
    }
}

function playRound(array $monkeys, int $part)
{
    for ($i = 0; $i < count($monkeys); $i++) {
        playTurn($monkeys[$i], $monkeys, $part);
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
    return $monkeyActivity[0] * $monkeyActivity[1];
}

function partOne(string $input)
{
    $numberOfRounds = 20;
    $rounds = [];
    $prevRound = parseInput($input);
    for ($i = 1; $i <= $numberOfRounds; $i++) {
        $rounds[$i] = playRound($prevRound, 1);
        $prevRound = $rounds[$i];
    }
    return calculateMonkeyBusiness($rounds[20]);
}

function partTwo(string $input)
{
    $numberOfRounds = 10000;
    $rounds = [];
    $prevRound = parseInput($input);
    for ($i = 1; $i <= $numberOfRounds; $i++) {
        $rounds[$i] = playRound($prevRound, 2);
        $prevRound = $rounds[$i];
    }
    return calculateMonkeyBusiness($rounds[20]);
}

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));
if (tests()) {
    // part one code
    $result01 = partOne($input);
    echo "Part one: {$result01}\n"; // 78678

    // part two code
    /* echo "Part two:\n";
    partTwo($input); */
}
