<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;

class ParentCategoryController extends Controller {
    // Get categories under The Tech
    public function theTech() {
        $theTech = Category::where('name', 'The Tech')->first();

        $categories = Category::where('parent_id', $theTech->id)->orderBy('order_sort', 'ASC')->with('childItems')->get();
        return view ('category.hierarchies', [
            'items' => $categories,
            'title' => 'The Tech',
            'menu' => 'tech',
        ]);
    }

    // Get categories under The People
    public function thePeople() {
        $thePeople = Category::where('name', 'The People')->first();

        $categories = Category::where('parent_id', $thePeople->id)->orderBy('order_sort', 'ASC')->with('childItems')->get();

        return view ('category.hierarchies', [
            'items' => $categories,
            'title' => 'The People',
            'menu' => 'people',
        ]);
    }

    // Get categories under Adjacent Fields
    public function adjacent() {
        $adjacent = Category::where('name', 'Adjacent Fields')->first();

        $categories = Category::where('parent_id', $adjacent->id)->orderBy('order_sort', 'ASC')->with('childItems')->get();
        
        return view ('category.hierarchies', [
            'items' => $categories,
            'title' => 'Adjacent Fields',
            'menu' => 'adjacent',
        ]);
    }
}
