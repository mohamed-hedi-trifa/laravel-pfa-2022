<?php

namespace App\Http\Controllers\authentification;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Models\Subscribers;
use App\Models\User;
use Auth ;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;

class AuthentificationController extends Controller
{
    use GeneralTrait;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {

        $rules=[
            "email"=> "required|email ",
            "password"=>"required "
        ];

        $validator = Validator::make($request->all() , $rules);

        if($validator->fails())
        {
            $code=$this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code , $validator);
        }

        $credentials['email'] = $request->email;
        $credentials['password'] = $request->password;

        $token = Auth::guard('api')->attempt($credentials);
        if(!$token)
            return $this->returnError('E001' , 'email or password not right');

        $user = Auth::guard('api')->user();
        $user->api_token = $token;


        return $this->returnData("token", $token , $user->type_user);
    }

    public function register(Request $request )
    {

        $rules=[
            "name" => "required",
            "email"=> "required",
            "email"=> "email",
            "password"=>"required "
        ];

        $validator = Validator::make($request->all() , $rules);

        if($validator->fails())
        {
            $code=$this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code , $validator);
        }


        $user = new User();
        $user ->name = $request->name;
        $user ->email = $request->email;
        $user ->telephone = "";
        $user ->avatar = "";
        $user ->adresse = "";
        $user ->type_user = "internet-user";
        $user ->loc_longitude = "";
        $user ->loc_altitude = "";
        $user ->city = "";
        $user ->government = "";
        $user ->password = bcrypt($request->password);
        $user->save();

        $credentials['email'] = $request->email;
        $credentials['password'] = $request->password;

        $token = Auth::guard('api')->attempt($credentials);
        if(!$token)
            return $this->returnError('E001' , 'email or password not right');

        $user = Auth::guard('api')->user();
        $user->api_token = $token;
        return $this->returnData("token", $token);


    }
    public function uploadImage( Request $request) {
        $avatar = $this->saveImage('avatar' ,$request->file('avatar') ,   auth()->user()->id );
    }

    public function registerSeller(Request $request){


        $user = User::where('id', auth()->user()->id)
            ->update(['telephone' => $request->telephone, 'avatar' => "avatar/". auth()->user()->id. '.jpg',
                'adresse' => $request->adresse, 'loc_longitude' => $request->loc_longitude,
                'loc_altitude' => $request->loc_altitude,'city' => $request->city,
                'government' => $request->government, 'type_user' => "sellerNotVerified" ]);
        return $this->returnSuccessMessage("user updated successfully");
    }




    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function me()
    {

        $seller= User::find(auth()->user()->id);
        $nbreSubscribres = Subscribers::where('idSeller', '=' , auth()->user()->id ) ->count();
        $nbrePublication = Announcements::where('idSeller', '=' , auth()->user()->id) ->count();
        $seller->nbreSubs = $nbreSubscribres;
        $seller->nbrePub = $nbrePublication;
        return $this->returnData("seller", $seller);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
