@if ((count($category->children) > 0) AND ($category->parent_id > 0))

@else
@php
if($category->link=='javascript:;'){
$link = $category->link;
}else{
$link = url('gwc/'.$category->link);
}
$chosendMenu='';
if(!empty(Session::get('parentmenu')) && Session::get('parentmenu')==$category->id){
$chosendMenu = 'kt-menu__item--open';
}
if(!empty(Session::get('childmenu')) && Session::get('childmenu')==$category->id){
$chosendMenu = 'kt-menu__item--open';
}

@endphp
    <li class="kt-menu__item  kt-menu__item--submenu putsessionmenu {{$chosendMenu}}"  aria-haspopup="true" data-ktmenu-submenu-toggle="hover" id="{{$category->id}}"><a href="{{$link}}" class="kt-menu__link @if($category->link=='javascript:;') kt-menu__toggle @endif">@if($category->parent_id==0)<i class="kt-menu__link-icon {{$category->icon}}"></i> @else <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>@endif<span class="kt-menu__link-text">{{ $category->name }}</span><i class="kt-menu__ver-arrow @if($category->link=="javascript:;") la la-angle-right  @endif "></i></a>
<div class="kt-menu__submenu ">

@endif

    @if (count($category->children) > 0)

        <ul class="kt-menu__subnav">

        @foreach($category->children as $category)

            @include('gwc.includes.menu', $category)

        @endforeach

        </ul>

    @endif
</div>
 </li>