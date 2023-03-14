   
@if(!empty($childs) && count($childs)>0)
    @foreach($childs as $child)

        <option value="{{ $child->id }}" @if(isset($productCatId) && $productCatId == $child->id) selected @endif>
         @for ($i = 0; $i <= $level; $i++)            
         {{$ParentName}} >
         @endfor
          {{ $child->name_en }}
        </option>

            @if(!empty($child->childs) && count($child->childs)>0)

                @include('gwc.product.dropdown_childs',['ParentName'=>$child->name_en,'childs' => $child->childs,'level'=>($level+1)])

            @endif

    @endforeach
@endif
