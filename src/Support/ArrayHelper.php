<?php

declare(strict_types=1);

namespace Juling\Foundation\Support;

use ReflectionClass;
use ReflectionProperty;
use think\Collection;

trait ArrayHelper
{
    /**
     * 将数组批量赋值到对象属性
     */
    public function loadData(array $row): void
    {
        foreach ($row as $col => $val) {
            if (! is_null($val)) {
                $setMethod = 'set'.parse_name($col, 1);
                if (method_exists($this, $setMethod)) {
                    $this->$setMethod($val);
                }
            }
        }
    }

    /**
     * 将对象转换到数组
     */
    public function toArray(bool $allProperty = false): array
    {
        return $allProperty ? $this->allProperty() : $this->effectiveProperty();
    }

    /**
     * 获取数据表数据
     */
    public function toEntity(bool $allProperty = false): array
    {
        $data = [];
        foreach ($this->toArray($allProperty) as $key => $val) {
            $data[Str::snake($key)] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
        }

        return $data;
    }

    /**
     * 获取JSON数据
     */
    public function toJson(bool $allProperty = false): string
    {
        return json_encode($this->toArray($allProperty), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取Collection
     */
    public function collect(bool $allProperty = false): Collection
    {
        return new Collection($this->toArray($allProperty));
    }

    public function has(string $property): bool
    {
        return property_exists($this, $property);
    }

    /**
     * 返回对象的全部属性
     */
    protected function getProperties(): array
    {
        $reflect = new ReflectionClass(__CLASS__);

        return $reflect->getProperties(ReflectionProperty::IS_PRIVATE);
    }

    /**
     * 对象的全部属性赋值
     */
    private function allProperty(): array
    {
        $props = $this->getProperties();

        $property = [];
        foreach ($props as $prop) {
            $property[] = $prop->getName();
        }

        $data = [];
        foreach ($property as $p) {
            $data[$p] = $this->$p ?? '';
        }

        return $data;
    }

    /**
     * 返回赋值的对象属性
     */
    private function effectiveProperty(): array
    {
        $data = [];
        foreach ($this as $k => $v) {
            $data[$k] = $v;
        }

        return $data;
    }
}
