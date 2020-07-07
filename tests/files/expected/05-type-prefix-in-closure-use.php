<?php

function test(string $value): callable
{
    return static function () use ($value): string {
        return $value;
    };
}
