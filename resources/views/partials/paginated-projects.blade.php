{{-- Search result count commented out --}}
{{-- <div class="row">
    <div class="col-md-12 margin-bottom-40">
        <h2>
            Showing <span style="color: #333; font-size: 22px;">{{ @$projects->total() }} </span> Results
        </h2>
    </div>
</div> --}}
@foreach($projects as $project)
    <div class="listing-item-container list-layout" style="background: #ffffff; border: 1px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 32px; padding: 24px; border-radius: 12px; transition: all 0.3s ease;">
        <div class="listing-item" style="background: transparent; border: none; box-shadow: none; height: auto; min-height: auto; display: flex; flex-wrap: wrap;">
            <div class="listing-item-image" style="flex: 0 0 250px; min-height: 180px; margin-right: 24px;">
                <a href="/listing/{{ $project->slug }}" target="_blank" rel="noopener noreferrer" class="listing-img-container" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; background: white; border-radius: 10px; padding: 8px; border: 1px solid #e9ecef; transition: all 0.3s ease;">
                    @if(@$project->media->first())
                        <img src="{{ @$project->media->first()->thumbnail_url }}" 
                             data-mobile-src="{{ @$project->media->first()->mobile_thumbnail_url }}"
                             loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}" 
                             alt="{{ $project->name }}" 
                             class="responsive-thumbnail"
                             style="filter: none; max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 6px; transition: transform 0.3s ease;"
                             onerror="this.src='{{ @$project->media->first()->display_url }}'"
                             onload="this.classList.add('loaded')">
                    @else
                        <div style="width: 100%; height: 100%; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                            <i class="fa fa-image" style="color: #dee2e6; font-size: 32px;"></i>
                        </div>
                    @endif
                </a>
            </div>
            
            <div class="listing-item-content" style="flex: 1; padding: 0; min-width: 0;">
                <div class="listing-title" style="margin-bottom: 16px;">
                    <h4 style="margin: 0 0 12px 0; font-size: 24px; line-height: 1.4; font-weight: 600;">
                        <a href="/listing/{{ $project->slug }}" target="_blank" rel="noopener noreferrer" style="color: #212529; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#0d6efd'" onmouseout="this.style.color='#212529'">
                            {!! $project->name !!}
                        </a>
                    </h4>

                    @if(!empty(@$project->location->first()->name))
                        <div style="color: #495057; font-size: 15px; margin-bottom: 12px; display: flex; align-items: center;">
                            <i class="fa fa-map-marker" style="margin-right: 6px; color: #6c757d;"></i>
                            {{ @$project->location->first()->name }}
                        </div>
                    @endif
                </div>

                <p style="font-size: 16px; line-height: 1.7; color: #212529; margin-bottom: 18px; font-weight: 400;">
                    {!! $project->introduction !!}
                </p>

                @if(@$project->tags->count() > 0)
                    <div style="margin-bottom: 14px;">
                        <strong style="color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Tags: </strong>
                        <div style="margin-top: 8px;">
                            @foreach(@$project->tags as $tag)
                                <a href="/listing-tag/{{ @$tag->name }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #d1e7dd; color: #0a3622; padding: 6px 12px; margin: 4px 6px 4px 0; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #badbcc;" onmouseover="this.style.background='#badbcc'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#d1e7dd'; this.style.transform='translateY(0)'">{{ @$tag->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @php
                    // Get all languages - prioritize all_languages JSON field, fallback to language/secondary_language
                    $languages = [];
                    if (!empty($project->all_languages) && is_array($project->all_languages)) {
                        $languages = array_filter($project->all_languages);
                    } else {
                        if (!empty($project->language)) {
                            $languages[] = $project->language;
                        }
                        if (!empty($project->secondary_language)) {
                            $languages[] = $project->secondary_language;
                        }
                    }
                    $languages = array_unique($languages);
                @endphp
                @if (!empty($languages))
                    <div style="margin-bottom: 14px;">
                        <strong style="color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Languages: </strong>
                        <div style="margin-top: 8px;">
                            @foreach($languages as $lang)
                                <a href="/listing-language/{{ urlencode($lang) }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #cfe2ff; color: #084298; padding: 6px 12px; margin: 4px 6px 4px 0; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #9ec5fe;" onmouseover="this.style.background='#9ec5fe'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#cfe2ff'; this.style.transform='translateY(0)'">{{ $lang }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(@$project->categoriesOrdered->count() > 0)
                    <div style="margin-bottom: 14px;">
                        <strong style="color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Categories:</strong>
                        <div style="margin-top: 8px;">
                            @foreach(@$project->categoriesOrdered as $cat)
                                <a href="/listing-category/{{ @$cat->slug }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #198754; color: white; padding: 6px 12px; margin: 4px 6px 4px 0; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #157347;" onmouseover="this.style.background='#157347'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#198754'; this.style.transform='translateY(0)'">{{ @$cat->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @php
                    // Get all organizations - prioritize many-to-many relationship, fallback to single organization
                    $organizations = [];
                    if ($project->organizations->count() > 0) {
                        $organizations = $project->organizations;
                    } elseif (!empty($project->organization_id) && !empty($project->organization)) {
                        $organizations = collect([$project->organization]);
                    }
                @endphp
                @if (!empty($organizations) && count($organizations) > 0)
                    <div style="margin-bottom: 14px;">
                        <strong style="color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Organizations:</strong>
                        <div style="margin-top: 8px;">
                            @foreach($organizations as $org)
                                <a href="/listing-organization/{{ $org->id }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #0d6efd; color: white; padding: 6px 12px; margin: 4px 6px 4px 0; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #0a58ca;" onmouseover="this.style.background='#0a58ca'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#0d6efd'; this.style.transform='translateY(0)'">{{ $org->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty(@$project->organization_type))
                    <div style="margin-bottom: 14px;">
                        <strong style="color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Organization Type:</strong>
                        <div style="margin-top: 8px;">
                            <a href="/listing-organization-type/{{ urlencode(@$project->organization_type) }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #6c757d; color: white; padding: 6px 12px; margin: 4px 6px 4px 0; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #5c636a;" onmouseover="this.style.background='#5c636a'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6c757d'; this.style.transform='translateY(0)'">{{ @$project->organization_type }}</a>
                        </div>
                    </div>
                @endif

                @if(!empty($project->website_url))
                    <div class="listing-footer" style="margin-top: 18px; padding-top: 18px; border-top: 2px solid #e9ecef;">
                        <a href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer" style="color: #198754; text-decoration: none; font-size: 15px; font-weight: 500; display: inline-flex; align-items: center; transition: color 0.2s ease;" onmouseover="this.style.color='#157347'" onmouseout="this.style.color='#198754'">
                            <i class="fa fa-globe" style="margin-right: 8px;"></i> {{ $project->website_url }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach