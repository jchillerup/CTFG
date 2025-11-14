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
        // Get organizations from the organizations table
        // Exclude placeholder names (names like "Organization rec4yKxu" - "Organization " followed by "rec" and a short ID)
        $organizations = \App\Models\Organization::select('name')
            ->where('name', 'NOT LIKE', 'Organization rec%')
            ->distinct()
            ->orderBy('name', 'ASC')
            ->pluck('name');

        $organizationsArray = array();
        foreach ($organizations as $org) {
            if (!empty($org)) {
                // Additional check to filter out any remaining placeholder patterns
                // Pattern: "Organization " followed by "rec" and 4-8 alphanumeric characters (the Airtable ID prefix)
                if (!preg_match('/^Organization\s+rec[a-zA-Z0-9]{4,8}$/i', $org)) {
                    array_push($organizationsArray, $org);
                }
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
