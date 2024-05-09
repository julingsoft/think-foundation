<?php

declare(strict_types=1);

namespace Juling\Foundation\Repository;

use Juling\Foundation\Contract\CurdRepositoryInterface;
use Exception;
use think\Collection;
use think\Model;

/**
 * @method Model model() 定义数据数据模型类
 */
abstract class CurdRepository implements CurdRepositoryInterface
{
    /**
     * 添加业务数据
     */
    public function create(array $data): mixed
    {
        $model = $this->model()->create($data);

        $pk = $this->getPrimaryKey();

        return $model->$pk;
    }

    /**
     * 批量增加数据
     *
     * @throws Exception
     */
    public function saveAll(array $data, bool $replace = true): Collection
    {
        return $this->model()->saveAll($data, $replace);
    }

    /**
     * 按照ID查询接单渠道
     */
    public function findOneById(mixed $id): array
    {
        $pk = $this->getPrimaryKey();

        return $this->findOneByWhere([$pk => $id]);
    }

    /**
     * 按照条件查询
     */
    public function findOneByWhere(array $condition = [], string $order = '', string $sort = 'desc'): array
    {
        $order = empty($order) ? $this->getPrimaryKey() : $order;

        $result = $this->model()->where($condition)->order($order, $sort)->findOrEmpty();
        if ($result->isEmpty()) {
            return [];
        }

        return $result->toArray();
    }

    /**
     * 查询某个字段的值
     */
    public function value(string $field, array $condition = []): mixed
    {
        return $this->model()->where($condition)->value($field);
    }

    /**
     * 查询某一列的值
     */
    public function column(string $field, array $condition = []): array
    {
        return $this->model()->where($condition)->column($field);
    }

    /**
     * 按条件统计数量
     *
     * @throws Exception
     */
    public function count(array $condition = []): int
    {
        return $this->model()->where($condition)->count();
    }

    /**
     * 查询全部
     *
     * @throws Exception
     */
    public function findAll(array $condition = [], string $order = '', string $sort = 'desc'): array
    {
        $order = empty($order) ? $this->getPrimaryKey() : $order;

        $result = $this->model()->where($condition)->order($order, $sort)->select();
        if ($result->isEmpty()) {
            return [];
        }

        return $result->toArray();
    }

    /**
     * 分页查询
     *
     * @throws Exception
     */
    public function paginate(array $condition = [], int $page = 1, int $pageSize = 20, string $order = '', string $sort = 'desc'): array
    {
        $order = empty($order) ? $this->getPrimaryKey() : $order;

        $result = $this->model()->where($condition)->order($order, $sort)->paginate([
            'page' => $page,
            'list_rows' => $pageSize,
        ]);

        return $result->toArray();
    }

    /**
     * 按ID更新数据
     */
    public function updateById(array $data, mixed $id): bool
    {
        $pk = $this->getPrimaryKey();

        return $this->update($data, [$pk => $id]);
    }

    /**
     * 更新数据
     */
    public function update(array $data, array $condition): bool
    {
        if (empty($condition)) {
            return false;
        }

        // 返回更新的模型对象，包括自动同步更新时间
        $result = $this->model()->update($data, $condition);

        return ! $result->isEmpty();
    }

    /**
     * 按ID删除数据
     */
    public function deleteById(mixed $id): bool
    {
        $pk = $this->getPrimaryKey();

        return $this->delete([$pk => $id]);
    }

    /**
     * 强制删除数据
     */
    public function delete(array $condition): bool
    {
        return $this->model()->destroy(function ($query) use ($condition) {
            $query->where($condition);
        }, true);
    }

    /**
     * 按ID软删除数据
     */
    public function softDeleteById(mixed $id): bool
    {
        $pk = $this->getPrimaryKey();

        return $this->softDeleteByWhere([$pk => $id]);
    }

    /**
     * 按条件软删除数据
     */
    public function softDeleteByWhere(array $condition): bool
    {
        if (empty($condition)) {
            return false;
        }

        return $this->model()->destroy(function ($query) use ($condition) {
            $query->where($condition);
        });
    }

    /**
     * 获取主键名
     */
    public function getPrimaryKey(): string
    {
        return $this->model()->getPk();
    }
}
