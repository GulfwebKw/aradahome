@foreach( $comments as $comment)
    <li class="comment_info">
        <div class="d-flex">
            <div class="comment_user">
                @if($comment->writer and $comment->writer->image) <img src="{!! url('uploads/customers/'.$comment->writer->image) !!}" class="user2" alt=""> @endif
            </div>
            <div class="comment_content">
                <div class="d-flex">
                    <div class="meta_data">
                        <h6><a href="#">@if($comment->writer) {{ $comment->writer->name }} @else {{ $comment->name }} @endif</a></h6>
                        <div class="comment-time">{{ $comment->created_at->format('F d, Y') }}</div>
                    </div>
                    <div class="ms-auto">
                        <a href="#leaveAReply" onclick="$('#reply_To').val({{$comment->id}});" class="comment-reply"><i class="ion-reply-all"></i>{{ __('webMessage.LEAVE_A_REPLY') }}</a>
                    </div>
                </div>
                <p class="w-100" @if( $comment->is_en ) style="direction: ltr;text-align: left;" @else style="direction: rtl;text-align: right;" @endif >{!! nl2br($comment->comment) !!}</p>
            </div>
        </div>
        @if( $comment->replays->count() > 0 )
        <ul class="children">
            @component('website.weblog.comment_componnet' , ['comments' =>  $comment->replays , 'firstLevel' => false , 'level' => $level+1 ])@endcomponent
        </ul>
        @endif
    </li>
@endforeach