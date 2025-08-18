<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // needed for slug()

class CategoryController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);


        $data['slug'] = Str::slug($data['name']);


        Category::create($data);


        return back()->with('status', 'Categorie toegevoegd.');
    }
}
