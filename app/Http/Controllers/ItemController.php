<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ItemController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $items = Item::with(['user', 'category'])
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($w) use ($request) {
                    $w->where('title', 'like', '%' . $request->q . '%')
                      ->orWhere('description', 'like', '%' . $request->q . '%');
                });
            })
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->location, fn ($q) => $q->where('location', 'like', '%' . $request->location . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('items.index', compact('items'));
    }


    public function create()
    {
    // Authorization: any authenticated user may create (policy returns true)
        $this->authorize('create', Item::class);

        $categories = Category::all();
        return view('items.create', compact('categories'));
    }


    public function store(Request $request)
    {
        // Authorization
        $this->authorize('create', Item::class);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'condition'     => 'nullable|string|max:255',
            'status'        => 'required|string|max:50',
            'location'      => 'nullable|string|max:255',
            'price_per_day' => 'nullable|string|max:50',
            'category_id'   => 'required|exists:categories,id',

            'cover_image'   => 'nullable|image|max:2048',
            'images.*'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('items', 'public');
        }

        $data['user_id'] = auth()->id();

        $item = Item::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                if ($img) {
                    $path = $img->store('items', 'public');
                    $item->images()->create(['path' => $path]);
                }
            }
        }

        return redirect()->route('items.index')->with('status', 'Item toegevoegd!');
    }


    public function show(Item $item)
    {
        $item->load(['user', 'category', 'images']);

        return view('items.show', compact('item'));
    }


    public function edit(Item $item)
    {
        // Authorization: only owner or admin may edit
        $this->authorize('update', $item);

        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        // Authorization
        $this->authorize('update', $item);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'condition'     => 'nullable|string|max:255',
            'status'        => 'required|string|max:50',
            'location'      => 'nullable|string|max:255',
            'price_per_day' => 'nullable|string|max:50',
            'category_id'   => 'required|exists:categories,id',
            'cover_image'   => 'nullable|image|max:2048',
            'images.*'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('items', 'public');
        }

        $item->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                if ($img) {
                    $path = $img->store('items', 'public');
                    $item->images()->create(['path' => $path]);
                }
            }
        }

        return redirect()->route('items.index')->with('status', 'Item bijgewerkt!');
    }

 
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        $item->status = 'gearchiveerd';
        $item->save();

        return redirect()->route('items.index')->with('status', 'Item is gearchiveerd.');
    }

    public function byCategory(Category $category)
    {
        $items = Item::with(['user', 'category'])
            ->where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('items.by-category', compact('items', 'category'));
    }
}
