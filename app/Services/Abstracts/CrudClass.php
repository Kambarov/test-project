<?php

namespace App\Services\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class CrudClass
{
    public function create(Model $model, array $attributes): Model|Builder
    {
        return $model::query()
            ->create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model;
    }

    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
}
