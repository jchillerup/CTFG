<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Listing;
use App\Models\Category;
use App\Models\Tag;
use App\Models\SearchLog;

class GuestController extends Controller {
    /**
     * Get index page
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return view
     */ 
    public function index(Request $request) {
        if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = 'Show active projects only';
        }

        request()->merge([
            'status' => $filterStatus
        ]);

        $projects = Listing::query()
            ->when(request('tags'), function($builder) {
                $tags = request('tags');

                $builder->whereHas('tags', function($builder) use ($tags) {
                    $builder->whereIn('name', $tags);
                });
            })
            ->when(request('categories'), function($builder) {
                $categories = request('categories');

                $builder->whereHas('categories', function($builder) use ($categories) {
                    $builder->whereIn('name', $categories);
                });
            })
            ->when(request('opensource'), function($builder) {
                $builder->where('open_source', request('opensource'));
            })
            ->when(request('types'), function($builder) {
                $types = request('types');
                if (in_array("Other", $types)) {
                    $key = array_search("Other", $types);
                    $types[$key] = NULL;

                    $builder->whereIn('type', $types)->orWhereNull('type');
                } else {
                    $builder->whereIn('type', $types);   
                }
            })
            ->when(request('organizationtypes'), function($builder) {
                $organizationtypes = request('organizationtypes');
                if (in_array("Other", $organizationtypes)) {
                    $key = array_search("Other", $organizationtypes);
                    $organizationtypes[$key] = NULL;

                    $builder->whereIn('organization_type', $organizationtypes)->orWhereNull('organization_type');
                } else {
                    $builder->whereIn('organization_type', $organizationtypes);   
                }
            })
            ->when(request('parentorganizations'), function($builder) {
                $parentOrganizations = request('parentorganizations');
                // Filter projects by parent organization
                $builder->whereIn('parent_organization', $parentOrganizations);
            })
            ->when(request('organizations'), function($builder) {
                $organizations = request('organizations');
                // Filter projects by the selected organizations - check both single and many-to-many relationships
                $builder->where(function($query) use ($organizations) {
                    $query->whereHas('organization', function($q) use ($organizations) {
                        $q->whereIn('name', $organizations);
                    })->orWhereHas('organizations', function($q) use ($organizations) {
                        $q->whereIn('name', $organizations);
                    });
                });
            })
            ->when(request('languages'), function($builder) {
                $languages = request('languages');
                $builder->where(function($query) use ($languages) {
                    foreach ($languages as $lang) {
                        $query->where(function($q) use ($lang) {
                            $q->where('language', $lang)
                              ->orWhere('secondary_language', $lang)
                              ->orWhereJsonContains('all_languages', $lang);
                        });
                    }
                });
            })
            ->when(request('date_from'), function($builder) {
                $dateFrom = request('date_from');
                $builder->where('created', '>=', $dateFrom);
            })
            ->when(request('date_to'), function($builder) {
                $dateTo = request('date_to');
                $builder->where('created', '<=', $dateTo);
            })
            ->when(request('status'), function($builder) {
                $status = request('status');
                if ($status == "Show active projects only") {
                    $builder->whereIn('status', ['Active', 'N/A']);
                } else {
                    $builder->whereIn('status', ['Active', 'N/A', 'Inactive', 'Document'])->orWhereNull('status');
                }
            }, function($builder) {
                $builder->whereIn('status', ['Active', 'N/A']);
            })
            ->when(request('q'), function($builder) {
                $builder->searchQuery(request('q'));
            })
            ->orderByRaw('-cover_image DESC')
            ->with(['organization', 'organizations', 'children'])
            ->orderBy('created', 'DESC')
            ->paginate(50);

        // Queue job for logging
        if (request('q')) {
            $this->logSearch(request('q'), $projects->total());
        }

        /*if (count(request()->all()) == 0) {
            $filterStatus = "Active";
        } else if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = '';
        } */


        $allProjects = Listing::count();

        return view ('projects.search-results', [
            'title' => 'Connecting Current - Directory',
            'menu' => 'directory',
            'projects' => $projects,
            'query' => request('q'),
            'filterCategories' => request('categories'),
            'filterTags' => request('tags'),
            'filterCountries' => request('countries'),
            'filterStatus' => $filterStatus,
            'filterOrgTypes' => request('organizationtypes'),
            'filterOpenSource' => request('opensource'),
            'filterTypes' => request('types'),
            'filterParentOrganizations' => request('parentorganizations'),
            'filterOrganizations' => request('organizations'),
            'filterLanguages' => request('languages'),
            'filterDateFrom' => request('date_from'),
            'filterDateTo' => request('date_to'),
            'allProjects' => $allProjects,
        ]);

    }

    // Get world map
    public function worldMap() {
        $projects = Listing::whereNotNull('latitude')->whereNotNull('longitude')->get(['latitude', 'longitude', 'first_location',  'hq_location', 'name', 'slug']);

        return view ('map.all-projects', [
            'title' => 'Connecting Current - World Map',
            'projects' => $projects,
            'template' => 'map',
            'menu' => 'map',
            'gMapsApiKey' => config('services.google.key'),
        ]);
    }

    // Log Search
    public function logSearch($query, $total) {
        $log = new SearchLog;
        $log->item = $query;
        $log->search_count =  $total;
        $log->total_results_count = $total;
        $log->last_search_results_count = $total;
        $log->save();
    }
}
