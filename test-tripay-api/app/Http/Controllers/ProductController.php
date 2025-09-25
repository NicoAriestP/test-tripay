<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\Product\CreateProductFormRequest;
use App\Http\Requests\Product\EditProductFormRequest;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductsCollection;
use App\Actions\Product\ProductAction;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $models = Product::query()
            ->when(
                $search,
                fn($query) =>
                $query->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('price', 'like', "%$search%")
                    ->orWhere('reference', 'like', "%$search%")
            )
            ->orderBy('id', 'ASC')
            ->get();

        return ProductsCollection::make($models);
    }

    public function store(CreateProductFormRequest $request, ProductAction $action)
    {
        $model = $action->store($request);

        return new ProductResource($model);
    }

    public function update(Product $model, EditProductFormRequest $request, ProductAction $action)
    {
        $model = $action->update($request, $model);

        return new ProductResource($model);
    }

    public function destroy(Product $model)
    {
        $model->delete();
        return response()->noContent();
    }
}
