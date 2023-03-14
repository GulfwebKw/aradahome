<?php

namespace App\Http\Controllers\Blog;

use App\BlogPost;
use App\BlogTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;
use File;
use Auth;
use App\Http\Controllers\Common;

class AdminPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = BlogPost::when($request->q , function ($query) use ($request){
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

        return response()->view('gwc.blog.index' , compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = BlogTag::all();
        $post = new BlogPost();
        return response()->view('gwc.blog.edit' , compact('tags' , 'post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$image_thumb_w = 450;
		$image_thumb_h = 450;
		$image_big_w = 900;
		$image_big_h = 900;
		//field validation'mimes:jpeg,png,jpg,gif,svg|max:2048'
		$this->validate($request, [
			'title_en'     => 'required|string',
			'title_ar'     => 'required|string',
		    'slug'         => 'nullable|string|unique:gwc_blog_posts,slug',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'status' 	   => 'required|in:draft,published,hidden'
		]);



		try {

			//upload image
			$imageName = "";
			if ($request->hasfile('image')) {
                if(!File::isDirectory(public_path('uploads/blog/'))){
                    File::makeDirectory(public_path('uploads/blog/'), 0777, true, true);
                }
                if(!File::isDirectory(public_path('uploads/blog/thumb/'))){
                    File::makeDirectory(public_path('uploads/blog/thumb/'), 0777, true, true);
                }
        
				$imageName = 'p-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/blog/'), $imageName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/blog/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/blog/' . $imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/blog/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/blog/thumb/' . $imageName));
			}

			
			$post = new BlogPost;
			$post->fill($request->all());
			if ( empty($request->slug) )
			    $post->slug = $this->createSlug($request->title_en);
			$post->image = $imageName;
			$post->created_by = Auth::guard('admin')->user()->id;
			$post->save();

			//save logs
			$key_name   = "Post";
			$key_id     = $post->id;
			$message    = "A new record for blog post is added. (" . $post->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/blog/post')->with('message-success', 'A record is added successfully');
		} catch (\Exception $e) {
			return redirect()->back()->withInput()->with('message-error', $e->getMessage());
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $tags = BlogTag::all();
        $post   = BlogPost::findOrFail($id);
        return response()->view('gwc.blog.edit' , compact('tags' , 'post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        
        $BlogPost   = BlogPost::findOrFail($id);
        $image_thumb_w = 450;
		$image_thumb_h = 450;
		$image_big_w = 900;
		$image_big_h = 900;
		//field validation'mimes:jpeg,png,jpg,gif,svg|max:2048'
		$this->validate($request, [
			'title_en'     => 'required|string',
			'title_ar'     => 'required|string',
		    'slug'         => 'nullable|string|unique:gwc_blog_posts,slug,' . $BlogPost->id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'status' 	   => 'required|in:draft,published,hidden'
		]);



		try {


			$BlogPost->fill($request->all());
			
			//upload image
			$imageName = "";
			if ($request->hasfile('image')) {
			    
			    if (!empty($BlogPost->image)) {
        			$web_image_path = "/uploads/blog/" . $BlogPost->image;
        			$web_image_paththumb = "/uploads/blog/thumb/" . $BlogPost->image;
        			if (File::exists(public_path($web_image_path))) {
        				File::delete(public_path($web_image_path));
        				File::delete(public_path($web_image_paththumb));
        			}
        		}
                if(!File::isDirectory(public_path('uploads/blog/'))){
                    File::makeDirectory(public_path('uploads/blog/'), 0777, true, true);
                }
                if(!File::isDirectory(public_path('uploads/blog/thumb/'))){
                    File::makeDirectory(public_path('uploads/blog/thumb/'), 0777, true, true);
                }
        		
				$imageName = 'p-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/blog/'), $imageName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/blog/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/blog/' . $imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/blog/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/blog/thumb/' . $imageName));
			    $BlogPost->image = $imageName;
			}

			
			if ( empty($request->slug) )
			    $BlogPost->slug = $this->createSlug($request->title_en);
			$BlogPost->created_by = Auth::guard('admin')->user()->id;
			$BlogPost->save();

			//save logs
			$key_name   = "Post";
			$key_id     = $BlogPost->id;
			$message    = "A new record for blog post is updated. (" . $BlogPost->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/blog/post')->with('message-success', 'A record is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->withInput()->with('message-error', $e->getMessage());
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $BlogPost   = BlogPost::findOrFail($id);
		//delete parent cat mage
		if (!empty($BlogPost->image)) {
			$web_image_path = "/uploads/blog/" . $BlogPost->image;
			$web_image_paththumb = "/uploads/blog/thumb/" . $BlogPost->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		//save logs
		$key_name   = "Post";
		$key_id     = $BlogPost->id;
		$message    = "A record is removed. (" . $BlogPost->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$BlogPost->delete();
		return redirect()->back()->with('message-success', 'Post is deleted successfully');
	
    }
    
    
    private function createSlug($title, $id = 0)
    {
        // Normalize the title
        $slug = str_slug($title);

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($slug, $id);

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }

        throw new \Exception('Can not create a unique slug');
    }

    private function getRelatedSlugs($slug, $id = 0)
    {
        return BlogPost::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }
}
