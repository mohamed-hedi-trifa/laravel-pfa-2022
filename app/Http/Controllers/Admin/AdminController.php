<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use GeneralTrait;

    public function listSeller()
    {
        $sellers = User::orderBy('created_at' ,'desc')->select()->where( 'users.type_user', 'LIKE', '%' . 'seller' . '%')->get();
        $i=0;
        $tab=[];
        foreach ($sellers as $user)
        {
            $tab[$i] = [
                'id' => $user->id,
                'name' => $user->name,

                'city' => $user->city .', ' .  $user->government,

                'avatar' => $user->avatar,
                'created_at_date' => $user->created_at->format('Y-m-d'),
                'created_at_time' => $user->created_at->format('G:ia'),
                'type_user' => $user->type_user,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'adresse' => $user->adresse,
            ];
            $i++;
        }

        //return response()->download('C:\laragon\www\pfa-backend\storage\app\avatar/14.jpg');
        return $this->returnData("sellers" , $tab);
    }

    public function getSellerById(Request $request)
    {
        $sellers = User::orderBy('created_at' ,'desc')->select()->where( 'users.type_user', 'LIKE', '%' . 'seller' . '%')
            ->where('id', '=' , $request->id )->get();

        return $this->returnData("sellers" , $sellers);
    }

    public function verifSeller(Request $request)
    {

        $type_seller = User::where('id','=' , $request->id)->first()->type_user;
        if ($type_seller === 'sellerNotVerified')
        {
            $type_account = 'sellerVerified';
            User::where('id','=' , $request->id)->update(["type_user" => $type_account]);

        }
        else
        {
            $type_account = 'sellerNotVerified';
             User::where('id','=' , $request->id)->update(["type_user" => $type_account]);
        }
        return $this->returnData("typeAccount" ,$type_account);

    }

    public function filterSeller(Request $request)
    {
        session (['keys' => $request->keys]);
        $sellers = User::where (function ($q) {
            $q->where('users.name', 'LIKE', '%' . session('keys') . '%')
                ->orwhere('users.email', 'LIKE', '%' . session('keys') . '%')
                ->orwhere('users.telephone', 'LIKE', '%' . session('keys') . '%');
        })->where('users.type_user', 'LIKE', '%' . 'seller' . '%')->select('users.*')->get();

        $i=0;
        $tab=[];
        foreach ($sellers as $user)
        {
            $tab[$i] = [
                'id' => $user->id,
                'name' => $user->name,
                'city' => $user->city,
                'government' => $user->government,
                'avatar' => $user->avatar,
                'created_at_date' => $user->created_at->format('Y-m-d'),
                'created_at_time' => $user->created_at->format('G:ia'),
                'type_user' => $user->type_user,
            ];
            $i++;
        }

        //return response()->download('C:\laragon\www\pfa-backend\storage\app\avatar/14.jpg');
        return $this->returnData("sellers" , $tab);
    }
    public function returnImage (Request $request)
    {

        $avatar = $this->saveImage('avatar' ,$request->file('avatar') ,  "ijexxxxe/1545" );
        return "c'est bon ";
        return response()->download('C:\laragon\www\pfa-backend\storage\app' .  chr(92)  . $request->nameImage);
    }
}
