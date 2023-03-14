<?php

namespace App\Http\Controllers\Blog;

use App\BlogCategory;
use App\BlogComments;
use App\BlogPost;
use App\BlogTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PostController extends Controller
{

    public function __construct()
    {
        if ( \request()->method() != "POST") {
            $lastPost = BlogPost::where('status', 'published')
                ->latest()
                ->take(3)
                ->get();
            $lastComment = BlogComments::where('status', 'published')
                ->latest()
                ->with('post')
                ->take(2)
                ->get();
            $archive = BlogPost::where('status', 'published')
                ->select(DB::raw('YEAR(created_at) year, MONTH(created_at) month, MONTHNAME(created_at) month_name, COUNT(*) post_count'))
                ->groupBy('year')
                ->groupBy('month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            View::share('lastPost', $lastPost);
            View::share('lastComment', $lastComment);
            View::share('archive', $archive);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ( intval($request->p) ){
            $blogPost = BlogPost::findOrFail($request->p);
            if( $blogPost->status != 'published' and ! auth('admin')->check() )
                abort(404);
            return redirect()->route('blog.show' , [$request->countrySubDomainCode , $request->getLocale() ,$blogPost->id , $blogPost->slug ]);
        }
        $posts = BlogPost::where('status' , 'published')
            ->when($request->q , function ($query) use ($request){
                $words = explode(' ' , $request->q );
                $query->where(function ($query) use($words){
                    foreach ( $words as $word)
                        $query->orWhere('title_en' , 'like' , '%'.$word.'%')
                            ->orWhere('title_ar' , 'like' , '%'.$word.'%')
                            ->orWhere('details_en' , 'like' , '%'.$word.'%')
                            ->orWhere('details_ar' , 'like' , '%'.$word.'%');
                });
            })
            ->latest()
            ->with('comments')
            ->paginate();

        return response()->view('website.weblog.index' , compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function category(Request $request)
    {
        $blogCategory = BlogCategory::findOrFail($request->id);
        if( $blogCategory->slug != $request->slug)
            return redirect()->route('blog.category' , [$request->countrySubDomainCode , $request->getLocale() ,$blogCategory->id , $blogCategory->slug ]);

        $posts = $blogCategory->posts()
            ->where('status' , 'published')
            ->when($request->q , function ($query) use ($request){
                $words = explode(' ' , $request->q );
                $query->where(function ($query) use($words){
                    foreach ( $words as $word)
                        $query->orWhere('title_en' , 'like' , '%'.$word.'%')
                            ->orWhere('title_ar' , 'like' , '%'.$word.'%')
                            ->orWhere('details_en' , 'like' , '%'.$word.'%')
                            ->orWhere('details_ar' , 'like' , '%'.$word.'%');
                });
            })
            ->latest()
            ->paginate();
dd($posts);
        return response()->view('blog.list' , compact('posts', 'blogCategory'));
    }

    /**
     * Display the specified resource.
     *
     * @param BlogPost $blogPost
     * @param null $slug
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function show(Request $request )
    {
        $blogPost = BlogPost::findOrFail($request->id);
        if( $blogPost->status != 'published' and ! auth('admin')->check() )
            abort(404);

        if( $blogPost->slug != $request->slug)
            return redirect()->route('blog.show' , [$request->countrySubDomainCode , $request->getLocale() , $blogPost->id , $blogPost->slug ]);

        return response()->view('website.weblog.details' ,['post' => $blogPost]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request)
    {
        $posts = BlogPost::where('status' , 'published')
            ->whereYear('created_at' , intval($request->year) )
            ->when($request->month != null  , function ($query) use($request) {
                $query->whereMonth('created_at' , $request->month);
            })
            ->when($request->q , function ($query) use ($request){
                $words = explode(' ' , $request->q );
                $query->where(function ($query) use($words){
                    foreach ( $words as $word)
                        $query->orWhere('title_en' , 'like' , '%'.$word.'%')
                            ->orWhere('title_ar' , 'like' , '%'.$word.'%')
                            ->orWhere('details_en' , 'like' , '%'.$word.'%')
                            ->orWhere('details_ar' , 'like' , '%'.$word.'%');
                });
            })
            ->latest()
            ->paginate();
        $archiveYear = $request->year;
        $archiveMonth = $request->month;
        return response()->view('website.weblog.index' , compact('posts' , 'archiveYear' , 'archiveMonth'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tag(Request $request,string $tag)
    {
        $posts = BlogPost::where('status' , 'published')
            ->whereHas('tags' , function ($query) use ($tag){
                $query->where( 'tag_en' , 'like' , '%'.$tag.'%')
                    ->orWhere( 'tag_en' , 'like' , '%'.$tag.'%');
            })
            ->when($request->q , function ($query) use ($request){
                $words = explode(' ' , $request->q );
                $query->where(function ($query) use($words){
                    foreach ( $words as $word)
                        $query->orWhere('title_en' , 'like' , '%'.$word.'%')
                            ->orWhere('title_ar' , 'like' , '%'.$word.'%')
                            ->orWhere('details_en' , 'like' , '%'.$word.'%')
                            ->orWhere('details_ar' , 'like' , '%'.$word.'%');
                });
            })
            ->latest()
            ->paginate();
        return response()->view('blog.list' , compact('posts'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeComment(Request $request)
    {

        $this->validate($request, [
            'name'     => 'required|string',
            'email'     => 'required|email',
            'comment'         => 'required|string|min:10'
        ]);
        $blogPost = BlogPost::findOrFail($request->id);
        if( $blogPost->status != 'published' and ! auth('admin')->check() )
            abort(404);

        $comment = new BlogComments() ;
        $comment->fill($request->all());
        $comment->post_id = $blogPost->id;
        $comment->is_en = $this->is_english($request->comment);
        if ( auth('webs')->check() ){
            $comment->name = null ;
            $comment->email = null ;
            $comment->user_id = auth('webs')->id() ;
        }
        if ( $comment->save() )
            return redirect()->back()->with('message-success' ,trans('webMessage.comments_body'));
        return redirect()->back()->withInput()->with('message-error' ,trans('webMessage.invalidpayment'));
    }

    private  function uniord($u) {
        // i just copied this function fron the php.net comments, but it should work fine!
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }
    private  function is_english($str) {
        if(mb_detect_encoding($str) !== 'UTF-8') {
            $str = mb_convert_encoding($str,mb_detect_encoding($str),'UTF-8');
        }

        /*
        $str = str_split($str); <- this function is not mb safe, it splits by bytes, not characters. we cannot use it
        $str = preg_split('//u',$str); <- this function woulrd probably work fine but there was a bug reported in some php version so it pslits by bytes and not chars as well
        */
        preg_match_all('/.|\n/u', $str, $matches);
        $chars = $matches[0];
        $arabic_count = 0;
        $latin_count = 0;
        $total_count = 0;
        foreach($chars as $char) {
            //$pos = ord($char); we cant use that, its not binary safe
            $pos = $this->uniord($char);

            if($pos >= 1536 && $pos <= 1791) {
                $arabic_count++;
            } else if($pos > 123 && $pos < 123) {
                $latin_count++;
            }
            $total_count++;
        }
        if(($arabic_count/$total_count) > 0.6) {
            // 60% arabic chars, its probably arabic
            return false;
        }
        return true;
    }
}
