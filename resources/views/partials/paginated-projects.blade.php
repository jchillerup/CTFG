<div class="row">
    <div class="col-md-12 margin-bottom-40">
        <h2>
            Showing <span style="color: #747674; font-size: 22px;">{{ @$projects->total() }} </span> Results
        </h2>
    </div>
</div>
@foreach($projects as $project)
    <div class="listing-item-container list-layout" style="background: transparent; border: none; box-shadow: none; margin-bottom: 40px; padding: 20px; border-radius: 8px; background-color: #f8f9fa;">
        <div class="listing-item" style="background: transparent; border: none; box-shadow: none; height: auto; min-height: auto; display: flex; flex-wrap: wrap;">
            <div class="listing-item-image" style="flex: 0 0 200px; min-height: 150px; margin-right: 20px;">
                <a href="/listing/{{ $project->slug }}" class="listing-img-container" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; background: white; border-radius: 8px; padding: 10px;">
                    @if(@$project->media->first())
                        <img src="{{ @$project->media->first()->display_url }}" loading="lazy" alt="{{ $project->name }}" style="filter: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                    @endif
                </a>
            </div>
            
            <div class="listing-item-content" style="flex: 1; padding: 0; min-width: 0;">
                <div class="listing-title" style="margin-bottom: 15px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 22px; line-height: 1.3;">
                        <a href="/listing/{{ $project->slug }}" style="color: #333; text-decoration: none;">
                            {!! $project->name !!}
                        </a>
                    </h4>

                    @if(!empty(@$project->location->first()->name))
                        <div style="color: #666; font-size: 14px; margin-bottom: 10px;">
                            <i class="fa fa-map-marker"></i>
                            {{ @$project->location->first()->name }}
                        </div>
                    @endif
                </div>

                <p style="font-size: 15px; line-height: 1.6; color: #555; margin-bottom: 15px;">
                    {!! $project->introduction !!}
                </p>

                @if(@$project->tags->count() > 0)
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #333; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Tags:</strong>
                        <div style="margin-top: 5px;">
                            @foreach(@$project->tags->take(3) as $tag)
                                <a href="/listing-tag/{{ @$tag->name }}" style="display: inline-block; background: #d4edda; color: #155724; padding: 4px 8px; margin: 2px 4px 2px 0; border-radius: 4px; text-decoration: none; font-size: 12px;">{{ @$tag->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(@$project->categoriesOrdered->count() > 0)
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #333; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Categories:</strong>
                        <div style="margin-top: 5px;">
                            @foreach(@$project->categoriesOrdered->take(3) as $cat)
                                <a href="/listing-category/{{ @$cat->slug }}" style="display: inline-block; background: #28a745; color: white; padding: 4px 8px; margin: 2px 4px 2px 0; border-radius: 4px; text-decoration: none; font-size: 12px;">{{ @$cat->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty(@$project->organization_id) && !empty(@$project->organization))
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #333; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Organization:</strong>
                        <div style="margin-top: 5px;">
                            <a href="/listing-organization/{{ @$project->organization->id }}" style="display: inline-block; background: #007bff; color: white; padding: 4px 8px; margin: 2px 4px 2px 0; border-radius: 4px; text-decoration: none; font-size: 12px;">{{ @$project->organization->name }}</a>
                        </div>
                    </div>
                @endif

                @if(!empty(@$project->organization_type))
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #333; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Organization Type:</strong>
                        <div style="margin-top: 5px;">
                            <a href="/listing-organization-type/{{ urlencode(@$project->organization_type) }}" style="display: inline-block; background: #6c757d; color: white; padding: 4px 8px; margin: 2px 4px 2px 0; border-radius: 4px; text-decoration: none; font-size: 12px;">{{ @$project->organization_type }}</a>
                        </div>
                    </div>
                @endif

                @if(!empty($project->website_url))
                    <div class="listing-footer" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                        <a href="{{ $project->website_url }}" target="_blank" style="color: #28a745; text-decoration: none; font-size: 14px;">
                            <i class="fa fa-globe"></i> {{ $project->website_url }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach