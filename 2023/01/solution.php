<?php

declare(strict_types=1);

function solvePartOne($input)
{
    $digits = preg_replace('/[a-zA-Z]+/', '', $input);
    $digits = explode("\n", $digits);
    $sum = 0;
    foreach ($digits as $row) {
        if ($row) {
            $sum += (int)($row[0] . $row[strlen($row) - 1]);
        }
    }
    return $sum;
}

function solvePartTwo($input)
{
    $replacements = [
        'one'   => '1',
        'two'   => '2',
        'three' => '3',
        'four'  => '4',
        'five'  => '5',
        'six'   => '6',
        'seven' => '7',
        'eight' => '8',
        'nine'  => '9',
    ];
    $digits = explode("\n", $input);
    $sum = 0;
    foreach ($digits as $row) {
        for ($i = 0; $i < strlen($row); $i++) { // forwards
            foreach ($replacements as $valAsString => $val) {
                if (substr($row, $i, strlen($valAsString)) == $valAsString) {
                    $row = substr($row, 0, $i) . $val . substr($row, $i + 1);
                    break 2;
                }
            }
        }

        for ($i = strlen($row); $i >= 0; $i--) { // backwards
            foreach ($replacements as $valAsString => $val) {
                if (substr($row, $i, strlen($valAsString)) == $valAsString) {
                    $row = substr($row, 0, $i) . $val . substr($row, $i + 1);
                    break 2;
                }
            }
        }
        $row = preg_replace('/[a-zA-Z]+/', '', $row);
        if ($row) {
            $sum += (int)($row[0] . $row[strlen($row) - 1]);
        }
    }

    return $sum;
}

function tests()
{
    $input = "1abc2\npqr3stu8vwx\na1b2c3d4e5f\ntreb7uchet";
    if (solvePartOne($input) === 142) {
        echo "TEST ONE OK!\n";
    } else {
        echo "TEST ONE FAIL\n";
    }

    $input = "two1nine\neightwothree\nabcone2threexyz\nxtwone3four\n4nineeightseven2\nzoneight234\n7pqrstsixteen";
    $solution = solvePartTwo($input);
    if ($solution === 281) {
        echo "TEST TWO OK!\n";
    } else {
        echo "TEST TWO FAIL {$solution}\n";
    }
}

tests();

$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
echo 'Part 1:' . solvePartOne($input1) . "\n"; // 54450

echo 'Part 2:' . solvePartTwo($input1) . "\n"; // 54265
