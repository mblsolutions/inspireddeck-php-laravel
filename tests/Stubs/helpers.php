<?php

// Stand-in for the helper that apps get from inspireddeck-support
if (!function_exists('route_contains')) {
    function route_contains(string $name)
    {
        return false;
    }
}
