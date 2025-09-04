@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12 margin-bottom-40">
            <h2>Adjacent Fields</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="sidebar">
                <div class="widget margin-bottom-40">
                    <h3 class="margin-top-0 margin-bottom-30">Categories</h3>
                    <ul class="list-links">
                        <li><a href="/adjacent">Adjacent Fields</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="row">
                @if($categories->count() > 0)
                    @foreach($categories as $category)
                        <div class="col-md-6 margin-bottom-30">
                            <div class="listing-item-container list-layout">
                                <a href="/listing-category/{{ $category->slug }}" class="listing-item">
                                    <div class="listing-item-content">
                                        <h3 class="listing-title">{{ $category->name }}</h3>
                                        @if($category->description)
                                            <p>{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-12">
                        <p>No categories found under Adjacent Fields.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection