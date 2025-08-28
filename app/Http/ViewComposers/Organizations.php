<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Listing;

class Organizations
{
    public $organizations = [];
    
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct() {
        // Get organizations from the knowledge table (dedicated organizations table)
        $organizations = \App\Models\Knowledge::select('name')
            ->distinct()
            ->orderBy('name', 'ASC')
            ->pluck('name');

        $organizationsArray = array();
        foreach ($organizations as $org) {
            if (!empty($org)) {
                array_push($organizationsArray, $org);
            }
        }
        
        $this->organizations = $organizationsArray;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(['allOrganizations' => $this->organizations]);
    }
}
