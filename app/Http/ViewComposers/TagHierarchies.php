<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

use Cache;

use App\Models\Tag;

class TagHierarchies {
    public $tagHierarchies;
    
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct() {

        $tagHierarchies = Tag::whereNull('parent_id')
            ->orderBy('order_sort', 'ASC')
            ->orderBy('name', 'ASC')
            ->with('childItems')
            ->get();

        $this->tagHierarchies = $tagHierarchies;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view) {
        $view->with(['tagHierarchies' => $this->tagHierarchies]);
    }
}