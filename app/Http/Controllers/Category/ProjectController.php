<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingTag;
use App\Models\Tag;

class ProjectController extends Controller {
    // Get projects by category
    public function getProjectsByCategory(Request $request) {
        $slug = $request->segment(2);

        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return abort(404);
        }

        $parent = $category->parent;
        $grandParent = '';
        $greatGrandParent = '';
        $greatGreatGrandParent = '';
        $ancestor = '';
        
        if (!empty($parent)) {
            if (!empty(Category::where('name', $parent->name)->first()->parent->name)) {
                $grandParent = Category::where('name', $parent->name)->first()->parent;
            }
        }

        if (!empty($grandParent)) {
            if (!empty(Category::where('name', $grandParent->name)->first()->parent)) {
                $greatGrandParent = Category::where('name', $grandParent->name)->first()->parent;
            }
        }

        if (!empty($greatGrandParent)) {
            if (!empty(Category::where('name', $greatGrandParent->name)->first()->parent)) {
                $greatGreatGrandParent = Category::where('name', $greatGrandParent->name)->first()->parent;
            }
        }

        if (!empty($greatGreatGrandParent)) {
            if (!empty(Category::where('name', $greatGreatGrandParent->name)->first()->parent)) {
                $ancestor = Category::where('name', $greatGreatGrandParent->name)->first()->parent;
            }
        }

