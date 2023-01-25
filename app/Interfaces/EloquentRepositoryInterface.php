<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface EloquentRepositoryInterface
{
    public function paginate(array $attributes): LengthAwarePaginator;

    public function store(array $attributes, $id = null): Model;

    public function find($id): Model;

    public function delete($id);
}
