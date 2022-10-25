<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, DB;
use App\Model\User;

class UserController extends Controller
{

    public function index()
    {
      $Users = User::get();

      return response()->json(["ok" => true,"data" => $Users]);
    }

    public function mostrar_borrados()
    {
      $Users = User::get();

      return response()->json([
        "ok" => true,
        "data" => $Users
      ]);
    }

    public function recuperar_borrado(Request $request, $id)
    {
     DB::beginTransaction();

      $User = User::withTrashed()->where('id', '=', $id)->first();

      $input = $request->all();

        try{

          if ($User == false) {
             return response()->json([
              'ok' => false, 
              'error' => "No se encontro o no ha sido eliminado esta User"
            ]);
          }

          $User->restore();
          DB::commit();

          return response()->json([
              'ok' => true, 
              'message' => "Se restauro el User con exito"
            ]);

          }catch(\Exception $ex){
            
          DB::rollBack();
            
            return response()->json([
                'ok' => false, 
                'error' => $ex->getMessage()
            ]);
          }
    }

    public function store(Request $request)
    {
      DB::beginTransaction();

        $input = $request->all();

        $validator = Validator::make($input, [
          'dni' => 'required|max:11|unique:personas',
          'email' => 'required|max:65',
          'nombre' => 'required|max:65',
          'segundo_nombre' => 'max:65',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'ok' => false, 
              'error' => $validator->messages()
            ]);
        }


        try{

            User::create($input);

            DB::commit();
          
            return response()->json([
              'ok' => true, 
              'message' => "Se registro el User con exito"
            ]);
          
        }catch(\Exception $ex){
          
          DB::rollBack();

          return response()->json([
              'ok' => false, 
              'error' => $ex->getMessage()
          ]);
        }

    }

    public function show($id)
    {
      $Users = User::where("Users.id", $id)
      ->first();

      return response()->json([
        "ok" => true,
        "data" => $Users
      ]);
    }
    
    public function update(Request $request, $id)
    {
      DB::beginTransaction();
      $User = User::findOrFail($id);

      $input = $request->all();

        $validator = Validator::make($input, [
          'name' => 'max:65',
          'segundo_nombre' => 'max:65',
          'apellido' => 'max:65',
          'segundo_apellido' => 'max:65',
          'carrera_id' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'ok' => false, 
              'error' => $validator->messages()
            ]);
        }

        try{

            DB::table('Users')
            ->where('id', $id)
            ->update($input);

            DB::commit();
          
          return response()->json([
              'ok' => true, 
              'message' => "Se registro con exito"
            ]);

        }catch(\Exception $ex){
          
          DB::rollBack();

          return response()->json([
              'ok' => false, 
              'error' => $ex->getMessage()
          ]);
        }

    }

    public function destroy($id)
    {
        try{

          $User = User::findOrFail($id);

          if ($User == false) {
             return response()->json([
              'ok' => false, 
              'error' => "No se encontro esta User"
            ]);
          }

          $User->delete();

          return response()->json([
              'ok' => true, 
              'message' => "Se elimino con exito",
            ]);

          }catch(\Exception $ex){
            
            return response()->json([
                'ok' => false, 
                'error' => $ex->getMessage()
            ]);
          }
    }

}
