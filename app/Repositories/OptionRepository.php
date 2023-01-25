<?php

namespace App\Repositories;

use App\Interfaces\OptionRepositoryInterface;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OptionRepository extends BaseRepository implements OptionRepositoryInterface
{
    public function __construct(Option $model)
    {
        parent::__construct($model);
    }

    public function lists(): Collection
    {
        return $this->model->get();
    }

    public function find($id): Option
    {
        return $this->model->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('option_name', $id);
        })->firstOrFail();
    }


    public function search(Request $request): Collection
    {
        $query = $this->model->newQuery();

        $optionName = $request->get('option_name');

        $query->when($optionName, function ($query) use ($optionName) {
            return $query->whereIn('option_name', $optionName);
        });

        return $query->get()->pluck('option_value', 'option_name');
    }
}
