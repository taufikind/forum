<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;
use Auth;
use DB;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if(Auth::check()){
              $user = Auth::user();
              $id = Auth::id();
              $tags = Tag::all();
              $populars = DB::table('forums')
                              ->join('page-views','forums.id','=','page-views.visitable_id')
                              ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
                              ->groupBy('id','title','slug')
                              ->orderBy('count','desc')
                              ->take(10)
                              ->get();

          if ($user->role_id == 1 || $user->role_id == 0) {
            $data = array(
              'tags' => $tags,
              'populars' => $populars,
              'role_id'=> $user->role_id,
              'id'=> $id
            );

            return view('tag/index')->with($data);
            }else {
              return redirect('/');
            }
          }else {
            $tags = Tag::all();
            $populars = DB::table('forums')
                            ->join('page-views','forums.id','=','page-views.visitable_id')
                            ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
                            ->groupBy('id','title','slug')
                            ->orderBy('count','desc')
                            ->take(10)
                            ->get();
            $role_id = 0;
            $data = array(
              'tags' => $tags,
              'populars' => $populars,
              'role_id'=> $role_id
            );
            return view('tag/index')->with($data);
          }
      // $populars = DB::table('forums')
      //                 ->join('page-views','forums.id','=','page-views.visitable_id')
      //                 ->select(DB::raw('count(visitable_id) as count'),'forums.id','forums.title','forums.slug')
      //                 ->groupBy('id','title','slug')
      //                 ->orderBy('count','desc')
      //                 ->take(10)
      //                 ->get();
      // // var_dump($populars); die();
      //
      // $tags = Tag::all();
      // var_dump($tags); die();
      // return view('tag.index', compact('tags','populars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = Tag::all();
        return view('tag.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tag = New Tag;
        $tag->name = $request->name;
        $tag->slug = str_slug($request->name);
        $tag->save();

        return back()->withInfo('Tag baru telah di Buat.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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

        $tags = Tag::where('id', $slug)
                    ->orWhere('slug', $slug)
                    ->firstOrFail();

        return view('tag.show', compact('tags','populars','role_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tag = Tag::find($id);
        return view('tag.edit', compact('tag'));
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
        $tag = Tag::find($id);
        $tag->name = $request->name;
        $tag->slug = str_slug($request->name);
        $tag->save();

        return redirect()->route('tag.create')->withInfo('Tag telah di Update.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);
        $tag->delete();
        return redirect()->route('tag.create')->withInfo('Tag telah di Hapus.');
    }
}
