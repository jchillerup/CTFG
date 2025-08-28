<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Listing;

class Languages
{
    public $languages = [];
    
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct() {
        // Get primary languages
        $primaryLanguages = Listing::whereNotNull('language')
            ->select('language')
            ->distinct()
            ->orderBy('language', 'ASC')
            ->pluck('language');

        // Get secondary languages
        $secondaryLanguages = Listing::whereNotNull('secondary_language')
            ->select('secondary_language')
            ->distinct()
            ->orderBy('secondary_language', 'ASC')
            ->pluck('secondary_language');

        // Combine and deduplicate
        $allLanguages = $primaryLanguages->merge($secondaryLanguages)->unique()->sort()->values();
        
        $languagesArray = array();
        foreach ($allLanguages as $lang) {
            if (!empty($lang)) {
                array_push($languagesArray, $lang);
            }
        }
        
        $this->languages = $languagesArray;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(['allLanguages' => $this->languages]);
    }
}
