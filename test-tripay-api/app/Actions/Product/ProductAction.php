<?php

namespace App\Actions\Product;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Http\Requests\Product\CreateProductFormRequest;
use App\Http\Requests\Product\EditProductFormRequest;

class ProductAction
{
    public function store(CreateProductFormRequest $request): Product
    {
        $validated = $request->validated();

        $model = new Product($validated);
        $model->save();

        return $model;
    }

    public function update(EditProductFormRequest $request, Product $model): Product
    {
        $validated = $request->validated();

        $model->update($validated);

        return $model;
    }
}
