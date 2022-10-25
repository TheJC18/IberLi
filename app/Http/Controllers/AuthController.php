<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash, Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthController extends Controller
{
    public function get_role($id)
    {
      if ($id == '0') {
        $arrayName = ['null'];
        return $arrayName;
      }else {
        $user = User::where("Users.id", $id)
        ->first();
        return $user->getRoleNames();
      }
      
    }

    public function register(Request $request)
    {
        $input = $request->all();
        
        $validator = Validator::make($input, [
          'name' => 'required|string:65|max:65',
          'dni' => 'required|string:15|max:15',
          'email' => 'required|email',
          'password' => 'required|max:65',
        ]);
        
        if ($validator->fails()) {
          return response()->json([
            'ok' => false, 
            'error' => $validator->messages()
          ]);
        }
        
        try{
            $user = new Package();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->dni = $request->dni;
            $user->password = $request->password;
            $user->saveOrFail();
  
            return response()->json([
              'ok' => true, 
              'message' => "Se registro con exito"
            ]);
        
        }catch(\Exception $ex){
                  
          return response()->json([
            'ok' => false, 
            'error' => $ex->getMessage()
          ]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['ok'=> false, 'code'=> 400, 'error'=> $validator->messages()]);
        }
                
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['ok' => false,'code'=> 404, 'error' => 'Verifique los datos ingresados.']);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['ok' => false, 'code'=> 500, 'error' => 'Fallo la conexión, intente nuevamente.']);
        }

        // all good so return the token
        $user = User::select("users.*")
          ->where('email', $request->email)
          ->get();
        return response()->json(['token' => $token, 'code' => 200, 'user'=> $user]);
    }

    public function logout(Request $request) 
    {      
        $token = $request->bearerToken();
    
        JWTAuth::invalidate(JWTAuth::parseToken($token));
        return response()->json(['ok' => true, 'message'=> "Adios, vuelve pronto."]);
        
    }

    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['ok' => false, 'code'=> 401, 'error' => ['email'=> $error_message]]);
        }

        try {
            Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject('Your Password Reset Link');
            });

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['ok' => false, 'error' => $error_message], 401);
        }

        return response()->json([
            'ok' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }

    public function destroy($id)
    {
        try{

          $User = User::findOrFail($id);

          if ($User == false) {
             return response()->json([
              'ok' => false,
              'error' => "No se encontro este User"
            ]);
          }

          $User->delete();

          return response()->json([
              'ok' => true, 'code'=> 202,
              'message' => "Se modifico con exito"
            ]);

          }catch(\Exception $ex){
            
            return response()->json([
                'ok' => false,
                'error' => $ex->getMessage()
            ]);
          }
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
              'ok' => true, 'code'=> 202,
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

}
