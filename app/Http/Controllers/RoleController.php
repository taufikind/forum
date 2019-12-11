<?php

namespace App\Http\Controllers;
use Auth;
use App\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect,Response;
class RoleController extends Controller
{
  public function index(){
                if(Auth::check()){
                    $user = Auth::user();
                    $id = Auth::id();

                if ($user->role_id == 1) {
                      $data = array(
                        'name'=> $user->name,
                        'email'=> $user->email,
                        'role_id'=> $user->role_id,
                        'id'=> $id
                      );
                      return view('role/list_role')->with($data);
                    }else {
                        return redirect('/');
                    }
                }
            }

            public function list(){
                // var_dump('tes'); die();
                $role = Role::all();
                $data = array();
                foreach ($role as $r) {
                  $row['role_id'] = $r->role_id;
                  $row['role_name'] = $r->role_name;
                  if ($r->role_status == 1) {
                    $row['role_status'] = 'Aktif';
                  }else {
                    $row['role_status'] = 'Tidak Aktif';
                  }
                  $row['created_at'] = date('Y-m-d H:i:s',strtotime($r->created_at));
                  $row['updated_at'] = date('Y-m-d H:i:s',strtotime($r->updated_at));
                  $data[]=$row;
                }


             if(request()->ajax()) {
                     return datatables()->of($data)
                     ->addColumn('action', 'action_button_role')
                     ->rawColumns(['action'])
                     ->addIndexColumn()
                     ->make(true);
          }
        }

        public function store(Request $request){
          date_default_timezone_set("Asia/Jakarta");
          $role_id = $request->role_id;

          $role   = Role::updateOrCreate(['role_id' => $role_id],
                  ['role_name'=> $request->role_name,'role_status'=> $request->role_status,'created_at'=> date('Y-m-d H:i:s'),'updated_at'=> date('Y-m-d H:i:s')]);
            return response::json($role);
        }

        public function destroy($id){
              $role = Role::where('role_id',$id)->delete();
              return Response::json($role);
          }

          public function edit($id){
            $where = array('role_id' => $id);
            $role  = Role::where($where)->first();
              return Response::json($role);
          }
}
