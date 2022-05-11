<?php

namespace App\Http\Controllers\sprint4;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Models\Subscribers;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class sprint4Controller extends Controller
{
    use GeneralTrait;


    public function addRemoveSubscriber(Request $request)
    {

        $count = Subscribers::where('idUser' , '=' , auth()->user()->id)
            ->where('idSeller' , '=' ,  $request->idSeller)
            ->count();
        if ($count ===0)
        {
            $subcriber = new Subscribers() ;
            $subcriber ->idUser = auth()->user()->id;
            $subcriber ->idSeller = $request->idSeller;
            $subcriber->save();

            return $this->returnSuccessMessage('1000','subscriber successfully added');
        }
        else{
            Subscribers::where('idUser' , '=' , auth()->user()->id)
                ->where('idSeller' , '=' ,  $request->idSeller)
                ->delete();
            return $this->returnSuccessMessage('1001','subscriber successfully removed');
        }
    }
    public function getSeller(Request $request)
    {
        $seller= User::find($request->id);
        $nbreSubscribres = Subscribers::where('idSeller', '=' , $request->id ) ->count();
        $nbrePublication = Announcements::where('idSeller', '=' , $request->id ) ->count();
        $seller->nbreSubs = $nbreSubscribres;
        $seller->nbrePub = $nbrePublication;
        return $this->returnData("seller", $seller);
    }
    public function getInfo(Request $request)
    {
        $nbreSubscribres = Subscribers::where('idSeller', '=' , $request->idSeller ) ->count();
        $nbrePublication = Announcements::where('idSeller', '=' , $request->idSeller ) ->count();

        $tab =[
        'nbreSubs'  => $nbreSubscribres,
            'nbrePub'  => $nbrePublication,
            ];
        return $this->returnData('info' , $tab);

    }
}
