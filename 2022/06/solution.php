<?php

declare(strict_types=1);

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));

function tests()
{
    $inputs = [
        'partOne' => [
            'length' => 4,
            'inputs' => [
                'mjqjpqmgbljsphdztnvjfqwrcgsmlb'    => 7,
                'bvwbjplbgvbhsrlpgdmjqwftvncz'      => 5,
                'nppdvjthqldpwncqszvftbrmjlhg'      => 6,
                'nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg' => 10,
                'zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw'  => 11,
            ],
        ],
        'partTwo' => [
            'length' => 14,
            'inputs' => [
                'mjqjpqmgbljsphdztnvjfqwrcgsmlb'    => 19,
                'bvwbjplbgvbhsrlpgdmjqwftvncz'      => 23,
                'nppdvjthqldpwncqszvftbrmjlhg'      => 23,
                'nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg' => 29,
                'zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw'  => 26,
            ]
        ],
    ];
    foreach ($inputs as $part) {
        foreach ($part['inputs'] as $testInput => $expectedValue) {
            $result = getCharCountBeforeStartPos($testInput, $part['length']);
            if ($result != $expectedValue) {
                echo "Error, expected {$expectedValue} got {$result}\n";
            }
        }
    }
}

function getCharCountBeforeStartPos(string $input, $length = 4): int
{
    $inputLength = strlen($input);
    for ($i = 0; $i < $inputLength; $i++) {
        $temp = substr($input, $i, $length);
        if (count(array_unique(str_split($temp))) == $length) {
            return $i+$length;
        }
    }
    return -1;
}

tests();

echo "Part one: " . getCharCountBeforeStartPos($input) . "\n"; // 1582
echo "Part two: " . getCharCountBeforeStartPos($input, 14) . "\n"; // 3588
