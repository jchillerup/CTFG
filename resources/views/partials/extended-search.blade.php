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
                    <div class="col-md-4 col-sm-12">
                        <label for="filter-categories-input" class="visually-hidden">Categories</label>
                        <select id="filter-categories-input" name="categories[]" data-placeholder="All Categories" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="filter-tags-input" class="visually-hidden">Tags</label>
                        <select id="filter-tags-input" name="tags[]" data-placeholder="All Tags" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($allTags as $tag)
                                <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="filter-project-type-input" class="visually-hidden">Type</label>
                        <select id="filter-project-type-input" name="types[]" data-placeholder="Type" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($listingTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="filter-countries-input" class="visually-hidden">Countries</label>
                        <select id="filter-countries-input" name="countries[]" data-placeholder="All Countries" id="countries" class="chosen-select-no-single" multiple style="display: none;">
                            @foreach($allCountries as $country)
                                <option value="{{ $country->name }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>
                <div class="row with-forms margin-bottom-30">
                    <div class="col-md-3 col-sm-12">
                        <label for="filter-date-from-input" class="visually-hidden">Publication Date From</label>
                        <input id="filter-date-from-input" name="date_from" type="date" placeholder="Publication Date From" value="{{ @$filterDateFrom }}" />
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <label for="filter-date-to-input" class="visually-hidden">Publication Date To</label>
                        <input id="filter-date-to-input" name="date_to" type="date" placeholder="Publication Date To" value="{{ @$filterDateTo }}" />
                    </div>

                    <div class="col-md-6" style="text-align: right;">
                        <button class="button" id="search" style="padding: 8px 30px;">Search</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
</form>
