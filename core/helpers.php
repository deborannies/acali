<?php

if (!function_exists('dd')) {
    function dd(mixed ...$vars): void
    {
        dump(...$vars);

        die(1);
    }
}
