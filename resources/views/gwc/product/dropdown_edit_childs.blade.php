   
@if(!empty($childs) && count($childs)>0)
    @foreach($childs as $child)

        <option value="{{ $child->id }}" @if($child->id==$listCategory->category_id) selected @endif >
         @for ($i = 0; $i <= $level; $i++)            
         {{$ParentName}} >
         @endfor
          {{ $child->name_en }}</option>

            @if(!empty($child->childs) && count($child->childs))

                @include('gwc.product.dropdown_edit_childs',['ParentName'=>$child->name_en,'childs' => $child->childs,'level'=>($level+1),'listCategory'=>$listCategory])

            @endif

@endforeach
@endif

