<?php

function test(string $strValue): callable
{
    return static function () use ($strValue): string {
        return $strValue;
    };
}
