<?php

namespace App\Modules\Core\Services\Impl;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Services\BaseServiceInterface;
use App\Modules\Core\Repositories\BaseRepositoryInterface;
use Exception;

abstract class BaseService implements BaseServiceInterface
{
    public string $modelName;

    public function __construct(
        private readonly BaseRepositoryInterface $repository
    ) {}

    public function getModelName(): string
    {
        $model = $this->repository->getModel();
        // 1. Eğer dil dosyasından değeri alabilirsek, onu döndür
        $translationKey = 'models.' . get_class($model);
        $translated = __($translationKey);
        // Eğer çeviri bulunamadıysa, $translated değişkeni çeviri anahtarının kendisiyle aynı olur
        // Ayrıca boş veya null dönerse de kontrol et
        if (
            $translated !== $translationKey &&
            !empty($translated) &&
            is_string($translated)
        ) {
            return $translated;
        }

        // 2. Eğer $this->modelName varsa onu döndür
        if (!empty($this->modelName)) {
            return $this->modelName;
        }

        // 3. Eğer $model değişkeni varsa class_basename ile döndür
        if ($model) {
            return class_basename($model);
        }

        // 4. Hiçbiri yoksa 'Record' döndür
        return 'Record';
    }

    public function index()
    {
        return $this->repository->findAll();
    }

    public function show(int $id)
    {
        $record = $this->repository->findById($id);
        if (!$record) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        return $record;
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->repository->findById($id);
        if (!$record) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        try {
            $record = $this->repository->findById($id);
            if (!$record) {
                throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
            }
            return $this->repository->delete($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function destroy(int $id)
    {
        $record = $this->repository->findById($id);
        if (!$record) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        return $this->repository->destroy($id);
    }
}
