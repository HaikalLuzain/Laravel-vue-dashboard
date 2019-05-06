<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrentUserController extends Controller
{
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return auth('api')->user();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth('api')->user();
        $currentPhoto = $user->photo;

        if ($request->photo != $currentPhoto){

            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];    

            \Image::make($request->photo)->save(public_path('img/profile/').$name);
            $request->merge(['photo' => $name]); 

            // Delete Old Photo
            $oldPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($oldPhoto)){
                @unlink($oldPhoto);
            }
        }

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $update = $user->update($request->all());

        if ($update) {
            $response = ['status' => 'success', 'code' => 200];
        }else{
            $response = ['status' => 'failed', 'code' => 201];
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
        //
    }
}
