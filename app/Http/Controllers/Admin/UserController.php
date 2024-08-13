<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public $data = [
        "parent" => "User Management",
    ];

    public function index(){
        $this->data['position'] = "Data User";
        $this->data['total_data'] = User::count();
        return view('admin.user_management.index', $this->data);
    }

    public function get_all_data(){
        $data = User::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status_user', function($row){
                return $row->status_role == 0 ? 'Admin' : ($row->status_role == 1 ? 'Dokter' :  'Pengguna Biasa');
            })
            ->addColumn('action', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';

                if ($row->status_role != 0 ) {
                    $btn .= '<a class="btn btn-warning ml-1" href="'.env('APP_URL').'admin/user_management/edit/'.$row->id.'"><i class="fas fa-pencil-alt"></i></a> ';
                    $btn .= '<button class="btn btn-danger btn-raised btn-xs" id="btn-hapus" title="Hapus"><i class="icon-trash"></i></button>';
                } else {
                    $btn .= '<div class="btn-group btn-group-solid mx-5">';
                    $btn .= '<a class="btn btn-secondary ml-1" href="'.env('APP_URL').'/setting/"><i class="fa-solid fa-gear"></i></a> ';
                }

                $btn .= '</div>';
                $btn .= '</div>';
                return $btn;

            })->make(true);
    }

    public function from_update_user($id){
        $this->data['position'] = "Form Edit User";

        $this->data['data_user'] = User::find($id);
        if ($this->data['data_user']->status_role == 1){
            $this->data['data_dokter'] = Dokter::where('id_user', $id)->get();
        }

        return view("admin.user_management.index", $this->data);

    }

    public function from_create_user(){
        $this->data['position'] = "Form Create User";

        return view("admin.user_management.index", $this->data);

    }

    public function save_update_user(Request $request, $id){
        $data = User::find($id);
        $data->name = $request->input('name');
        $data->email = $request->input('email');
        $data->status_role = $request->input('status_role');
        $data->no_telpon = $request->input('no_telpon');

        if ($request->password != "") {
            $data->password = Hash::make($request->password);
        }
        if($data->update()){
            if ($request->status_role == 1){
                $data_dokter                = Dokter::where('id_user', $id)->first();
                $data_dokter->spesalis      = $request->spesalis;
                $data_dokter->jenis_dokter  = $request->jenis_dokter;
                $data_dokter->pengalaman    = $request->pengalaman;

                if(!$data_dokter->update()){
                    return redirect()->back()->with('status','Updated Failed');
                }
            }
            return redirect()->back()->with('status','Updated Successfully');
        } else {
            return redirect()->back()->with('status','Updated Failed');
        }
    }

    public function save_create_user(Request $request){
        $this->validate($request, [
            'name'          => 'required',
            'email'         => 'required',
            'password'      => 'required',
            'no_telpon'     => 'required',
            'status_role'   => 'required'
        ]);

        try {
            $new_user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'no_telpon'     => $request->no_telpon,
                'status_role'   => $request->status_role,
                'image_profile' => 'storage/default.jpg'
            ]);

            if ($new_user) {
                if ($request->status_role == 1){
                    $validator = Validator::make($request->all(), [
                        'spesalis'      => 'required|string',
                        'pengalaman'    => 'required|string',
                        'jenis_dokter'  => 'required|string',
                    ]);

                    if ($validator->fails()) {
                        $new_user->delete();
                        return redirect()->back()->with('errors', $validator->errors());
                    }

                    $data_dokter = Dokter::create([
                        'spesalis' => $request->spesalis,
                        'pengalaman' => $request->pengalaman,
                        'jenis_dokter'  => $request->jenis_dokter,
                        'id_user' => $new_user->id
                    ]);
                    if (!$data_dokter){
                        $new_user->delete();
                        return redirect()->back()->with('status','User Created Dokter Failed');
                    }
                }
                return redirect()->back()->with('status','User Created Successfully');
            }
        } catch (\Throwable $th) {

            return redirect()->back()->with('errors',$th->getMessage());
        }
    }

    public function delete_user(Request $request){
        DB::beginTransaction();
        try{
            $data_user = User::where('id', $request->id_user)->first();
            if ($data_user) {
                if ($data_user->status_role == 1) {
                    $data_dokter = Dokter::where('id_user', $request->id_user)->first();
                    $data_dokter->delete();
                }
                User::where('id', $request->id_user)->delete();
            }

            DB::commit();
            return response()->json(['title'=>'Success!','icon'=>'success','text'=>'Data Berhasil Dihapus!', 'ButtonColor'=>'#66BB6A', 'type'=>'success']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['title'=>'Error','icon'=>'error','text'=>$e->getMessage(), 'ButtonColor'=>'#EF5350', 'type'=>'error']);
        }
    }
}
