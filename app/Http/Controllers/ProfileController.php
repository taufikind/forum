<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Forum;
use Auth;

class ProfileController extends Controller
{
   public function index(User $user)
   {
       // $forums = Forum::where('user_id', $user->id)->get();
       // return view('profile.index', compact('user','forums'));
       if(Auth::check()){
               $user = Auth::user();
               $id = Auth::id();
               $forums = Forum::where('user_id', $user->id)->get();

           if ($user->role_id == 1 || $user->role_id == 0) {
             $data = array(
               'forums' => $forums,
               'user' => $user,
               'role_id'=> $user->role_id,
               'id'=> $id
             );

             return view('profile/index')->with($data);
             }else {
               return redirect('/');
             }
           }
   }
}
