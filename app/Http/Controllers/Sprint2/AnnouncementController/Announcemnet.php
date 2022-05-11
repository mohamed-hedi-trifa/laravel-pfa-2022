<?php

namespace App\Http\Controllers\Sprint2\AnnouncementController;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Models\Images;
use App\Models\StarAnnouce;
use App\Models\SumLike;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;

class Announcemnet extends Controller
{
    use GeneralTrait;

    public function returnImage (Request $request)
    {
        $idAnnance= Announcements::orderBy('created_at', 'desc')->where('idSeller' , '=' , auth()->user()->id)->select('id')->first();


        if($idAnnance=== null)
        {
            $avatar = $this->saveImage('avatar' ,$request->file('avatar') ,  'seller-' .auth()->user()->id . "/" . "announce-1/img" .$request->name);
        }else
        {
            $avatar = $this->saveImage('avatar' ,$request->file('avatar') ,  'seller-' .auth()->user()->id . "/" . "announce-" .$idAnnance->id . '/img' .$request->name);
        }

        return "c'est bondd ";
    }

    public function addAnnouce(Request $request){

        $rules=[
            "name"=> "required ",
            "description"=> "required ",
            "price"=> "required ",
        ];

        $validator = Validator::make($request->all() , $rules);

        if($validator->fails())
        {
            $code=$this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code , $validator);
        }

        $idAnnance= Announcements::orderBy('created_at', 'desc')->where('idSeller' , '=' , auth()->user()->id)->select('id')->first();

        if ($idAnnance === null)
            $numberImages= $this->returnCountImages(auth()->user()->id,1);
        else
            $numberImages= $this->returnCountImages(auth()->user()->id,$idAnnance->id);

        $Announcement = new Announcements();
        $Announcement->idSeller = auth()->user()->id;
        $Announcement->nameAnn = $request->name;
        $Announcement->description = $request->description;
        $Announcement->gender = $request->gender;
        $Announcement->price = $request->price;
        $Announcement->idCategory = $request->idCategory;
        $Announcement->sumLike = 0;

        if ($idAnnance === null)
            $Announcement->images = $numberImages ."@avatar/seller-" . auth()->user()->id . "/" . "announce-1"  . '/img';
        else
            $Announcement->images = $numberImages ."@avatar/seller-" . auth()->user()->id . "/" . "announce-" .$idAnnance->id . '/img';

        $Announcement->save();

        return $this->returnSuccessMessage("announce created successfully", "0000" );

    }
    public function returnCountImages($idUser , $idAnnounce)
    {
        $i=0;

        while(true)
        {
            $file = public_path('avatar/seller-'. $idUser.'/announce-'. $idAnnounce .'/img'. $i . '.jpg' );
            if(! file_exists($file)){

                return $i+1;
            }
            else
            {
                $i++;
            }
        }
    }
    public function getAnnounceProfile()
    {
        $announcements = Announcements::where('idSeller',  '=' , auth()->user()->id)->get();

        $i= 0;
        foreach ($announcements as $announce)
        {
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
                "sumLike" => $announce->sumLike,
                "sumBasket" => $announce->sumBasket,
                "price" => $announce->price,
                
            ];
            $i++;
        }

        return $this->returnData('announce' , $tab);
    }
    public function getAnnounceProfilewithId(Request $request)
    {
        $announcements = Announcements::where('idSeller',  '=' , $request->id )->get();

        $i= 0;
        foreach ($announcements as $announce)
        {
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
                "sumLike" => $announce->sumLike,
                "sumBasket" => $announce->sumBasket,
                "price" => $announce->price,
                
            ];
            $i++;
        }

        return $this->returnData('announce' , $tab);
    }

    public function affichPost(Request $request){
        /*$announcements = Announcements::orderBy('created_at' ,'desc')->select()
            ->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
            ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
            ->inRandomOrder()->get();*/


        if(!$request->minPrice and !$request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }
        elseif($request->minPrice and $request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'announcements.price', '>',  $request->minPrice )
                ->where( 'announcements.price', '<',  $request->maxPrice )
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }
        elseif(!$request->minPrice and $request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'announcements.price', '<',  $request->maxPrice )
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }
        elseif($request->minPrice and !$request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'announcements.price', '>',  $request->minPrice )
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }


        $i=0;
        $tab=[];
        foreach ($announcements as $announce)
        {
           /* try {
                $sumLike = SumLike::where('idAnnouncement', '=' , $announce->id) ->where('idUser', '=' ,auth()->user()->id)->count();
            } catch (Exception $e) {
                $sumLike= 0;
            }*/
            //$sumLike = SumLike::where('idAnnouncement', '=' , $announce->id) ->where('idUser', '=' ,auth()->user()->id)->count();
            $tab[$i] = [
                'id' => $announce->id,
                'idSeller' => $announce->idSeller,
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
                "sumLike" => $announce->sumLike,
                "stars" => $announce->stars,
                "sumBasket" => $announce->sumBasket,
                "city" => $announce->city,
                "government" => $announce->government,
                "price" => $announce->price,
                "prices" => $announce->idCategory,


            ];
            $i++;
        }

        //return response()->download('C:\laragon\www\pfa-backend\storage\app\avatar/14.jpg');
        return $this->returnData("posts" , $tab);
    }

    public function affichPostWithAuth(Request $request){
        /*$announcements = Announcements::orderBy('created_at' ,'desc')->select()
            ->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
            ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
            ->inRandomOrder()->get();*/


        if(!$request->minPrice and !$request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }
        elseif($request->minPrice and $request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'announcements.price', '>',  $request->minPrice )
                ->where( 'announcements.price', '<',  $request->maxPrice )
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }
        elseif(!$request->minPrice and $request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'announcements.price', '<',  $request->maxPrice )
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }
        elseif($request->minPrice and !$request->maxPrice)
        {
            $announcements = Announcements::join('users', function($join)
            {
                $join->on('announcements.idSeller', '=', 'users.id');
            })->where( 'announcements.gender', 'LIKE', '%' . $request->gender . '%')
                ->where( 'announcements.idCategory', 'LIKE', '%' . $request->idCategory . '%')
                ->where( 'announcements.price', '>',  $request->minPrice )
                ->where( 'users.city', 'LIKE', '%' . $request->city . '%')
                ->select('announcements.*', 'users.id as idSeller', 'users.city as city', 'users.government as government' , 'users.avatar as StoreAvatar' ,'users.name as storeName' )
                ->get();
        }


        $i=0;
        $tab=[];
        foreach ($announcements as $announce)
        {
            $countLike = SumLike::where('idUser' , '=' , auth()->user()->id)
                ->where('idAnnouncement' , '=' ,  $announce->id)
                ->count();
            $countStars = StarAnnouce::where('idUser' , '=' , auth()->user()->id)
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
                'idSeller' => $announce->idSeller,
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
                "countStars" =>$countStars,
                "stars" => $announce->stars,
                "sumBasket" => $announce->sumBasket,
                "city" => $announce->city,
                "government" => $announce->government,
                "price" => $announce->price,
                


            ];
            $i++;
        }

        //return response()->download('C:\laragon\www\pfa-backend\storage\app\avatar/14.jpg');
        return $this->returnData("posts" , $tab);
    }
}
