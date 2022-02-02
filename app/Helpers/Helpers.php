<?php

if (!function_exists('response()->json')) {
    function tttt(object $payload, int $status_code = 200, $stacktrace = null)
    {
        if (config('app.debug')) {
            $payload->stacktrace = $stacktrace;
        }
        return response()->json((array)$payload, $status_code);
    }
}
