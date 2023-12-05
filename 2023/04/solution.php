<?php

declare(strict_types=1);

function tests()
{
    $input = <<<INPUT
    Card 1: 41 48 83 86 17 | 83 86  6 31 17  9 48 53
    Card 2: 13 32 20 16 61 | 61 30 68 82 17 32 24 19
    Card 3:  1 21 53 59 44 | 69 82 63 72 16 21 14  1
    Card 4: 41 92 73 84 69 | 59 84 76 51 58  5 54 83
    Card 5: 87 83 26 28 32 | 88 30 70 12 93 22 82 36
    Card 6: 31 18 13 56 72 | 74 77 10 23 35 67 36 11
    INPUT;

    if (solvePartOne($input) === 13) {
        echo "TEST ONE OK\n";
    } else {
        echo "TEST ONE FAIL\n";
    }

    if (solve($input) === 30) {
        echo "TEST TWO OK\n";
    } else {
        echo "TEST TWO FAIL\n";
    }
}

function solvePartOne(string $input)
{
    $tickets = explode("\n", $input);
    foreach ($tickets as &$row) {
        preg_match('/Card\s+(\d+):(.*)\|(.*)/', $row, $matches);
        $indexes = [2, 3];
        foreach ($indexes as $index) {
            $matches[$index] = array_filter(array_map(function ($element) {
                return (int)trim($element);
            }, explode(' ', $matches[$index])), function ($element) {
                return $element ? true : false;
            });
        }
        $row = [
            'cardIndex'        => (int)$matches[1],
            'winningNumbers'    => $matches[2],
            'cardNumbers'       => $matches[3],
            'points'            => 0,
        ];
        $intersection = array_values(array_intersect($row['winningNumbers'], $row['cardNumbers']));
        if (!empty($intersection)) {
            for ($i = 0; $i < count($intersection); $i++) {
                if ($i == 0) {
                    $row['points'] = 1;
                } else {
                    $row['points'] *= 2;
                }
            }
        }
    }
    unset($row);
    return array_sum(array_column($tickets, 'points'));
}

function calcNewTicketsPerRow(string $chunk, array $originalTickets)
{
    $newTickets = [];
    $tickets = explode("\n", $chunk);
    $i = 0;
    foreach ($tickets as $row) {
        preg_match('/Card\s+(\d+):(.*)\|(.*)/', $row, $matches);
        $indexes = [2, 3];
        foreach ($indexes as $index) {
            $matches[$index] = array_filter(array_map(function ($element) {
                return (int)trim($element);
            }, explode(' ', $matches[$index])), function ($element) {
                return $element ? true : false;
            });
        }
        $tempTicket = [
            'cardIndex'         => (int)$matches[1],
            'winningNumbers'    => $matches[2],
            'cardNumbers'       => $matches[3],
        ];
        $intersection = array_values(array_intersect($tempTicket['winningNumbers'], $tempTicket['cardNumbers']));
        if (!empty($intersection)) {
            for ($y = 0; $y < count($intersection); $y++) {
                if (isset($originalTickets[$tempTicket['cardIndex'] + $y])) {
                    if (!isset($newTickets[$tempTicket['cardIndex']])) {
                        $newTickets[$tempTicket['cardIndex']] = 1;
                    } else {
                        $newTickets[$tempTicket['cardIndex']]++;
                    }
                }
            }
        } else {
            $newTickets[$tempTicket['cardIndex']] = 0;
        }
        $i++;
    }
    return $newTickets;
}

function calcTickets($ticketsPerCards, &$sum, $originalNewTickets)
{
    $sum += count($ticketsPerCards);
    foreach ($ticketsPerCards as $cardIndex => $ticketsPerCards) {
        $a = [];
        for ($i = 1; $i <= $ticketsPerCards; $i++) {
            if (isset($originalNewTickets[$cardIndex + $i])) {
                $a[$cardIndex + $i] = $originalNewTickets[$cardIndex + $i];
            }
        }
        calcTickets($a, $sum, $originalNewTickets);
    }
    return $sum;
}

function solve(string $input)
{
    $originalTickets = explode("\n", $input);
    $originalNewTickets = calcNewTicketsPerRow($input, $originalTickets);
    $chunks = [$originalNewTickets];

    $sum = 0;
    calcTickets($originalNewTickets, $sum, $originalNewTickets);
    return $sum;
}


tests();

$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
$solution = solvePartOne($input1);
echo "PART ONE: {$solution}\n";

$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
$solution = solve($input1);
echo "PART TWO: {$solution}\n";
