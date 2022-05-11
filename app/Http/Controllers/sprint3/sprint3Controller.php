<?php

namespace App\Http\Controllers\sprint3;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Models\basket;
use App\Models\StarAnnouce;
use App\Models\SumLike;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use DB;
class sprint3Controller extends Controller
{
    use GeneralTrait;
    public function addLike(Request $request)
    {
        $count = SumLike::where('idUser' , '=' , auth()->user()->id)
            ->where('idAnnouncement' , '=' ,  $request->idAnnouncement)
            ->count();
        if ($count ===0)
        {
            $like= new SumLike() ;
            $like ->idUser = auth()->user()->id;
            $like ->idAnnouncement = $request->idAnnouncement;
            $like->save();

            $a =Announcements::where('id' , '=' , $request->idAnnouncement)
                ->increment('sumLike', 1);
            return $this->returnError('001','Like successfully added');
        }
        else{
            SumLike::where('idUser' , '=' , auth()->user()->id)
                ->where('idAnnouncement' , '=' ,  $request->idAnnouncement)
                ->delete();
            $a =Announcements::where('id' , '=' , $request->idAnnouncement)
                ->increment('sumLike', -1);
        }
        return $this->returnSuccessMessage('the user has already done like');
    }
    public function addToBasket(Request $request)
    {
        $count = basket::where('idUser' , '=' , auth()->user()->id)
            ->where('idAnnouncement' , '=' ,  $request->idAnnouncement)
            ->count();
        if ($count ===0)
        {
            $basket = new basket();
            $basket ->idUser = auth()->user()->id;
            $basket ->idAnnouncement = $request->idAnnouncement;
            $basket->save();
            $a =Announcements::where('id' , '=' , $request->idAnnouncement)
                ->increment('sumBasket', 1);
            return $this->returnSuccessMessage('Announce added to basket successfully');
        }
        return $this->returnError('001','Announce already added to basket');
    }
    public function getBasket()
    {
        $baskets = basket::join('announcements', function($join)
        {
            $join->on('baskets.idAnnouncement', '=', 'announcements.id')
                ->join('users', function($join){
                    $join->on('announcements.idSeller', '=', 'users.id');
                });
        })->where('idUser' , '=' , auth()->user()->id)
            ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
            ->get();

        $i=0;
        $tab=[];
        foreach ($baskets as $announce)
        {
            $countLike = SumLike::where('idUser' , '=' , auth()->user()->id)
                ->where('idAnnouncement' , '=' ,  $announce->id)
                ->count();

            /* try {
                 $sumLike = SumLike::where('idAnnouncement', '=' , $announce->id) ->where('idUser', '=' ,auth()->user()->id)->count();
             } catch (Exception $e) {
                 $sumLike= 0;
             }*/
            //$sumLike = SumLike::where('idAnnouncement', '=' , $announce->id) ->where('idUser', '=' ,auth()->user()->id)->count();
            $tab[$i] = [
                'id' => $announce->id,
                'storeName' => $announce->storeName,
                'description' => $announce->description,
                'nbrImage' => substr($announce->images, 0 , strpos('@', $announce->images)+1 ) ,
                //'image' => strpos($announce->images, '@' ) ,
                'image' => substr($announce->images, strpos($announce->images, '@' )+1 , strlen($announce->images) -strpos($announce->images, '@' )  ) ,
                'created_at_date' => $announce->created_at->format('Y-m-d'),
                'created_at_time' => $announce->created_at->format('G:ia'),
                "storeId" => $announce->idSeller,
                "titleAnn" => $announce->nameAnn,
                "StoreAvatar" => $announce->StoreAvatar,
                "stock" => $announce->stock,
                "sumLike" => $countLike,
                "sumBasket" => $announce->sumBasket,
                "city" => $announce->city,
                "government" => $announce->government,
                "price" => $announce->price,


            ];
            $i++;
        }

        return $this->returnData("basket" ,$tab);
    }

    public function removefromBasket(Request $request){
        basket::where('id' ,  '=' , $request->id )
            ->delete();
        return $this->getBasket();
    }

    public function addstars(Request $request){



        $count = StarAnnouce::where('idUser' , '=' , auth()->user()->id)
            ->where('idAnnouncement' , '=' ,  $request->idAnnouncement)
            ->count();
        $newAVG = (Announcements::find($request->idAnnouncement)->stars * StarAnnouce::where('idAnnouncement' , '=' ,  $request->idAnnouncement)->count() + $request->nbstar) / (1+StarAnnouce::where('idAnnouncement' , '=' ,  $request->idAnnouncement)->count());


        $star = new StarAnnouce();
        $star ->idUser = auth()->user()->id;
        $star ->idAnnouncement = $request->idAnnouncement;
        $star ->nbreStar = $request->nbstar;
        $star->save();


        Announcements::find($request->idAnnouncement)
            ->update(['stars' => $newAVG]);
        return $this->returnData("announce" , Announcements::find($request->idAnnouncement));
        if ($count ===0)
        {

        }

    }
}
