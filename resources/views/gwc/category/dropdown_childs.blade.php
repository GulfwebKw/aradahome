   

    @foreach($childs as $child)

        <option value="{{ $child->id }}" {{!empty($editcategory->parent_id) && $editcategory->parent_id==$child->id?'selected':''}}>
         @for ($i = 0; $i <= $level; $i++)            
         -
         @endfor
          {{ $child->name_en }}</option>

            @if(count($child->childs))

                @include('gwc.category.dropdown_childs',['childs' => $child->childs,'level'=>($level+1),'editcategory'=>!empty($editcategory) && $editcategory?$editcategory:[]])

            @endif

    @endforeach

