<form method="GET" action="/listings/search">
    <div class="row">
        <div class="col-md-12">
            <div class="main-search-input gray-style margin-top-0 margin-bottom-20" style="z-index: 10000; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e9ecef; padding: 12px; background: transparent;">
                <label for="project-search-input" class="visually-hidden">Search Connecting Current projects</label>
                <div class="main-search-input-item">
                    <input id="project-search-input" name="q" type="text" class="typeahead tt-query"
                        placeholder="Search Connecting Current Listings ... " autocomplete="off"
                        value="{{ @$query }}" style="font-size: 16px; padding: 12px 16px; border-radius: 8px; border: 1px solid #dee2e6;" />
                </div>

                <!-- <button class="button" id="search">Search</button> -->
            </div>
        </div>
        <!-- <div class="col-md-2">
            <a href="/search-log" style="color: #28a745; line-height: 70px; float: right;">
                Search Log
            </a>
        </div> -->
    </div>

    <div class="row search-filters-container">
        <div class="col-md-12">
            <div class="main-search-box no-shadow margin-bottom-30" style="border-bottom: 2px solid #e9ecef; padding-bottom: 20px; background: transparent; padding: 20px; border-radius: 12px; border: 1px solid #e9ecef;">
                <div class="row with-forms margin-bottom-20">
                    <!-- <input type="hidden" name="q" value="{{ @$query }}"> -->
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-categories-input" class="visually-hidden">Categories</label>
                        <select id="filter-categories-input" name="categories[]" data-placeholder="All Categories"
                            class="chosen-select-no-single" multiple style="display: none;">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->name }}"
                                    {{ (is_array(request('categories')) && in_array($cat->name, request('categories'))) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-tags-input" class="visually-hidden">Tags</label>
                        <select id="filter-tags-input" name="tags[]" data-placeholder="All Tags"
                            class="chosen-select-no-single" multiple style="display: none;">
                            @foreach ($allTags as $tag)
                                <option value="{{ $tag->name }}"
                                    {{ (is_array(request('tags')) && in_array($tag->name, request('tags'))) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-project-type-input" class="visually-hidden">Type</label>
                        <select id="filter-project-type-input" name="types[]" data-placeholder="Type"
                            class="chosen-select-no-single" multiple style="display: none;">
                            @foreach ($listingTypes as $type)
                                <option value="{{ $type }}"
                                    {{ (is_array(request('types')) && in_array($type, request('types'))) ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-languages-input" class="visually-hidden">Languages</label>
                        <select id="filter-languages-input" name="languages[]" data-placeholder="All Languages"
                            class="chosen-select-no-single" multiple style="display: none;">
                            @foreach ($allLanguages as $lang)
                                <option value="{{ $lang }}"
                                    {{ (is_array(request('languages')) && in_array($lang, request('languages'))) ? 'selected' : '' }}>
                                    {{ $lang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="filter-organizations-input" class="visually-hidden">Organizations</label>
                        <select id="filter-organizations-input" name="organizations[]"
                            data-placeholder="All Organizations" class="chosen-select-no-single" multiple
                            style="display: none;">
                            @foreach ($allOrganizations as $org)
                                <option value="{{ $org }}"
                                    {{ (is_array(request('organizations')) && in_array($org, request('organizations'))) ? 'selected' : '' }}>
                                    {{ $org }}
                                </option>
                            @endforeach
                        </select>
                    </div>





                    {{-- <div class="col-md-4 col-sm-12">
                        <label for="filter-open-source-input" class="visually-hidden">Open Source</label>
                        <select id="filter-open-source-input" name="opensource[]" data-placeholder="Open source" class="chosen-select-no-single" multiple style="display: none;">
                            <?php
                            $opensourceArray = ['Yes', 'No', 'Partially'];
                            ?>
                            @foreach ($opensourceArray as $ops)
                                <option value="{{ $ops }}"
                                    <?php
                                    if (is_array($filterOpenSource) && @in_array($ops, @$filterOpenSource)) {
                                        echo 'selected';
                                    }
                                    ?>
                                    >{{ $ops }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                     <div class="col-lg-9 col-md-4 col-sm-6 col-12" style="text-align: right; display: flex; align-items: center; justify-content: flex-end;">
                        <button class="button" id="search" style="padding: 12px 32px; font-size: 16px; font-weight: 600; border-radius: 8px; background: #0d6efd; border: none; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onmouseover="this.style.background='#0a58ca'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'" onmouseout="this.style.background='#0d6efd'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">Search</button>
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
                {{-- <div class="row with-forms margin-bottom-30">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12" style="text-align: right;">
                        <button class="button" id="search" style="padding: 8px 30px;">Search</button>
                    </div>
                </div> --}}

            </div>

        </div>
    </div>
</form>
