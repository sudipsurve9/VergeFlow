<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiType;

class ApiTypeController extends Controller
{
    public function index()
    {
        $types = ApiType::orderBy('name')->paginate(10);
        return view('admin.api_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.api_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:api_types,name',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        ApiType::create($request->only(['name', 'icon', 'description']));
        return redirect()->route('admin.api-types.index')->with('success', 'API Type added successfully.');
    }

    public function edit($id)
    {
        $type = ApiType::findOrFail($id);
        return view('admin.api_types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = ApiType::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:api_types,name,' . $id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $type->update($request->only(['name', 'icon', 'description']));
        return redirect()->route('admin.api-types.index')->with('success', 'API Type updated successfully.');
    }

    public function destroy($id)
    {
        $type = ApiType::findOrFail($id);
        $type->delete();
        return redirect()->route('admin.api-types.index')->with('success', 'API Type deleted successfully.');
    }
} 