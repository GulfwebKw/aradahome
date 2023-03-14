@php
$vendors = App\Http\Controllers\webController::shopByVendorsScrolling();
@endphp
@if(!empty($vendors) && count($vendors)>0)
 <div class="section pb_20 small_pt">
        <div class="custom-container">
            <div class="heading_tab_header">
                    <div class="heading_s2">
                        <h4>{{trans('webMessage.seller')}}</h4>
                    </div>
                </div>
            <div class="row justify-content-center">
               @foreach($vendors as $vendor)
    			@php
    			if($vendor->bgimage){
    			$bgimagevendors=url('uploads/users/'.$vendor->bgimage);
    			}else{
    			$bgimagevendors=url('uploads/users/no-image.png');
    			}
    			if($vendor->image){
    			$imagevendors=url('uploads/users/thumb/'.$vendor->image);
    			}else{
    			$imagevendors=url('uploads/users/no-image.png');
    			}
    		
    			@endphp
                <div class="col-md-3">
                    <div class="sale-banner mb-3 mb-md-4">
                        <a href="{{url(app()->getLocale().'/vendors/'.$vendor->slug)}}"
                            class="erf-link">
                            @if($vendor['name_'.app()->getLocale()] ) <div class="erf-disc">
                                {{$vendor['name_'.app()->getLocale()]}}</div> @endif
                            <img 
                                src="{{ @$imagevendors ?? url('uploads/category/no-image.png') }}"
                                alt="{{$shopcategoriesList['name_'.app()->getLocale()]}}">
                            <div class="erf-overlay"></div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

@endif

