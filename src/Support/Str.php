<?php

declare(strict_types=1);

namespace Juling\Support;

use think\helper\Str as BaseStr;

class Str extends BaseStr
{
    public static function replace(array|string $search, array|string $replace, array|string $subject): array|string
    {
        return str_replace($search, $replace, $subject);
    }
}
