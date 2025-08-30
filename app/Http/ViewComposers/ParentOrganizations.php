<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Listing;

class ParentOrganizations
{
    public $parentOrganizations = [];
    
    /**
     * Create a parent organizations composer.
     *
     * @return void
     */
    public function __construct() {
        // Get distinct parent organizations from listings table
        $parentOrganizations = Listing::select('parent_organization')
            ->whereNotNull('parent_organization')
            ->where('parent_organization', '!=', '')
            ->distinct()
            ->orderBy('parent_organization', 'ASC')
            ->pluck('parent_organization');

        $parentOrganizationsArray = array();
        foreach ($parentOrganizations as $org) {
            if (!empty($org)) {
                array_push($parentOrganizationsArray, $org);
            }
        }
        
        $this->parentOrganizations = $parentOrganizationsArray;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(['allParentOrganizations' => $this->parentOrganizations]);
    }
}