        if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = 'Show active projects only';
        }

        request()->merge([
            'status' => $filterStatus
        ]);

        $listingIds = ListingCategory::where('category_id', $category->id)->pluck('listing_id')->toArray();

        $projects = Listing::query()
            ->whereIn('id', $listingIds)
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
            ->when(request('parentorganizations'), function($builder) {
                $parentOrganizations = request('parentorganizations');
                // Filter projects by parent organization
                $builder->whereIn('parent_organization', $parentOrganizations);
            })
            ->when(request('status'), function($builder) {
                $status = request('status');
                if ($status == "Show active projects only") {
                    $builder->whereIn('status', ['Active', 'N/A']);
                } else {
                    $builder = $builder;
                }
            })
            ->when(request('q'), function($builder) {
                $builder->searchQuery(request('q'));
            })
            ->with(['organization', 'organizations', 'children'])
            ->orderBy('created', 'DESC')
            ->paginate(50);

        $category->update([
            'hits' => $category->hits + 1,
        ]);

        return view ('projects.projects-by-category', [
            'title' => 'Projects - '.$category->name,
            'categoryName' => $category->name,
            'category' => $category,
            'parentCategoryName' => @$category->parentCategory->name,
            'categoryDesc' => @$category->description,
            'projects' => $projects,
            'activeAncestor' => @$category->parent->parent->parent,
            'activeGrandParent' => @$category->parent->parent,
            'activeParent' => @$category->parent,
            'activeCat' => $category->name,
            'query' => request('q'),
            'filterCategories' => request('categories'),
            'filterTags' => request('tags'),
            'filterCountries' => request('countries'),
            'filterStatus' => request('status'),
            'filterOrgTypes' => request('organizationtypes'),
            'filterOpenSource' => request('opensource'),
            'filterTypes' => request('types'),
        ]);
    }

    // Get projects by tag
    public function getProjectsByTag(Request $request) {
        $name = $request->segment(2);

        $tag = Tag::where('name', $name)->first();

        if (!$tag) {
            return abort(404);
        }

        //$projects = $tag->listings()
        $listingIds = ListingTag::where('tag_id', $tag->id)->pluck('listing_id')->toArray();

        $projects = Listing::query()
            ->whereIn('id', $listingIds)
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
            ->when(request('countries'), function($builder) {
                $countries = request('countries');

                $builder->when(count($countries),function ($builder)use ($countries) {
                    $builder->whereHas('location', function($builder) use ($countries) {
                        $builder->where( function($builder) use ($countries) {
                            foreach ($countries as $country) {
                                $builder->orWhere('country', 'LIKE', '%' . $country . '%');
                                //$builder->orWhere('name', 'LIKE', '%' . $country . '%');
                            }
                        });
                    });
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
            ->when(request('status'), function($builder) {
                $status = request('status');
                if ($status == "Show active projects only") {
                    $builder->whereIn('status', ['Active', 'N/A']);
                } else {
                    $builder->whereIn('status', ['Active', 'N/A', 'Inactive', 'Document']);
                }
            }, function($builder) {
                $builder->whereIn('status', ['Active', 'N/A']);
            })
            ->when(request('q'), function($builder) {
                $builder->searchQuery(request('q'));
            })
            ->with(['organization', 'organizations', 'children'])
            ->orderBy('created', 'DESC')
            ->paginate(50);

        $parentTag = $tag->parent;

        if (count(request()->all()) == 0) {
            $filterStatus = "Active";
        } else if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = '';
        }

        return view ('projects.projects-by-tag', [
            'projects' => $projects,
            'title' => 'Projects - '.$tag->name,
            'tagName' => $tag->name,
            'tag' => $tag,
            'activeTag' => $tag,
            'activeParentTag' => $parentTag,
            'query' => request('q'),
            'filterCategories' => request('categories'),
            'filterTags' => request('tags'),
            'filterCountries' => request('countries'),
            'filterStatus' => $filterStatus,
            'filterOrgTypes' => request('organizationtypes'),
            'filterOpenSource' => request('opensource'),
            'filterTypes' => request('types'),
        ]);
    }

    // Get projects by language
    public function getProjectsByLanguage(Request $request) {
        // Handle URL-encoded language names with special characters
        $language = urldecode($request->route('name'));
        
        // Replace + with space (in case urlencode used + instead of %20)
        $language = str_replace('+', ' ', $language);
        
        if (empty($language)) {
            return abort(404);
        }

        if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = 'Show active projects only';
        }

        request()->merge([
            'status' => $filterStatus
        ]);

        $projects = Listing::query()
            ->where(function($query) use ($language) {
                // Check in language field
                $query->where('language', $language)
                      // Check in secondary_language field
                      ->orWhere('secondary_language', $language)
                      // Check in all_languages JSON array
                      ->orWhereJsonContains('all_languages', $language);
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
            ->when(request('status'), function($builder) {
                $status = request('status');
                if ($status == "Show active projects only") {
                    $builder->where(function($query) {
                        return $query->whereIn('status', ['Active', 'N/A'])
                                     ->orWhereNull('status');
                    });
                }
            })
            ->with(['organization', 'organizations', 'children'])
            ->orderBy('created', 'DESC')
            ->paginate(50);

        if (count(request()->all()) == 0) {
            $filterStatus = "Active";
        } else if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = '';
        }

        return view ('projects.projects-by-language', [
            'projects' => $projects,
            'title' => 'Projects - '.$language,
            'languageName' => $language,
            'query' => request('q'),
            'filterCategories' => request('categories'),
            'filterTags' => request('tags'),
            'filterCountries' => request('countries'),
            'filterStatus' => $filterStatus,
            'filterOrgTypes' => request('organizationtypes'),
            'filterOpenSource' => request('opensource'),
            'filterTypes' => request('types'),
        ]);
    }

    // Get projects by organization
    public function getProjectsByOrganization(Request $request) {
        $slug = urldecode($request->segment(2));

        // Try to find by slug first
        $organization = \App\Models\Organization::where('slug', $slug)->first();
        
        // Fallback: try to find by name (in case slug wasn't generated properly)
        if (!$organization) {
            $organization = \App\Models\Organization::where('name', $slug)->first();
            if ($organization) {
                \Log::warning("Organization found by name but not slug: {$slug}. Organization: {$organization->name} (ID: {$organization->id}). Slug should be: {$organization->slug}");
                // Generate slug if missing
                if (empty($organization->slug)) {
                    $organization->slug = Organization::generateSlug($organization->name, $organization->id);
                    $organization->save();
                }
            }
        }

        if (!$organization) {
            \Log::warning("Organization not found for slug/name: {$slug}");
            return abort(404);
        }

        \Log::debug("Finding listings for organization: {$organization->name} (ID: {$organization->id}, Slug: {$slug})");

        $projects = Listing::query()
            ->where(function($query) use ($organization) {
                // Check both single organization_id and many-to-many organizations relationship
                // Use same approach as search filter for consistency
                $query->where('organization_id', $organization->id)
                      ->orWhereHas('organizations', function($q) use ($organization) {
                          $q->where('organizations.id', $organization->id);
                      });
            })
            ->when(request('categories'), function($builder) {
                $categories = request('categories');

                $builder->whereHas('categories', function($builder) use ($categories) {
                    $builder->whereIn('name', $categories);
                });
            })
            ->when(request('tags'), function($builder) {
                $tags = request('tags');

                $builder->whereHas('tags', function($builder) use ($tags) {
                    $builder->whereIn('name', $tags);
                });
            })
            ->when(request('countries'), function($builder) {
                $countries = request('countries');

                $builder->when(count($countries),function ($builder)use ($countries) {
                    $builder->whereHas('location', function($builder) use ($countries) {
                        $builder->where( function($builder) use ($countries) {
                            foreach ($countries as $country) {
                                $builder->orWhere('country', 'LIKE', '%' . $country . '%');
                            }
                        });
                    });
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
            ->when(request('status'), function($builder) {
                $status = request('status');
                if ($status == "Show active projects only") {
                    $builder->whereIn('status', ['Active', 'N/A']);
                } else {
                    $builder->whereIn('status', ['Active', 'N/A', 'Inactive', 'Document']);
                }
            }, function($builder) {
                $builder->whereIn('status', ['Active', 'N/A']);
            })
            ->when(request('q'), function($builder) {
                $builder->searchQuery(request('q'));
            })
            ->with(['organization', 'organizations', 'children'])
            ->orderBy('created', 'DESC')
            ->paginate(50);

        \Log::info("Found {$projects->total()} listings for organization: {$organization->name} (ID: {$organization->id}, Slug: {$slug})");
        
        // Debug: Check counts for both relationships
        $countBySingle = Listing::where('organization_id', $organization->id)->count();
        $countByMany = Listing::whereHas('organizations', function($q) use ($organization) {
            $q->where('organizations.id', $organization->id);
        })->count();
        \Log::info("Organization '{$organization->name}' - Single org_id count: {$countBySingle}, Many-to-many count: {$countByMany}, Total paginated: {$projects->total()}");
        
        // Additional debug: Log the actual listings found
        if ($projects->total() > 0) {
            foreach ($projects->items() as $listing) {
                \Log::info("Listing found: {$listing->name} (ID: {$listing->id}, Status: {$listing->status})");
            }
        } else {
            \Log::warning("No listings found for organization '{$organization->name}' despite query showing results");
        }

        if (count(request()->all()) == 0) {
            $filterStatus = "Active";
        } else if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = '';
        }

        return view ('projects.projects-by-organization', [
            'projects' => $projects,
            'title' => 'Projects - '.$organization->name,
            'organizationName' => $organization->name,
            'organization' => $organization,
            'activeOrganization' => $organization,
            'query' => request('q'),
            'filterCategories' => request('categories'),
            'filterTags' => request('tags'),
            'filterCountries' => request('countries'),
            'filterStatus' => $filterStatus,
            'filterOrgTypes' => request('organizationtypes'),
            'filterOpenSource' => request('opensource'),
            'filterTypes' => request('types'),
        ]);
    }

    // Get projects by organization type
    public function getProjectsByOrganizationType(Request $request) {
        $type = urldecode($request->route('type'));

        $projects = Listing::query()
            ->where('organization_type', $type)
            ->when(request('categories'), function($builder) {
                $categories = request('categories');

                $builder->whereHas('categories', function($builder) use ($categories) {
                    $builder->whereIn('name', $categories);
                });
            })
            ->when(request('tags'), function($builder) {
                $tags = request('tags');

                $builder->whereHas('tags', function($builder) use ($tags) {
                    $builder->whereIn('name', $tags);
                });
            })
            ->when(request('countries'), function($builder) {
                $countries = request('countries');

                $builder->when(count($countries),function ($builder)use ($countries) {
                    $builder->whereHas('location', function($builder) use ($countries) {
                        $builder->where( function($builder) use ($countries) {
                            foreach ($countries as $country) {
                                $builder->orWhere('country', 'LIKE', '%' . $country . '%');
                            }
                        });
                    });
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
            ->whereIn('status', ['Active', 'N/A'])
            ->when(request('q'), function($builder) {
                $builder->searchQuery(request('q'));
            })
            ->with(['organization', 'organizations', 'children'])
            ->orderBy('created', 'DESC')
            ->paginate(50);

        if (count(request()->all()) == 0) {
            $filterStatus = "Active";
        } else if(request('status')){
            $filterStatus = request('status');
        } else {
            $filterStatus = '';
        }

        return view ('projects.projects-by-organization-type', [
            'projects' => $projects,
            'title' => 'Projects - '.$type,
            'organizationTypeName' => $type,
            'organizationType' => $type,
            'activeOrganizationType' => $type,
            'query' => request('q'),
            'filterCategories' => request('categories'),
            'filterTags' => request('tags'),
            'filterCountries' => request('countries'),
            'filterStatus' => $filterStatus,
            'filterOrganizations' => request('organizations'),
            'filterOpenSource' => request('opensource'),
            'filterTypes' => request('types'),
        ]);
    }

    // Get tags table
    public function tagsTable() {
        $tags = Tag::whereNull('parent_id')
            ->whereIn('name', ['Publication'])
            ->orderByRaw("FIELD(name , 'Publication') ASC")
            ->with('childItems')
            ->get();

        return view ('tags.tags-table', [
            'tags' => $tags,
            'title' => 'Listing Tags',
            'menu' => 'tags',
        ]);
    }
}
