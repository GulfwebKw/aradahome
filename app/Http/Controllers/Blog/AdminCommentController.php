<?php

namespace App\Http\Controllers\Blog;

use App\BlogPost;
use App\BlogComments;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;
use File;
use Auth;
use App\Http\Controllers\Common;

class AdminCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $comments = BlogComments::when($request->q , function ($query) use ($request){
                $words = explode(' ' , $request->q );
                $query->where(function ($query) use($words){
                    foreach ( $words as $word)
                        $query->orWhere('comment' , 'like' , '%'.$word.'%')
                            ->orWhere('name' , 'like' , '%'.$word.'%')
                            ->orWhere('status' , 'like' , '%'.$word.'%');
                });
            })
            ->when($request->p , function ($query) use ($request){
                 $query->Where('post_id' , $request->p );
            })
            ->latest()
            ->with(['writer' , 'post'] )
            ->paginate();

        return response()->view('gwc.blog.comments' , compact('comments'));
    }
    
    public function status(Request $request, $id)
	{
	
		try {

			$comment = BlogComments::findOrFail($id);
			$comment->status = $request->status;
			$comment->verifier_id = Auth::guard('admin')->user()->id;
			$comment->save();


			//save logs
			$key_name   = "Comment";
			$key_id     = $comment->id;
			$message    = "Status for comment is updated to ".$request->status.". (" . $comment->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

            return ['status' => 200, 'message' => 'Status is modified successfully'];
		} catch (\Exception $e) {
			 return ['status' => 500, 'message' => $e->getMessage()];
		}
	}
}