<div class="col-lg-4 col-sm-6">

    @foreach($allowed_module as $module)
        @if($module!=="")
            <div style="width: 100px; display: inline-block">
                <div class="thumb" >
                    <a href="#" class="{{$module}}-default module" data-module-type="{{$module}}"> <img src="http://demo.interface.club/limitless/layout_1/LTR/default/assets/images/demo/flat/11.png" alt=""></a>

                </div>

                <div class="caption">
                    <h6 class="no-margin"><a href="#" class="{{$module}}-default module" data-module-type="{{$module}}">{{$module}}</a></h6>
                </div>
            </div>
        @endif
    @endforeach

</div>




