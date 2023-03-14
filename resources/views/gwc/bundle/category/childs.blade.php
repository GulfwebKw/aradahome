<div class="kt-list-timeline__items">

    @foreach($childs as $child)
         
         <div class="kt-list-timeline__item">
         @for ($i = 0; $i <= $level; $i++)
         <span class="kt-list-timeline__badge"></span>
         @endfor
         <span class="kt-list-timeline__text"><a href="{{url('gwc/product?bundle_category='.$child->id)}}">{{$child->name_en}}({{count($child->allproducts)}})</a></span>
         
         <span class="kt-list-timeline__time">
         @if($child->image)
         <img src="{!! url('uploads/bundle_category/thumb/'.$child->image) !!}" width="35">
         @endif
         </span>
         <span class="kt-list-timeline__time">
         <span class="kt-switch"><label><input value="{{$child->id}}" {{!empty($child->is_active)?'checked':''}} type="checkbox"  id="bundles/category" class="change_status"><span></span></label></span>
         </span>
         <span class="kt-list-timeline__time">{{$child->display_order}}</span>
         <span class="kt-list-timeline__time">
         <!--action-->
          <span style="overflow: visible; position: relative; width: 80px;">
                                                 <div class="dropdown">
                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>
                                                 <div class="dropdown-menu dropdown-menu-right">
                                                 <ul class="kt-nav">
                                                 @if(auth()->guard('admin')->user()->can('bundle-category-edit'))
                                                 <li class="kt-nav__item"><a href="{{url('gwc/bundles/category/'.$child->id.'/edit')}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-contract"></i><span class="kt-nav__link-text">{{__('adminMessage.edit')}}</span></a></li>
                                                 @endif
                                                 @if(auth()->guard('admin')->user()->can('bundle-category-view'))
                                                 <li class="kt-nav__item"><a href="{{url('gwc/bundles/category/'.$child->id.'/view')}}" class="kt-nav__link"><i class="kt-nav__link-icon la la-eye"></i><span class="kt-nav__link-text">{{__('adminMessage.view')}}</span></a></li>
                                                 @endif
                                                 @if(auth()->guard('admin')->user()->can('bundle-category-delete'))
                                                 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$child->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text">{{__('adminMessage.delete')}}</span></a></li>
                                                 @endif
                                                 </ul>
                                                 </div>
                                                 </div>
                                                 </span>
                                                 
                                                 <!--Delete modal -->
                          <div class="modal fade" id="kt_modal_{{$child->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">{{__('adminMessage.alert')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body">
											<h6 class="modal-title text-left">{!!__('adminMessage.alertDeleteMessage')!!}</h6>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.no')}}</button>
											<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('gwc/bundles/category/delete/'.$child->id)}}'">{{__('adminMessage.yes')}}</button>
										</div>
									</div>
								</div>
							</div>
         <!--end action -->
         </span>
         </div>
<div class="kt-separator kt-separator--space-sm kt-separator--border-dashed"></div>
            @if(count($child->childs))
                @include('gwc.bundle.category.childs',['childs' => $child->childs,'level'=>($level+1)])
            @else    
            
            @endif
              
    @endforeach
</div>