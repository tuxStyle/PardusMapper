<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\Config\RectorConfig;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Set\ValueObject\LevelSetList;

// require_once 'rector/LongArrayToShortArrayNewLinedRector.php';

return static function (RectorConfig $rectorConfig): void {

    // $rectorConfig->bootstrapFiles([
    //     __DIR__ . '/pardusmap.mhwva.net/globals.php',
    // ]);

    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/Download',
        __DIR__ . '/include',
        __DIR__ . '/info',
        __DIR__ . '/templates',
        __DIR__ . '/unused',
    ]);

    // $rectorConfig->rule(LongArrayToShortArrayNewLinedRector::class);

    // define sets of rules
       $rectorConfig->sets([
           LevelSetList::UP_TO_PHP_83,
       ]);

    //    $rectorConfig->skip([
    //     ClassPropertyAssignToConstructorPromotionRector::class,
    //     LongArrayToShortArrayRector::class,
    //     ReadOnlyPropertyRector::class,
    //     StrictArraySearchRector::class,
    //     JsonThrowOnErrorRector::class,
    // ]);
};
