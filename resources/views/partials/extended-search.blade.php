<form method="GET">
    <div class="row">
        <div class="col-md-12">
            <div class="main-search-input gray-style margin-top-0 margin-bottom-10" style="z-index: 10000;">
                <label for="project-search-input" class="visually-hidden">Search Connecting Current projects</label>
                <div class="main-search-input-item">
                    <input id="project-search-input" name="q" type="text" class="typeahead tt-query" placeholder="Search Connecting Current projects ... " autocomplete="off" value="{{ @$query }}" />
                </div>

                <!-- <button class="button" id="search">Search</button> -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="main-search-box no-shadow margin-bottom-30" style="border-bottom: 2px dotted #ccc;">
                <div class="row with-forms margin-bottom-30">
                    <!-- <input type="hidden" name="q" value="{{ @$query }}"> -->
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-categories-input" class="visually-hidden">Categories</label>
                        <select id="filter-categories-input" name="categories[]" data-placeholder="All Categories" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}"
                                    {{ (is_array(request('categories')) && in_array($cat->name, request('categories'))) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-tags-input" class="visually-hidden">Tags</label>
                        <select id="filter-tags-input" name="tags[]" data-placeholder="All Tags" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($allTags as $tag)
                                <option value="{{ $tag->name }}"
                                    {{ (is_array(request('tags')) && in_array($tag->name, request('tags'))) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-project-type-input" class="visually-hidden">Type</label>
                        <select id="filter-project-type-input" name="types[]" data-placeholder="Type" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($listingTypes as $type)
                                <option value="{{ $type }}"
                                    {{ (is_array(request('types')) && in_array($type, request('types'))) ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-organizations-input" class="visually-hidden">Organizations</label>
                        <select id="filter-organizations-input" name="organizations[]" data-placeholder="All Organizations" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($allOrganizations as $org)
                                <option value="{{ $org }}"
                                    {{ (is_array(request('organizations')) && in_array($org, request('organizations'))) ? 'selected' : '' }}>
                                    {{ $org }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                {{-- Date filter calendar hidden to save space, especially on mobile --}}
                {{-- <div class="row with-forms margin-bottom-30">
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-date-from-input" class="visually-hidden">Publication Date From</label>
                        <input id="filter-date-from-input" name="date_from" type="date" placeholder="Publication Date From" value="{{ @$filterDateFrom }}" />
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-date-to-input" class="visually-hidden">Publication Date To</label>
                        <input id="filter-date-to-input" name="date_to" type="date" placeholder="Publication Date To" value="{{ @$filterDateTo }}" />
                    </div>

                    <div class="col-lg-6 col-md-4 col-sm-12 col-12" style="text-align: right;">
                        <button class="button" id="search" style="padding: 8px 30px;">Search</button>
                    </div>
                </div> --}}
                <div class="row with-forms margin-bottom-30">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12" style="text-align: right;">
                        <button class="button" id="search" style="padding: 8px 30px;">Search</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
</form>
