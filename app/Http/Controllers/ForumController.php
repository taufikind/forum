<?php

namespace App\Http\Controllers;

use App\forum;
use App\Tag;
use Illuminate\Http\Request;
use Auth;
use DB;
use Storage;

class ForumController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index','show','populars');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function populars()
    {
        $populars = DB::table('forums')
                        ->join('page-views','forums.id','=','page-views.visitable_id')
                        ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
                        ->groupBy('id','title','slug')
                        ->orderBy('count','desc')
                        ->take(10)
                        ->get();

        return view('forum.populars', compact ('populars'));
    }

    public function index()
    {
      if(Auth::check()){
              $user = Auth::user();
              $id = Auth::id();
              $forums = Forum::paginate(6);
              $populars = DB::table('forums')
                              ->join('page-views','forums.id','=','page-views.visitable_id')
                              ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
                              ->groupBy('id','title','slug')
                              ->orderBy('count','desc')
                              ->take(5)
                              ->get();

          if ($user->role_id == 1 || $user->role_id == 0) {
            $data = array(
              'forums' => $forums,
              'populars' => $populars,
              'role_id'=> $user->role_id,
              'id'=> $id
            );

            return view('forum/index')->with($data);
            }else {
              return redirect('/');
            }
          }else {
            $forums = Forum::paginate(6);
            $populars = DB::table('forums')
                            ->join('page-views','forums.id','=','page-views.visitable_id')
                            ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
                            ->groupBy('id','title','slug')
                            ->orderBy('count','desc')
                            ->take(5)
                            ->get();
            $role_id = 0;
            $data = array(
              'forums' => $forums,
              'populars' => $populars,
              'role_id'=> $role_id
            );
            return view('forum/index')->with($data);
          }
        // $populars = DB::table('forums')
        //                 ->join('page-views','forums.id','=','page-views.visitable_id')
        //                 ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
        //                 ->groupBy('id','title','slug')
        //                 ->orderBy('count','desc')
        //                 ->take(5)
        //                 ->get();
        //
        // $forums = Forum::paginate(6);
        //
        // return view('forum.index', compact ('forums','populars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
          if ($id == 1) {
          $forums = Forum::orderBy('id','desc')->paginate(1);
          $tags = Tag::all();
          $role_id = $id;
          return view('forum.create', compact('tags','forums','role_id'));
        }else {
            return redirect('/');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'tags' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:1024',
        ]);
        $forums = New Forum;
        $forums->user_id = Auth::user()->id;
        $forums->title = $request->title;
        $forums->slug = str_slug($request->title);
        $forums->description = $request->description;
        if ($request->file('image')){
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $location = public_path('/images');
            $file->move($location, $filename);
            $forums->image = $filename;
        }

        $forums->save();
        $forums->tags()->sync($request->tags);
        return back()->withInfo('Pertanyaan Berhasil Di Kirim.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function show($slug,$role_id)
    {
        $role_id = $role_id;
        $populars = DB::table('forums')
                        ->join('page-views','forums.id','=','page-views.visitable_id')
                        ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
                        ->groupBy('id','title','slug')
                        ->orderBy('count','desc')
                        ->take(5)
                        ->get();
        // var_dump($populars); die();
        $forums = Forum::where('id', $slug)
                    ->orWhere('slug', $slug)
                    ->firstOrFail();
                    // var_dump($forums); die();
        $forums->addPageView();


        return view('forum.show', compact('forums','populars','role_id'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $tags = Tag::all();
        $forum = Forum::where('id', $slug)
                    ->orWhere('slug', $slug)
                    ->firstOrFail();
        return view('forum.edit', compact('forum','tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'tags' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:1024',
        ]);
        $forums = Forum::find($id);
        $forums->user_id = Auth::user()->id;
        $forums->title = $request->title;
        $forums->slug = str_slug($request->title);
        $forums->description = $request->description;
        if ($request->file('image')){
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $location = public_path('/images');
            $file->move($location, $filename);

            $oldImage = $forums->image;
            \Storage::delete($oldImage);

            $forums->image = $filename;
        }

        $forums->save();
        $forums->tags()->sync($request->tags);
        return back()->withInfo('Pertanyaan Berhasil Di Update.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $forum = Forum::find($id);
        Storage::delete($forum->image);
        $forum->tags()->detach();
        $forum->delete();
        return back()->withInfo('Pertanyaan Berhasil di Hapus.');
    }
}
