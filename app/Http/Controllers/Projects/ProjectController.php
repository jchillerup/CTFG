<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use DB;

use App\Jobs\LogSearch;

use App\Models\Listing;
use App\Models\SearchLog;
use App\Models\Category;
use App\Models\Tag;

class ProjectController extends Controller {
    // Embed add project Airtable form view
    public function add() {
        return view('projects.add', [
            'title' => 'Share a project',
            'menu' => 'add-project'
        ]);
    }

    // Get a single project - by name
    public function singleProject(Request $request) {
        $slug = $request->segment(2);

        $project = Listing::with('organization')->where('slug', $slug)->first();

        if (!$project) {
            return abort(404);
        }

        // Update category hits
        foreach ($project->categories as $cat) {
            $cat->update([
                'hits' => $cat->hits + 1,
            ]);
        }

        return view('projects.single', [
            'title' => 'Project - '.$project->name,
            'menu' => 'directory',
            'project' => $project,
            'gMapsApiKey' => config('services.google.key'),
        ]);
    }

    // Search
    public function search(Request $request) {
        if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = 'Show active projects only';
        }

        request()->merge([
            'status' => $filterStatus
        ]);

        $projects = Listing::query()
            ->when(request('q'), function($builder) {
                $builder->searchQuery(request('q'));
            })
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
                // Filter projects by the selected organizations
                $builder->whereHas('organization', function($query) use ($organizations) {
                    $query->whereIn('name', $organizations);
                });
            })
            ->when(request('languages'), function($builder) {
                $languages = request('languages');
                $builder->where(function($query) use ($languages) {
                    $query->whereIn('language', $languages)
                          ->orWhereIn('secondary_language', $languages);
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
                    $builder->where(function($query) {
                        return $query->whereIn('status', ['Active', 'N/A'])
                                     ->orWhereNull('status');
                    });
                }
            })
            ->with('organization')
            ->orderBy('created', 'DESC')
            ->paginate(50);

        // Queue job for logging
        if (request('q')) {
            $this->logSearch(request('q'), $projects->total());
        }

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

    // Search autocomplete
    public function searchAutoComplete(Request $request) {
        $q = $request->query->get('query');
        $q = trim($q);
        
        $data = Listing::select("name", "slug")
            ->where("name", "LIKE", "%{$q}%")
            ->orWhere("introduction", "LIKE", "%{$q}%")
            ->get();

        //$data = \DB::select(DB::raw("SELECT * FROM listings WHERE name LIKE '%{$q}%' OR introduction LIKE '%{$q}%'"));

            
        return response()->json($data);
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
