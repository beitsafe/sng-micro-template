<?php

namespace App\Repositories;

use App\Interfaces\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements EloquentRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate(array $attributes): LengthAwarePaginator
    {
        $search = $attributes['q'] ?? null;
        $perPage = $attributes['per_page'] ?? null;
        $orderCol = $attributes['order'] ?? 'created_at';
        $orderDir = $attributes['dir'] ?? 'DESC';

        $query = $this->model->newQuery();
        $searchColumns = $this->model->searchable ?? $this->model->getFillable();

        $query->when($searchColumns && $search, function ($query) use ($search, $searchColumns) {
            $query->where(function ($q) use ($search, $searchColumns) {
                // Prepare search by all searchable columns
                array_map(function ($column) use ($q, $search) {
                    return $q->orWhere($column, 'LIKE', "%{$search}%");
                }, $searchColumns);
            });
        });

        if (method_exists($this, 'extendPaginate')) {
            $query = $this->extendPaginate($query, $attributes);
        }

        return $query->orderBy($orderCol, $orderDir)->paginate($perPage)->appends($attributes);;
    }

    public function store(array $attributes, $id = null): Model
    {
        if ($id) {
            $this->model = $this->find($id);
        }

        $this->model->fill($attributes);
        $this->model->save();

        return $this->model;
    }

    public function find($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function delete($id)
    {
        $this->model->destroy($id);
    }
}
