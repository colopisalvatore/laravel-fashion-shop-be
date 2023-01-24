<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Texture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        return view('admin.products.index', compact('products'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::all();
        $textures = Texture::all();
        $categories = Category::all();

        return view('admin.products.create', compact('brands', 'textures', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * 
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $slug = Product::generateSlug($request->name);

        $data['slug'] = $slug;

        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->put('images', $request->image);
            $data['image'] = $path;
        }

        $new_product = Product::create($data);

       
        return redirect()->route('admin.products.show', $new_product->slug);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * 
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * 
     */
    public function edit(Product $product)
    {
        $brands = Brand::all();
        $textures = Texture::all();
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'brands', 'textures', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * 
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $slug = Product::generateSlug($request->name);
        $data['slug'] = $slug;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete($product->image);
            }
            $path = Storage::disk('public')->put('product_image', $request->image);
            $data['image'] = $path;
        }

        $product->update($data);

        

        return redirect()->route('admin.products.index')->with('message', "$product->name updated successfully");

    }

    /**
     * Remove the specified resource from storage.
     *
     * 
     * @param \App\Models\Product $product
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('message', "$product->name deleted successfully");
    }
}
