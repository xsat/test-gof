<?php

const MIN = 0;
const MAX = 15;

function find_top(int $i, int $j): array
{
    if (--$j < MIN) {
        $j = MAX;
    }

    return [$i, $j];
}

function find_bot(int $i, int $j): array
{
    if (++$j > MAX) {
        $j = MIN;
    }

    return [$i, $j];
}

function find_left(int $i, int $j): array
{
    if (--$i < MIN) {
        $i = MAX;
    }

    return [$i, $j];
}

function find_right(int $i, int $j): array
{
    if (++$i > MAX) {
        $i = MIN;
    }

    return [$i, $j];
}

function sum_active_cells(array $grid, int $i, int $j): int
{
    $active = 0;

    $top = find_top($i, $j);
    $active += $grid[$top[0]][$top[1]] ?? 0;
    $bot = find_bot($i, $j);
    $active += $grid[$bot[0]][$bot[1]] ?? 0;
    $left = find_left($i, $j);
    $active += $grid[$left[0]][$left[1]] ?? 0;
    $right = find_right($i, $j);
    $active += $grid[$right[0]][$right[1]] ?? 0;

    $top_left = find_left($top[0], $top[1]);
    $active += $grid[$top_left[0]][$top_left[1]] ?? 0;
    $top_right = find_right($top[0], $top[1]);
    $active += $grid[$top_right[0]][$top_right[1]] ?? 0;

    $bot_left = find_left($bot[0], $bot[1]);
    $active += $grid[$bot_left[0]][$bot_left[1]] ?? 0;
    $bot_right = find_right($bot[0], $bot[1]);
    $active += $grid[$bot_right[0]][$bot_right[1]] ?? 0;

    return $active;
}

function get_active_cells(int $i, int $j): array
{
    $top = find_top($i, $j);
    $bot = find_bot($i, $j);
    $left = find_left($i, $j);
    $right = find_right($i, $j);
    $top_left = find_left($top[0], $top[1]);
    $top_right = find_right($top[0], $top[1]);
    $bot_left = find_left($bot[0], $bot[1]);
    $bot_right = find_right($bot[0], $bot[1]);

    return [
        [$i, $j],
        $top,
        $bot,
        $left,
        $right,
        $top_left,
        $top_right,
        $bot_left,
        $bot_right,
    ];
}


function grid(string $raw): array {
    $grid = [];

    foreach (explode(',', $raw) as $item) {
        [$x, $y] = explode('-', $item);
        $grid[$x][$y] = true;
    }

    return $grid;
}

function simulate(array $grid): string
{
    $next = [];
    $checked = [];

    foreach ($grid as $i => $items) {
        foreach ($items as $j => $item) {
            foreach (get_active_cells($i, $j) as [$x, $y]) {
                if (isset($checked[$x][$y])) {
                    continue;
                }

                $checked[$x][$y] = true;

                $value = "$x-$y";
                if (!in_array($value, $next)) {
                    $active_cells = sum_active_cells($grid, $x, $y);
                    if (isset($grid[$x][$y])) {
                        if ($active_cells === 2 || $active_cells === 3) {
                            $next[] = $value;
                        }
                    } elseif ($active_cells === 3) {
                        $next[] = $value;
                    }
                }
            }
        }
    }

    return implode(',', $next);
}

function html(array $grid): void {
    echo '<table class="table table-bordered">';

    for ($i = MIN; $i <= MAX; $i++) {
        echo '<tr>';
        for ($j = MIN; $j <= MAX; $j++) {
            if (($grid[$j][$i] ?? 0)) {
                echo '<td class="bg-primary text-center text-white">' . $j . '-'  . $i . '</td>';
            } else {
                echo '<td class="text-center text-white">' . $j . '-'  . $i . '</td>';
            }
        }
        echo '</tr>';
    }

    echo '</table>';
}

$grid = grid($_REQUEST['c'] ?? '2-1,3-2,1-3,2-3,3-3');

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/jpeg" href="/icon.jpeg">
    <link href="/bootstrap.min.css" rel="stylesheet">

    <title>Game of Life</title>
</head>
<body class="bg-dark">
    <section class="container text-white">
        <h1>Game of Life</h1>
        <?php html($grid); ?>
        <hr>
        <a class="btn btn-success" href="/?c=<?= simulate($grid) ?>">Next</a>
        <a class="btn btn-danger" href="/">Reset</a>
    </section>
</body>
</html>
