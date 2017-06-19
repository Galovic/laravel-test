<div class="col-xs-12">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title text-semibold">Návštěvnost webu</h6>
        </div>

        <div class="panel-body" id="charts-body" style="display: none">
            <div id="ga-sessions-chart" style="width: 100%; height: 300px;"></div>
            <hr>
            <div class="col-md-6 col-xs-12">
                <div class="row">
                    {{-- Visits / Sesions --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                Návštěvy
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-visits-chart" style="width: 100%; height: 25px;"></div>
                            </div>
                        </div>

                    </div>

                    {{-- Users --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                Uživatelé
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-users-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Pageviews --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                Zobrazení stránek
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-pageviews-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Views per session --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                Počet stránek na 1 návštěvu
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-viewsPerSession-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Bouncer Rate --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                Míra okamžitého opuštění
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-BounceRate-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Organic searches --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                Organických vyhledání
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-organicSearches-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div id="ga-returningUsers-chart" style="width: 100%; height: 300px;"></div>
            </div>

        </div>
    </div>
</div>

@section('breadcrumb-elements')
    <li>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-cog"></i> Nastavení <span class="caret"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="{{ route('admin.dashboard.profiles') }}">Přepnout GA profil</a></li>
            <li role="separator" class="divider"></li>
            <li>
                <a href="{{ route('admin.dashboard.off') }}" class="automatic-post">
                    Odpojit Google Analytics
                </a>
            </li>
        </ul>
    </li>
@endsection

@push('script')
{{--{{ Html::script(asset('js/c3d3.js')) }}--}}

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    var dashboardUrls = {
        chartDataUrl: "{{ route('admin.dashboard.chartData') }}"
    };
</script>

{{ Html::script(elixir('js/dashboard.page.js')) }}
@endpush