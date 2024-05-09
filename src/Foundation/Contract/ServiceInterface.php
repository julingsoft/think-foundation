<?php

declare(strict_types=1);

namespace Juling\Foundation\Contract;

interface ServiceInterface
{
    public function getRepository(): CurdRepositoryInterface;
}
