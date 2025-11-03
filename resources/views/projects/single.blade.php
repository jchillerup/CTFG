@extends('layouts.template')

@section('styles')
    <style type="text/css">
        div iframe {
            width: 100%;
        }

        /* Responsive layout improvements */
        .sticky-wrapper {
            max-width: 100%;
            margin: 0 auto;
        }


        .listing-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e9ecef;
            transition: box-shadow 0.3s ease;
        }
        
        .listing-section:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .additional-info-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e9ecef;
            transition: box-shadow 0.3s ease;
        }
        
        .additional-info-section:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .additional-info-section h3 {
            margin-bottom: 18px;
            color: #212529;
            font-size: 1.5em;
            font-weight: 600;
        }

        .founders-section,
        .contact-section,
        .resources-section {
            margin-bottom: 24px;
        }

        .founders-section h3,
        .contact-section h3,
        .resources-section h3 {
            margin-bottom: 12px;
            color: #212529;
            font-size: 1.35em;
            font-weight: 600;
        }

        /* Content spacing inside cards */
        .listing-section > *,
        .additional-info-section > * {
            margin-bottom: 10px;
        }

        .listing-section > *:last-child,
        .additional-info-section > *:last-child {
            margin-bottom: 0;
        }

        .share-buttons {
            list-style: none;
            padding: 0;
        }

        .share-buttons li {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 10px;
        }

        .share-buttons a {
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            transition: background 0.3s ease;
        }

        .share-buttons a:hover {
            background: #e9ecef;
        }

        /* Content spacing inside cards */
        .listing-section > *,
        .additional-info-section > * {
            margin-bottom: 10px;
        }

        .listing-section > *:last-child,
        .additional-info-section > *:last-child {
            margin-bottom: 0;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .sticky-wrapper {
                padding: 0 15px;
            }

            .listing-section,
            .additional-info-section {
                padding: 10px;
                margin-bottom: 15px;
            }

            .col-sm-12 {
                padding-right: 15px;
                padding-left: 15px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .table-responsive th {
                min-width: 120px;
            }

            /* Mobile-specific listing item styles */
            .listing-item-container {
                padding: 15px !important;
                margin-bottom: 20px !important;
            }

            .listing-item {
                display: block !important;
                flex-wrap: unset !important;
            }

            .listing-item-image {
                flex: none !important;
                width: 100% !important;
                margin-right: 0 !important;
                margin-bottom: 15px !important;
                min-height: 120px !important;
            }

            .listing-item-content {
                flex: none !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .listing-title h4 {
                font-size: 18px !important;
                line-height: 1.4 !important;
            }

            .listing-title h4 a {
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }

            /* Mobile link styles */
            .listing-footer a {
                font-size: 12px !important;
                word-break: break-all !important;
                display: block !important;
                margin-top: 10px !important;
            }

            /* Mobile badge styles */
            .listing-item-content span {
                font-size: 11px !important;
                padding: 3px 6px !important;
                margin: 1px 2px 1px 0 !important;
            }

            /* Mobile table responsiveness */
            .table-responsive {
                font-size: 12px;
            }

            .table-responsive th,
            .table-responsive td {
                padding: 8px 4px;
                word-break: break-word;
            }

            .table-responsive th {
                min-width: 80px;
                font-size: 11px;
            }
        }

        /* Tablet responsiveness */
        @media (min-width: 769px) and (max-width: 1024px) {
            .sticky-wrapper {
                padding: 0 20px;
            }

            .listing-section,
            .additional-info-section {
                padding: 15px;
            }
        }

        /* Desktop improvements */
        @media (min-width: 1025px) {
            .sticky-wrapper {
                max-width: 1400px;
                margin: 0 auto;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row sticky-wrapper">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <!-- Project Title Section -->
            <div class="listing-section">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        @if (@$project->media->first())
                            <img src="{{ @$project->media->first()->display_url }}" loading="lazy"
                                alt="Graphic representing {!! $project->name !!}" style="max-width: 100%; height: auto; border-radius: 6px;">
                        @else
                            <img src="{{ asset('images/gray.png') }}" loading="lazy"
                                alt="Graphic representing {!! $project->name !!}" style="max-width: 100%; height: auto; border-radius: 6px;">
                        @endif
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <h1 style="margin-top: 0; margin-bottom: 18px; color: #212529; font-size: 2em; font-weight: 700; line-height: 1.3;">{!! $project->name !!}</h1>
                        @if (!empty($project->website_url))
                            <p style="margin-bottom: 10px;">
                                <a href="{{ @$project->website_url }}" target="_blank" rel="noopener noreferrer" style="color: #28a745; text-decoration: none;">
                                    <i class="fa fa-globe"></i>
                                    {{ @$project->website_url }}
                                </a>
                            </p>
                        @endif
                        @if ($project->location->count() > 0)
                            <p style="margin-bottom: 10px; color: #333;">
                                <i class="fa fa-map-marker"></i>
                                {{ @$project->location->first()->name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div id="listing-overview" class="listing-section">
                @if ($project->tags->count() > 0)
                    <div style="margin-bottom: 24px;">
                        <strong style="color: #495057; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; display: block; margin-bottom: 10px;">Tags:</strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach ($project->tags as $tag)
                                <a href="/listing-tag/{{ @$tag->name }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #d1e7dd; color: #0a3622; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #badbcc;" onmouseover="this.style.background='#badbcc'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#d1e7dd'; this.style.transform='translateY(0)'">{{ @$tag->name }}</a>
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
                    <div style="margin-bottom: 24px;">
                        <strong style="color: #495057; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; display: block; margin-bottom: 10px;">Languages:</strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach ($languages as $lang)
                                <a href="/listing-language/{{ urlencode($lang) }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background: #cfe2ff; color: #084298; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s ease; border: 1px solid #9ec5fe;" onmouseover="this.style.background='#9ec5fe'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#cfe2ff'; this.style.transform='translateY(0)'">{{ $lang }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="margin-top-35">
                    <p>
                        {!! $project->introduction !!}
                    </p>
                    @if (!empty($project->description))
                        <p class="listing-description">
                            {{ Illuminate\Mail\Markdown::parse($project->description) }}
                        </p>
                    @endif
                </div>

                <div class="margin-top-35">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
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
                                    <tr>
                                        <th>{{ count($organizations) > 1 ? 'Organizations:' : 'Organization:' }} </th>
                                        <td>
                                            @foreach($organizations as $org)
                                                @if(!$loop->first), @endif
                                                <a href="/listing-organization/{{ $org->id }}" target="_blank" rel="noopener noreferrer" style="color: #0A78C2;">{{ $org->name }}</a>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->organization_type))
                                    <tr>
                                        <th>Organization Type: </th>
                                        <td>{{ $project->organization_type }}</td>
                                    </tr>
                                @endif
                                {{-- Status hidden from listing view --}}
                                {{-- @if (!empty(@$project->status))
                                    <tr>
                                        <th>Status: </th>
                                        <td>{{ $project->status }}</td>
                                    </tr>
                                @endif --}}
                                @if ($project->links->count() > 0)
                                    <tr>
                                        <th>Related Links: </th>
                                        <td>
                                            <ul>
                                                @foreach (@$project->links as $link)
                                                    <li>
                                                        <a style="color: #0A78C2;" target="_blank" rel="noopener noreferrer"
                                                            href="{{ $link->link }}">
                                                            {{ $link->notes }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endif
                                {{-- @if (!empty(@$project->email))
                            <tr><th>Email: </th><td>{{ $project->email }}</td></tr>
                        @endif --}}

                                @if (!empty(@$project->founded))
                                    <tr>
                                        <th>Founded: </th>
                                        <td>{{ $project->founded }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->closed))
                                    <tr>
                                        <th>Closed: </th>
                                        <td>{{ $project->closed }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->shutdown_reason))
                                    <tr>
                                        <th>If shutdown, what happened?: </th>
                                        <td>{{ $project->shutdown_reason }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->postmortem))
                                    <tr>
                                        <th>Postmortem: </th>
                                        <td>{{ $project->postmortem }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->parent_id))
                                    <tr>
                                        <th>Parent Organization: </th>
                                        <td><a style="color: #0A78C2;" target="_blank" rel="noopener noreferrer"
                                                href="/listing/{{ $project->parent->slug }}">{{ $project->parent->name }}</a>
                                        </td>
                                    </tr>
                                @endif
                                @if (@$project->children->count() > 0)
                                    <tr>
                                        <th>Project(s): </th>
                                        <td>
                                            @foreach ($project->children as $child)
                                                <a style="color: #0A78C2;" target="_blank" rel="noopener noreferrer" href="/listing/{{ $child->slug }}">
                                                    {{ $child->name }}
                                                </a>
                                                @if ($project->children->last()->id != $child->id)
                                                    ,&nbsp;
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->open_source))
                                    <tr>
                                        <th>Open Source: </th>
                                        <td>{{ $project->open_source }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->open_source_license))
                                    <tr>
                                        <th>Open Source License: </th>
                                        <td>{{ $project->open_source_license }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->features))
                                    <tr>
                                        <th>Features: </th>
                                        <td>{{ $project->features }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->project_stage))
                                    <tr>
                                        <th>Project Stage: </th>
                                        <td>{{ $project->project_stage }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->used_by))
                                    <tr>
                                        <th>Who's it used by?: </th>
                                        <td>{{ $project->used_by }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->pricing_information))
                                    <tr>
                                        <th>Pricing information: </th>
                                        <td>{{ $project->pricing_information }}</td>
                                    </tr>
                                @endif
                                @if (!empty(@$project->no_of_employees))
                                    <tr>
                                        <th>Number of employees: </th>
                                        <td>{{ $project->no_of_employees }}</td>
                                    </tr>
                                @endif
                                {{-- Last Modified date hidden from listing view --}}
                                {{-- @if (!empty(@$project->last_modified))
                                    <tr>
                                        <th>Last Modified: </th>
                                        <td>{{ Carbon\Carbon::parse(@$project->last_modified)->format('n/j/Y') }}</td>
                                    </tr>
                                @endif --}}
                                @if (!empty(@$project->created))
                                    <tr>
                                        <th>Added on: </th>
                                        <td>{{ Carbon\Carbon::parse(@$project->created)->format('n/j/Y') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($project->categories->count() > 0)
                    <h3 class="listing-desc-headline">Project Categories</h3>
                    <ul class="listing-features" style="list-style: inherit; padding-left: 30px;">
                        @foreach ($project->categories as $category)
                            <li><a style="color: #0A78C2;" target="_blank" rel="noopener noreferrer"
                                    href="/listing-category/{{ $category->slug }}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                @endif

                <div class="clearfix"></div>
            </div>

            <!-- Slider -->
            @if ($project->media->count() > 1)
                <div id="listing-gallery" class="listing-section">
                    <h3 class="listing-desc-headline margin-top-70">Additional Images</h3>
                    <div class="listing-slider-small mfp-gallery-container margin-bottom-0">
                        @foreach ($project->media as $fi)
                            <a href="{{ $fi->display_url }}" data-background-image="{{ $fi->display_url }}"
                                class="item mfp-gallery" title="Featured image"></a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (@$project->funding->count() > 0)
                <div id="add-review" class="add-review-box">
                    <h3 class="listing-desc-headline margin-bottom-10">Funding Details</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Funded By</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($project->funding as $fng)
                                    <tr>
                                        <td>{{ @$fng->funded_by }}</td>
                                        <td>
                                            @if (!empty($fng->link))
                                                <a href="{{ @$fng->link }}" target="_blank" style="color: blue;">
                                                    {{ @$fng->funding_date }}
                                                </a>
                                            @else
                                                {{ @$fng->funding_date }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$fng->amount > 0)
                                                $ {{ @$fng->amount }}
                                            @else
                                                &nbsp;
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endif

            @if (@$project->impact->count() > 0)
                <div id="add-review" class="add-review-box" style="margin-top: 10px; background-color: #fcfcfc;">
                    <h3 class="listing-desc-headline margin-bottom-10">Evidence of this project's impact:</h3>
                    @foreach ($project->impact as $impactItem)
                        @if (!empty($impactItem->statement))
                            <div style="margin-bottom: 15px;">
                                <p>
                                    {{ $impactItem->statement }}
                                    @if (!empty($impactItem->url))
                                        (<a href="{{ $impactItem->url }}" target="_blank" rel="noopener noreferrer" style="color: #0A78C2;">Source</a>,
                                    @endif
                                    @if (!empty($impactItem->impact_date))
                                        <a href="{{ $impactItem->impact_date }}" target="_blank" rel="noopener noreferrer">
                                            {{ $impactItem->impact_date }}
                                        </a>)
                                    @else
                                        @if (!empty($impactItem->url))
                                            )
                                        @endif
                                    @endif
                                </p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="margin-top-50" style="max-height: 600px; overflow-y: scroll;">
                @if (!empty($project->has_iframe_embed))
                    {!! $project->has_iframe_embed !!}
                @endif
            </div>
            {{-- <div class="margin-top-50" style="max-height: 600px; overflow-y: scroll;">
            @if (!empty($project->has_twitter_feed))
                {!! $project->has_twitter_feed !!}
            @endif
        </div> --}}

        </div>


        <!-- Additional Information Section -->
        @php
            $hasFounders = @$project->founders->count() > 0;
            $hasSocialLinks = !empty($project->facebook_url) || !empty($project->twitter_url) || !empty($project->instagram_url);
            $hasContactForm = !empty(@$project->contact_form_email);
            $hasResources = !empty(@$project->linkedin_url) ||
                !empty(@$project->youtube_channel) ||
                !empty(@$project->contact_page_url) ||
                !empty(@$project->github_url) ||
                !empty(@$project->events_page_url) ||
                !empty(@$project->jobs_page_url) ||
                !empty(@$project->blog_url) ||
                !empty(@$project->host_organization_url) ||
                !empty(@$project->parent_id);
            $hasAdditionalInfo = $hasFounders || $hasSocialLinks || $hasContactForm || $hasResources;
        @endphp
        @if ($hasAdditionalInfo)
        <div class="col-lg-12 col-md-12 col-sm-12 margin-top-50">
            
            <div class="additional-info-section">
                @if ($hasFounders)
                    <div class="founders-section">
                        <h3>Founder(s)</h3>
                        <ul style="padding-left: 30px;">
                            @foreach ($project->founders as $founder)
                                <li><span style="color: #000;">{{ @$founder->name }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($hasSocialLinks)
                <ul class="share-buttons margin-top-40 margin-bottom-0">
                    @if (!empty($project->facebook_url))
                        <li>
                            <a class="fb-share" target="_blank" href="{{ $project->facebook_url }}"><i
                                    class="fa fa-facebook"></i> Facebook</a>
                        </li>
                    @endif
                    @if (!empty($project->twitter_url))
                        <li>
                            <a class="twitter-share" target="_blank" href="{{ $project->twitter_url }}"><i
                                    style="color: #1da1f2" class="fa fa-twitter"></i> Twitter</a>
                        </li>
                    @endif
                    @if (!empty($project->instagram_url))
                        <li>
                            <a class="instagram-share" target="_blank" href="{{ $project->instagram_url }}"><i
                                    class="fa fa-instagram"></i> Instagram</a>
                        </li>
                    @endif
                </ul>
                @endif



                @if ($hasContactForm)
                    <div class="contact-section margin-top-35">
                        <h3>Contact {{ $project->name }}</h3>
                        <form action="/listing-contact-form" method="POST">
                            @csrf
                            <div class="row">
                                @if (Session::has('success'))
                                    <div class="col-12">
                                        <div class="alert alert-success" style="padding: 10px 25px; color: #006600;">
                                            <p style="color: #006600 !important;">
                                                {{ Session('success') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                @if ($errors->any())
                                    <div class="col-12" style="color: green;">
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="email" id="email" type="email"
                                            onfocus="this.placeholder = ''"
                                            onblur="this.placeholder = 'Enter email address'" placeholder="Email" />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control" name="message" id="msg" rows="4" onfocus="this.placeholder = ''"
                                            onblur="this.placeholder = 'Enter Message'" placeholder="Enter Message"></textarea>
                                    </div>
                                </div>

                                <div class="form-group mt-3 col-9">
                                    @if (config('services.recaptcha.key'))
                                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') }}">
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group mt-3 col-3 text-right" style="margin-top: -60px;">
                                    <input type="hidden" name="recipient" id="recipient"
                                        value="{{ $project->contact_form_email }}">
                                    <input type="hidden" name="slug" id="slug" value="{{ $project->slug }}">
                                    <button type="submit" class="button button-contactForm boxed-btn">Send</button>
                                </div>
                                <div class="mt-3 col-12">
                                    <p style="margin-top: 30px; font-size: 12px; line-height: 20px;">
                                        By hitting "Send", you agree that the Connecting Current will share your email
                                        address and message with {{ $project->name }}. {{ $project->name }} has agreed to
                                        receive messages via this form but may not be able to reply to every message. This
                                        service does not imply any affiliation between {{ $project->name }} and the
                                        Connecting Current. You will not be signed up for anything.
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                @if ($hasResources)
                    <div class="resources-section margin-top-35">
                        <h3>Resources</h3>
                        <ul>
                            @if (!empty(@$project->linkedin_url))
                                <li>LinkedIn: <span><a href="{{ @$project->linkedin_url }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->linkedin_url }}</a></span></li>
                            @endif
                            @if (!empty(@$project->youtube_channel))
                                <li>Youtube: <span><a href="{{ @$project->youtube_channel }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->youtube_channel }}</a></span></li>
                            @endif
                            @if (!empty(@$project->contact_page_url))
                                <li>Contact page: <span><a href="{{ @$project->contact_page_url }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->contact_page_url }}</a></span></li>
                            @endif
                            @if (!empty(@$project->github_url))
                                <li>Github: <span><a href="{{ @$project->github_url }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->github_url }}</a></span></li>
                            @endif
                            @if (!empty(@$project->events_page_url))
                                <li>Events page: <span><a href="{{ @$project->events_page_url }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->events_page_url }}</a><span></li>
                            @endif
                            @if (!empty(@$project->jobs_page_url))
                                <li>Jobs page: <span><a href="{{ @$project->jobs_page_url }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->jobs_page_url }}</a></span></li>
                            @endif
                            @if (!empty(@$project->blog_url))
                                <li>Blog: <span><a href="{{ @$project->blog_url }}"
                                            target="_blank" rel="noopener noreferrer">{{ @$project->blog_url }}</a></span></li>
                            @endif
                            @if (!empty(@$project->parent_id))
                                <li>Parent Org: <span>                                                <a style="color: #0A72B8;" target="_blank" rel="noopener noreferrer"
                                            href="/listing/{{ $project->parent->slug }}">{{ @$project->parent->name }}</a></span>
                                </li>
                            @endif
                            {{-- @if (!empty(@$project->host_organization_url))
                            <li>Host Org Url: <span><a href="{{ @$project->host_organization_url }}" target="_blank">{{ @$project->host_organization_url }}</a></span></li>
                        @endif --}}
                        </ul>
                    </div>
                @endif

                {{-- @if ($project->links->count() > 0)
                <div class="boxed-widget opening-hours margin-top-35" style="text-align: left;">
                    <h3>Links</h3>
                    <ol>
                        @foreach ($project->links as $link)
                            <li>
                                <h5>Link:</h5> <span><a href="{{ @$link->link }}" target="_blank">{{ @$link->link }}</a></span>
                            </li>
                           <br/>
                            <h5>Notes:</h5> <span>{{ $link->notes }}</span>
                            
                        @endforeach
                    </ol>
                </div>
            @endif --}}

                <div class="clearfix"></div>
            </div>
        </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endsection
