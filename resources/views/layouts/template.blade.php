<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ @$title }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main-color.css') }}" id="colors">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    @yield('styles')

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-R1HZWFGK53"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-R1HZWFGK53');
    </script>
</head>

<body>
    <!-- Wrapper -->
    <div id="wrapper">
        <header id="header-container">
            <!-- Header -->
            <div id="header">
                <div class="container" style="width: 100% !important; max-width: 1400px; padding: 0 20px;">
                    <div class="left-side">
                        <div id="logo">
                            <a href="/"
                                style="display: flex; align-items: center; text-decoration: none;">
                                <img src="{{ asset('images/logo.png') }}" alt="Connecting Current"
                                    style="height: 50px; margin-right: 15px;">
                                {{-- <div>
                                    <h2 class="site-title" style="margin-top: 2px; margin-bottom: 0;">
                                        <span class="site-title">Connecting Current</span>
                                    </h2>
                                    <small style="font-size: .8em;">A Digital Democracy Knowledge Hub</small>
                                </div> --}}
                            </a>
                        </div>
                        <!-- Mobile Navigation
                        <div class="mmenu-trigger">
                            <button class="hamburger hamburger--collapse" type="button">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                                <span class="visually-hidden">Toggle navigation</span>
                            </button>
                        </div> -->
                        <div class="clearfix"></div>
                    </div>

                    <div class="right-side">
                        <div class="header-widget">
                            <div style="padding-right: 25px; font-size: 19px;">
                                <a href="https://gaggle.email/join/knowledgehub@gaggle.email" target="_blank"
                                    class="@if (@$menu == 'about') active @endif" style="margin-right: 10px;">
                                    Subscribe
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>
        <div class="clearfix"></div>

        {{-- Titlebar removed as it's not being used and takes up too much space --}}
        {{-- @if (@$template != 'map')
            <div id="titlebar" class="gradient" style="margin-bottom: 1px;"></div>
        @else
            <div>&nbsp;</div>
        @endif --}}

        <div class="container" style="width: 100%; max-width: 1400px; padding: 0 20px; margin-top: 24px; margin-bottom: 40px;">
            @yield('content')
        </div>

        @include('layouts.partials.footer')

        <div id="backtotop"><a href="#"><span class="visually-hidden">Back to Top</span></a></div>
    </div>


    <script type="text/javascript" src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-migrate-3.1.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/mmenu.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/chosen.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/slick.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/rangeslider.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/magnific-popup.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/waypoints.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/counterup.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/tooltips.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/image-optimization.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
    </script>

    <script>
    // Generic client-side pagination for any listings container
    (function() {
        function initClientPagination(root) {
            var container = root.querySelector('.listings-container');
            if (!container) return false;
            var items = Array.prototype.slice.call(container.querySelectorAll('.listing-item-container'));
            if (!items.length) return false;

            // Avoid double-initialization
            if (container.__clientPagerInitialized) return true;
            container.__clientPagerInitialized = true;

            // Build controls
            var controls = document.createElement('div');
            controls.style.display = 'flex';
            controls.style.alignItems = 'center';
            controls.style.justifyContent = 'space-between';
            controls.style.gap = '12px';
            controls.style.flexWrap = 'wrap';
            controls.style.borderTop = '1px solid #e9ecef';
            controls.style.paddingTop = '12px';
            controls.className = 'client-pagination-controls';
            controls.innerHTML = ''+
                '<div style="display:flex; align-items:center; gap: 8px;">\
                    <label style="margin:0; font-size:14px; color:#495057;">Per page</label>\
                    <select class="client-per-page" style="padding:6px 8px; border:1px solid #ced4da; border-radius:6px; background:white;">\
                        <option value="5">5</option>\
                        <option value="10" selected>10</option>\
                        <option value="20">20</option>\
                        <option value="50">50</option>\
                    </select>\
                </div>\
                <div style="display:flex; align-items:center; gap: 8px;">\
                    <button type="button" class="client-prev" style="padding:6px 10px; border:1px solid #ced4da; background:white; border-radius:6px; cursor:pointer;">Prev</button>\
                    <span class="client-pageinfo" style="min-width:120px; text-align:center; font-size:14px; color:#495057;">Page 1 / 1</span>\
                    <button type="button" class="client-next" style="padding:6px 10px; border:1px solid #ced4da; background:white; border-radius:6px; cursor:pointer;">Next</button>\
                </div>';

            // Insert controls after container
            if (container.parentNode) {
                container.parentNode.insertBefore(controls, container.nextSibling);
            }

            // Hide any server paginator nearby (only after init)
            var serverPaginators = root.querySelectorAll('.pagination, #server-pagination');
            serverPaginators.forEach(function(el){ el.style.display = 'none'; });

            var perPageSelect = controls.querySelector('.client-per-page');
            var prevBtn = controls.querySelector('.client-prev');
            var nextBtn = controls.querySelector('.client-next');
            var pageInfo = controls.querySelector('.client-pageinfo');

            var perPage = parseInt(perPageSelect ? perPageSelect.value : '10', 10);
            var currentPage = 1;

            function totalPages() { return Math.max(1, Math.ceil(items.length / perPage)); }

            function render() {
                var start = (currentPage - 1) * perPage;
                var end = start + perPage;
                for (var i = 0; i < items.length; i++) {
                    items[i].style.display = (i >= start && i < end) ? '' : 'none';
                }
                var tp = totalPages();
                if (pageInfo) pageInfo.textContent = 'Page ' + currentPage + ' / ' + tp;
                if (prevBtn) prevBtn.disabled = currentPage <= 1;
                if (nextBtn) nextBtn.disabled = currentPage >= tp;
            }

            if (perPageSelect) perPageSelect.addEventListener('change', function(){
                perPage = parseInt(this.value, 10);
                currentPage = 1;
                render();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            if (prevBtn) prevBtn.addEventListener('click', function(){
                if (currentPage > 1) { currentPage--; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
            });
            if (nextBtn) nextBtn.addEventListener('click', function(){
                if (currentPage < totalPages()) { currentPage++; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
            });

            render();
            return true;
        }

        document.addEventListener('DOMContentLoaded', function(){
            // Try to initialize using the closest major content area
            var roots = document.querySelectorAll('.container, .row, body');
            var initialized = false;
            for (var i = 0; i < roots.length; i++) {
                if (initClientPagination(roots[i])) { initialized = true; break; }
            }
        });
    })();
    </script>

    <script type="text/javascript">
        var path = "{{ route('autocomplete') }}";

        /*$('input.typeahead').typeahead({
            displayKey: 'name',
            source:  function (query, process) {
                return $.get(path, { query: query }, function (data) {
                    return process(data);
                });
            },
            afterSelect: function (data) {
                window.location.replace("/listings/search?q="+data.name);
            }
            
        }); */

        $('input.typeahead').typeahead({
            displayKey: 'name',
            source: function(query, process) {
                return $.get(path, {
                    query: query
                }, function(data) {
                    return process(data);
                });
            },
            afterSelect: function(data) {
                $.LoadingOverlay("show");
                //window.location.replace("/listings/search?q="+data.name);
                window.location.replace("/listing/" + data.slug);
            }

        }).keydown(function(event) {
            if (event.which == 13) {
                $(this).blur();
                $(this).focus();
                var q = $(".typeahead").val();

                $.LoadingOverlay("show");
                window.location.replace("/listings/search?q=" + q);
                return false;
            }
        });


        $('#search').click(function(e) {
            var param = $(".typeahead").val();
            $.LoadingOverlay("show");
            //window.location.href  = "/listings/search?q="+param;
        });

        function myFunction() {
            var dots = document.getElementById("dots");
            var moreText = document.getElementById("more");
            var linkText = document.getElementById("readMore");

            if (dots.style.display === "none") {
                dots.style.display = "inline";
                linkText.innerHTML = "&nbsp;&nbsp;[++ Expand ++]";
                moreText.style.display = "none";
            } else {
                dots.style.display = "none";
                linkText.innerHTML = "&nbsp;&nbsp;[ -- Collapse --]";
                moreText.style.display = "inline";
            }
        }

        $("#search").click(function() {
            $.LoadingOverlay("show");
        });

        $("#filter").click(function() {
            $.LoadingOverlay("show");
        });

        $(".overlay").click(function() {
            $.LoadingOverlay("show");
        });
    </script>

    @yield('scripts')

</body>

</html>
