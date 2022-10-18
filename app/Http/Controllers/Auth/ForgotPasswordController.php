<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\InvoicePaid;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */


    public function forgot(Request $request) {
     $credentials  = $request->only(['email']);
            $validation = Validator::make($credentials , array(
                'email' => 'required',
            ));

            if ($validation->fails()) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'Campos não Pode estar vazio',
                ));
            }

         $user = User::where('email', $credentials['email'])->first();
         if (is_null($user)) {
              return response()->json(array(
                            'success' => false,
                            'message' => 'email não cadastrado'
                        ));
         }

          $passwordReset = PasswordReset::create(['email' => $user->email,'token' => str_random(60)]);
            if($passwordReset){
                 $user->notify(new InvoicePaid($passwordReset['token']));
            }

          return response()->json(array(
                     'success' => true,
                     'message' => 'link Reset  enviado para o seu email'
                 ));
    }


     public function reset(Request $request){
         $data  = $request->only(['email','password','token','password_confirmation']);

             $validation = Validator::make($data , array(
                           'email' => 'required|string|email',
                           'password' => 'required|string',
                           'password_confirmation' => 'required|same:password',
                           'token' => 'required|string'
              ),
             $messages = ['required' => 'o campo :attribute  não pode ser vazio !',
                         'password_confirmation.same' => 'A confirmação da senha deve corresponder à senha',
]);


             if ($validation->fails()) {
                         return response()->json(array(
                             'success' => false,
                             'message' => $validation->errors()
                         ));
                     }


            $passwordReset = PasswordReset::where('token', $data['token'])
                                          ->where('email',  $data['email'])->first();

            if (!$passwordReset)
               return response()->json(array(
                  'success' => false,
                  'message' => array(['token invalido'])
               ));


            $user = User::where('email', $passwordReset->email)->first();
            if (!$user)
                return response()->json([
                    'message' => 'Não encontramos usuario com este email '
                ], 404);
            $user->password = bcrypt($request->password);
            $user->save();
            PasswordReset::where('token', $data['token'])->where('email',  $data['email'])->delete();
               return response()
                        ->json(array(
                            'success' => true,
                            'message' => 'senha  atualizado com sucesso !'
                        ));
        }
}
