<?php
#author’s name： Yew Kai Quan
namespace App\Support;

use Illuminate\Support\Str;

class SafeRedirect
{
    public static function sanitize(?string $target, string $fallbackRoute = 'kfc.locations'): string
    {
        if (!$target || !is_string($target)) {
            return route($fallbackRoute);
        }

        $target = trim(str_replace(["\r","\n"], '', $target));
        $target = filter_var($target, FILTER_SANITIZE_URL) ?: '';

        if ($target === '') {
            return route($fallbackRoute);
        }

        if (\Illuminate\Support\Str::startsWith($target, ['/'])) {
            return $target;
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $host    = parse_url($target, PHP_URL_HOST);
        $scheme  = parse_url($target, PHP_URL_SCHEME);

        $whitelist = [$appHost];

        if ($host && in_array($host, $whitelist, true) && in_array($scheme, ['http','https'], true)) {
            return $target;
        }

        return route($fallbackRoute);
    }
}
