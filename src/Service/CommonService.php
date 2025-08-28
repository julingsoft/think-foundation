<?php

declare(strict_types=1);

namespace Juling\Foundation\Service;

use Juling\Foundation\Contract\CommonServiceInterface;
use Juling\Foundation\Contract\CurdRepositoryInterface;
use Juling\Foundation\Exception\CustomException;

/**
 * @method CurdRepositoryInterface getRepository()
 */
abstract class CommonService implements CommonServiceInterface
{
    public function insertGetId(array $entity): mixed
    {
        return $this->getRepository()->create($entity);
    }

    public function save(array $entity): bool
    {
        $insertId = $this->insertGetId($entity);

        return $insertId > 0;
    }

    public function saveBatch(array $list, int $batchSize = self::DEFAULT_BATCH_SIZE): bool
    {
        $listGroup = array_chunk($list, $batchSize);

        $collections = [];
        foreach ($listGroup as $data) {
            $collections[] = $this->getRepository()->saveAll($data);
        }

        return ! empty($collections);
    }

    /**
     * @throws CustomException
     */
    public function saveOrUpdateBatch(array $list, int $batchSize = self::DEFAULT_BATCH_SIZE): bool
    {
        throw new CustomException('TODO: Implement saveOrUpdateBatch() method.');
    }

    public function removeById(mixed $id): bool
    {
        return $this->getRepository()->softDeleteById($id);
    }

    public function remove(array $condition): bool
    {
        if (empty($condition)) {
            return false;
        }

        return $this->getRepository()->softDeleteByWhere($condition);
    }

    public function removeByIds(array $ids): bool
    {
        $pk = $this->getRepository()->getPrimaryKey();

        return $this->getRepository()->softDeleteByWhere([[$pk, 'in', $ids]]);
    }

    public function updateById(array $entity, mixed $id): bool
    {
        return $this->getRepository()->updateById($entity, $id);
    }

    public function update(array $entity, array $condition): bool
    {
        if (empty($condition)) {
            return false;
        }

        return $this->getRepository()->update($entity, $condition);
    }

    /**
     * @throws CustomException
     */
    public function saveOrUpdate(array $entity): bool
    {
        throw new CustomException('TODO: Implement saveOrUpdate() method.');
    }

    public function getById(mixed $id): array
    {
        return $this->getRepository()->findOneById($id);
    }

    public function listByIds(array $ids, string $order = '', string $sort = 'desc'): array
    {
        $pk = $this->getRepository()->getPrimaryKey();

        $order = empty($order) ? $pk : $order;

        return $this->getRepository()->findAll([[$pk, 'in', $ids]], $order, $sort);
    }

    public function getOne(array $condition = [], string $order = '', string $sort = 'desc'): array
    {
        return $this->getRepository()->findOneByWhere($condition, $order, $sort);
    }

    public function value(string $field, array $condition = []): mixed
    {
        return $this->getRepository()->value($field, $condition);
    }

    public function pluck(string $field, array $condition = []): array
    {
        return $this->getRepository()->column($field, $condition);
    }

    public function count(array $condition = []): int
    {
        return $this->getRepository()->count($condition);
    }

    public function getList(array $condition = [], string $order = '', string $sort = 'desc'): array
    {
        return $this->getRepository()->findAll($condition, $order, $sort);
    }

    public function page(array $condition = [], int $page = 1, int $perPage = 20, string $order = '', string $sort = 'desc'): array
    {
        return $this->getRepository()->paginate($condition, $page, $perPage, $order, $sort);
    }
}
