<?php

namespace App\Http\Controllers\API;

use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Events\UserCreatedEvent; 

class UserController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('isAdmin');
        return User::latest()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check = $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($check){
            $data = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'type' => request('type'),
                'bio' => request('bio'),
                // 'photo' => request('photo'),
                'password' => Hash::make(request('password')),
            ]);

            $extra = [
                'name' => $data->name,
                'operation' => 'create'
            ];
    
            if ($data) {
                broadcast(new UserCreatedEvent($extra))->toOthers();
                $response = ['status'=>'success','code'=>200];
            }else{
                $response = ['status'=>'error','code'=>201];
            }
        }
        else{
            $response = ['status'=>'error','code'=>422];
        }

    return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            // 'password' => 'sometimes|string|min:6'
        ]);

        $update = User::where('id',$id)->update([
            'name' => request('name'),
            'email' => request('email'),
            'type' => request('type'),
            'bio' => request('bio'),
        ]);

        $extra = [
            'name' => $user->name,
            'operation' => 'update'
        ];

        
        if ($update) {
            broadcast(new UserCreatedEvent($extra))->toOthers();
            $response = ['status'=>'success','code'=>200];
        } else {
            $response = ['status'=>'error','code'=>201];
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->authorize('isAuthor');

        $cek = User::findOrFail($id);

        $extra = [
            'name' => $cek->name,
            'operation' => 'delete'
        ];

        if($cek->delete()){
            // broadcast(new UserCreatedEvent)->toOthers();
            broadcast(new UserCreatedEvent($extra))->toOthers(); // Broadcast only to other User
            $response = ['status'=>'success','code'=>200];
        }else{
            $response = ['status'=>'error','code'=>201];
        }

        return response()->json($response);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $users = User::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('type', 'LIKE', "%$search%");
            })->paginate(5);
        }
        else {
            $users = User::latest()->paginate(5);
        }
        return $users;
    }
}
