@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class="col-md-6 col-md-offset-3">

            <div class="panel panel-flat" id="ga-profiles-list">

                <div class="panel-heading">
                    <h5 class="panel-title">Vyberte profil</h5>
                </div>

                <div class="col-xs-12">
                    Zvolte profil, jehož data chcete vidět na dashboardu.
                    <br>
                    <label class="alert">
                        <input checked="checked" name="enable_tracking" type="checkbox" value="1" v-model="enableTracking">
                        Přidat do stránky měřící kód
                    </label>
                </div>

                <div class="clearfix"></div>

                <ul class="media-list media-list-linked">

                    @foreach($profilesList as $profile)
                    <li class="media">
                        <a href="#" class="media-link" @click.prevent="selectProfile('{{ $profile->accountId }}', '{{ $profile->property }}', '{{ $profile->id }}')">
                            <div class="media-body">
                                <div class="media-heading text-semibold">
                                    {{ $profile->name }}
                                </div>
                                <span class="text-muted">{{ $profile->url }}</span>
                            </div>
                            <div class="media-right media-middle">
                                <span class="label label-primary">{{ $profile->property }}</span>
                            </div>
                        </a>
                    </li>
                    @endforeach

                </ul>
            </div>

        </div>
    </div>
@endsection

@push('script')
<script>
    new Vue({
        el: '#ga-profiles-list',
        data: {
            enableTracking: true
        },
        methods: {
            selectProfile: function (accountId, propertyId, profileId) {
                var $list = $(this.$el);

                if (!$list.lock({spinner: SpinnerType.OVER})) {
                    return false;
                }

                $.ajax({
                    url: "{{ route('admin.dashboard.profiles') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        accountId: accountId,
                        propertyId: propertyId,
                        profileId: profileId,
                        enableTracking: this.enableTracking * 1
                    }
                }).done(function (response) {
                    if (response.refresh) {
                        window.location.reload(true);
                    } else if (response.redirect) {
                        window.location = response.redirect;
                    }
                }).always(function(){
                    $list.unlock();
                });
            }
        }
    });
</script>
@endpush