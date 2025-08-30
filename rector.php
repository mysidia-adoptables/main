<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\ClassMethod\ReturnTypeWillChangeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationBasedOnParentClassMethodRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationBasedOnParentClassMethodRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/', // change to your code directory
    ]);

    $rectorConfig->skip([
        __DIR__ . '/vendor',
    ]);

    // 🔨 Upgrade all the way from PHP 5.6 → 8.2
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
    ]);


    // 🔨 Add real type declarations to match parent/interface
    $rectorConfig->rule(AddReturnTypeDeclarationBasedOnParentClassMethodRector::class);

};
