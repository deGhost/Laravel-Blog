<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facade\Storage;
//accessing Post.php
use App\Post;


class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        // User will be redirected to login (if guest) except for index and show views
        $this->middleware('auth', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //allequante way: fetch all data of a model(Post) 
        //  $posts = Post::all(); 
        /* to access a certain post via title:
         $post = Post::where('title','Post two')->get()
         Using sql queries instead of aliqunate, use DB, then:
         $posts = DB::select('SELECT * FROM posts');
         we can limit the number of posts displayed:
          orderBy('','')->take(numberOfPosts)->get()
         PS: to check if a query is right use return Post:: ....
          */
         // order by titles asc
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('posts.create'); //return /posts/create





    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
             'title'=> 'required',
             'body'=> 'required',   
             'cover_image'=> 'image|nullable|max:1999'
        ]);

        // Handle file upload
        if($request->hasFile('cover_image')){
            // Get file name with the extension
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get file name only
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // Get extension only
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Get unique name to store file
            $fileNameToStore = $fileName.''.time().'.'.$extension;
            // Upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }else{
            // Default image
            $fileNameToStore = 'noimage.jpg';
        }
        
        // create post
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        //user->id = get id of current user logged in
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        // redirect and past a message to our view.inc.messages
        return redirect('/posts')->with('success', 'Post Create');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unauthorized Page!');
        }
        
        return view('posts.edit')->with('post',$post);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'=> 'required',
            'body'=> 'required'   
       ]);
 
              // Handle file upload
              if($request->hasFile('cover_image')){
                // Get file name with the extension
                $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
                // Get file name only
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // Get extension only
                $extension = $request->file('cover_image')->getClientOriginalExtension();
                // Get unique name to store file
                $fileNameToStore = $fileName.''.time().'.'.$extension;
                // Upload image
                $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
            }
     

       // Find  post
       $post = Post::find($id);
       $post->title = $request->input('title');
       $post->body = $request->input('body');
       if($request->hasFile('cover_image')){
       $post->cover_image = $fileNameToStore;
    }
       $post->save();

       // redirect and past a message to our view.inc.messages
       return redirect('/posts')->with('success', 'Post Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
          // Find  post
       $post = Post::find($id);

       if($post->cover_image != 'noimage.jpg'){
        Storage::delete('public/cover_images/'.$post->cover_image);
       }
       $post->delete();

         // redirect and past a message to our view.inc.messages
       return redirect('/posts')->with('success', 'Post Removed');
    }
}
