@extends('layouts.template')

@section('styles')
@if(isset($projects) && $projects->count() > 0)
    @foreach($projects->take(3) as $project)
        @if(@$project->media->first())
            <link rel="preload" as="image" href="{{ @$project->media->first()->thumbnail_url }}">
        @endif
    @endforeach
@endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12" style="margin-bottom: 32px;">
            @include('partials.search')
        </div>
    </div>

    <div class="row">
        {{-- Left sidebar (Categories and Tags) hidden to save space and improve mobile experience --}}
        {{-- <div class="col-lg-3 col-md-4">
            <div class="sidebar">
                <!-- Categories Widget -->
                <div class="widget margin-bottom-40">
                    <h3 class="margin-top-0 margin-bottom-30">Categories</h3>
                    @include('cache.categories_cache')
                </div>

                <!-- Tags widget -->
                <div class="widget margin-bottom-40">
                    <h3 class="margin-top-0 margin-bottom-30">Tags</h3>
                    @include('cache.tags_cache')
                </div>
            </div>
        </div> --}}


        <div class="col-lg-12 col-md-12 padding-right-30">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="listings-container list-layout">
                                @include('partials.paginated-projects')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12" style="margin-top: 12px;">
                    <div id="client-pagination" style="display:none; align-items:center; justify-content: space-between; gap: 12px; flex-wrap: wrap; border-top: 1px solid #e9ecef; padding-top: 12px;">
                        <div style="display:flex; align-items:center; gap: 8px;">
                            <label for="perPage" style="margin:0; font-size: 14px; color:#495057;">Per page</label>
                            <select id="perPage" style="padding:6px 8px; border:1px solid #ced4da; border-radius:6px; background:white;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div style="display:flex; align-items:center; gap: 8px;">
                            <button id="prevPage" type="button" style="padding:6px 10px; border:1px solid #ced4da; background:white; border-radius:6px; cursor:pointer;">Prev</button>
                            <span id="pageInfo" style="min-width: 120px; text-align:center; font-size: 14px; color:#495057;">Page 1 / 1</span>
                            <button id="nextPage" type="button" style="padding:6px 10px; border:1px solid #ced4da; background:white; border-radius:6px; cursor:pointer;">Next</button>
                        </div>
                    </div>

                    <div id="server-pagination" style="margin-top: 8px;">
                        {{ $projects->withQueryString()->links() }}
                    </div>
                </div>

            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var container = document.querySelector('.listings-container');
    if (!container) return;
    var items = Array.prototype.slice.call(container.querySelectorAll('.listing-item-container'));
    if (items.length === 0) return;

    var clientPager = document.getElementById('client-pagination');
    var serverPager = document.getElementById('server-pagination');
    var perPageSelect = document.getElementById('perPage');
    var prevBtn = document.getElementById('prevPage');
    var nextBtn = document.getElementById('nextPage');
    var pageInfo = document.getElementById('pageInfo');

    // If server-side paginator is present, keep it for no-JS users only
    if (serverPager) serverPager.style.display = 'none';
    clientPager.style.display = 'flex';

    var perPage = parseInt(perPageSelect ? perPageSelect.value : '10', 10);
    var currentPage = 1;

    function totalPages() {
        return Math.max(1, Math.ceil(items.length / perPage));
    }

    function render() {
        var start = (currentPage - 1) * perPage;
        var end = start + perPage;
        for (var i = 0; i < items.length; i++) {
            var show = i >= start && i < end;
            items[i].style.display = show ? '' : 'none';
        }
        var tp = totalPages();
        if (pageInfo) pageInfo.textContent = 'Page ' + currentPage + ' / ' + tp;
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= tp;
    }

    if (perPageSelect) perPageSelect.addEventListener('change', function () {
        perPage = parseInt(this.value, 10);
        currentPage = 1;
        render();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    if (prevBtn) prevBtn.addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage -= 1;
            render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    if (nextBtn) nextBtn.addEventListener('click', function () {
        if (currentPage < totalPages()) {
            currentPage += 1;
            render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Initial render
    render();
});
</script>
@endsection