<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $categories = Category::paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['cat_name' => 'required|unique:tbl_categories,cat_name', 'cat_description' => 'nullable|string']);
        Category::create($request->all());
        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, $id)
    {
        $cat = Category::findOrFail($id);
        $request->validate(['cat_name' => 'required|unique:tbl_categories,cat_name,' . $id . ',cat_id', 'cat_description' => 'nullable']);
        $cat->update($request->all());
        return back()->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        $cat = Category::findOrFail($id);
        $cat->delete();
        return back()->with('success', 'Category deleted.');
    }
}
