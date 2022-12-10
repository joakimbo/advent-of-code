<?php

declare(strict_types=1);

$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt"));
$input = explode("\n", $input);

const SCORE = [
    'rock'      => 1,
    'paper'     => 2,
    'scissors'  => 3,
];
const RULES = [ // 'key' (beats) => 'value'
    'rock'      => 'scissors',
    'paper'     => 'rock',
    'scissors'  => 'paper',
];
const OUTCOMES = [
    'win' => 6,
    'draw' => 3,
    'loss' => 0,
];
const OPPONENT = [
    'A' => 'rock',
    'B' => 'paper',
    'C' => 'scissors',
];

function playRound(string $opponent, ?string $self = null, ?string $outcome = null)
{
    $score = 0;
    if ($outcome && in_array($outcome, array_keys(OUTCOMES)) && $opponent) {
        switch ($outcome) {
            case 'win':
                $score = SCORE[array_search($opponent, RULES)];
                break;
            case 'draw':
                $score = SCORE[$opponent];
                break;
            case 'loss':
                $score = SCORE[RULES[$opponent]];
                break;
        }
    } elseif ($self && $opponent) {
        $score += SCORE[$self];
        if (RULES[$self] == $opponent) {
            $outcome = 'win';
        } elseif ($self == $opponent) {
            $outcome = 'draw';
        } elseif (RULES[$opponent] == $self) {
            $outcome = 'loss';
        }
    }
    return $score + OUTCOMES[$outcome];
}

function partOne($input)
{
    $self = [
        'X' => 'rock',
        'Y' => 'paper',
        'Z' => 'scissors',
    ];
    $totScore = 0;
    foreach ($input as &$round) {
        $round = explode(' ', $round);
        $totScore += playRound(OPPONENT[$round[0]], $self[$round[1]]);
    }
    unset($round);
    return $totScore;
}
echo "Part one: " . partOne($input) . "\n"; // 13268

function partTwo($input)
{
    $outcome = [
        'X' => 'loss',
        'Y' => 'draw',
        'Z' => 'win',
    ];
    $totScore = 0;
    foreach ($input as &$round) {
        $round = explode(' ', $round);
        $totScore += playRound(OPPONENT[$round[0]], null, $outcome[$round[1]]);
    }
    unset($round);
    return $totScore;
}
echo "Part two: " . partTwo($input) . "\n"; // 15508 */
