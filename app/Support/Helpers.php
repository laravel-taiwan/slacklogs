<?php

namespace App\Support;

use App\Domain\Channel;
use App\Domain\User;

class Helpers
{
    public static function parseText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        return (string) preg_replace_callback('/<([^>]+)>/', static function (array $match): string {
            [$target, $label] = array_pad(explode('|', $match[1], 2), 2, null);

            if (str_starts_with($target, '@')) {
                $name = User::query()->where('sid', substr($target, 1))->value('name');

                return '@'.($name ?: $label ?: substr($target, 1));
            }

            if (str_starts_with($target, '#')) {
                $name = Channel::query()->where('sid', substr($target, 1))->value('name');

                return '#'.($name ?: $label ?: substr($target, 1));
            }

            if (filter_var($target, FILTER_VALIDATE_URL)) {
                return $target;
            }

            return $label ?: $target;
        }, $text);
    }
}
