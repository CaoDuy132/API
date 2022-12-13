<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\productM;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sortView(Request $request){
            $arrIdBrand = $request->arrIdBrand;
            $arrIdCate = $request->arrIdCate;
            $minPrice = $request->min;
            $maxPrice = $request->max;
            $page = $request->page;
            $sortBy = $request->sortBy;
            $pageSize = 9;
            if($page){
                $page = ceil($page);
                if($page<1){
                    $page=1;
                }
            }
            $skip = ($page-1)*$pageSize;
            $query = productM::where('status','=','1');
            if(isset($arrIdBrand )){
                $query->whereIn('idBrand',$request->arrIdBrand);
            }
            if(isset($minPrice)&&isset($maxPrice)){
                    $query->whereBetween('price',[$minPrice,$maxPrice]);
            }
            if(isset($arrIdCate)){
                    $query->whereIn('idCate',$arrIdCate);
            }
            if($sortBy=='price_increase'){
                $query->orderBy('price','asc');
            }
            if($sortBy=='price_down'){
                $query->orderBy('price','desc');
            }
            if($sortBy=='newest'){
                $query->orderBy('id','asc');
            }
            if($sortBy=='oldest'){
                $query->orderBy('id','desc');
            }
            if($sortBy=='default'){
                $query->orderBy('id','asc');
            }
            if($sortBy=='sales'){
                $query->where('discount','!=','0');
            }
            $products = $query->skip($skip)->take($pageSize)->get();
            $total = $query->count();
            $totalPage = ceil($total / $pageSize);
            return response()->json(['products'=>$products,'total'=>$total,'totalPage'=>$totalPage]);
    }
    public function sortViewMen(Request $request){
        $arrIdBrand = $request->arrIdBrand;
        $arrIdCate = $request->arrIdCate;
        $minPrice = $request->min;
        $maxPrice = $request->max;
        $page = $request->page;
        $sortBy = $request->sortBy;
        $pageSize = 9;
        if($page){
            $page = ceil($page);
            if($page<1){
                $page=1;
            }
        }
        $skip = ($page-1)*$pageSize;
        $query = productM::where('status','=','1')->where('gender','=', 1);
        if(isset($arrIdBrand )){
            $query->whereIn('idBrand',$request->arrIdBrand);
        }
        if(isset($minPrice)&&isset($maxPrice)){
                $query->whereBetween('price',[$minPrice,$maxPrice]);
        }
        if(isset($arrIdCate)){
                $query->whereIn('idCate',$arrIdCate);
        }
        if($sortBy=='price_increase'){
            $query->orderBy('price','asc');
        }
        if($sortBy=='price_down'){
            $query->orderBy('price','desc');
        }
        if($sortBy=='newest'){
            $query->orderBy('id','asc');
        }
        if($sortBy=='oldest'){
            $query->orderBy('id','desc');
        }
        if($sortBy=='default'){
            $query->orderBy('id','asc');
        }
        if($sortBy=='sales'){
            $query->where('discount','!=','0');
        }
        $products = $query->skip($skip)->take($pageSize)->get();
        $total = $query->count();
        $totalPage = ceil($total / $pageSize);
        return response()->json(['products'=>$products,'total'=>$total,'totalPage'=>$totalPage]);
}
public function sortViewWomen(Request $request){
    $arrIdBrand = $request->arrIdBrand;
    $arrIdCate = $request->arrIdCate;
    $minPrice = $request->min;
    $maxPrice = $request->max;
    $page = $request->page;
    $sortBy = $request->sortBy;
    $pageSize = 9;
    if($page){
        $page = ceil($page);
        if($page<1){
            $page=1;
        }
    }
    $skip = ($page-1)*$pageSize;
    $query = productM::where('status','=','1')->where('gender','=', 0);
    if(isset($arrIdBrand )){
        $query->whereIn('idBrand',$request->arrIdBrand);
    }
    if(isset($minPrice)&&isset($maxPrice)){
            $query->whereBetween('price',[$minPrice,$maxPrice]);
    }
    if(isset($arrIdCate)){
            $query->whereIn('idCate',$arrIdCate);
    }
    if($sortBy=='price_increase'){
        $query->orderBy('price','asc');
    }
    if($sortBy=='price_down'){
        $query->orderBy('price','desc');
    }
    if($sortBy=='newest'){
        $query->orderBy('id','asc');
    }
    if($sortBy=='oldest'){
        $query->orderBy('id','desc');
    }
    if($sortBy=='default'){
        $query->orderBy('id','asc');
    }
    if($sortBy=='sales'){
        $query->where('discount','!=','0');
    }
    $products = $query->skip($skip)->take($pageSize)->get();
    $total = $query->count();
    $totalPage = ceil($total / $pageSize);
    return response()->json(['products'=>$products,'total'=>$total,'totalPage'=>$totalPage]);
}
    public function searchView(Request $request){
        $keyword = $request->keyword;
        $products = productM::where('status','=','1')->where('name', 'LIKE', '%' .$keyword. '%')->get();
        return response()->json($products);
    }
    public function shopDetail(Request $request){
        $arrIdBrand = $request->arrIdBrand;
        $minPrice = $request->min;
        $maxPrice = $request->max;
        $page = $request->page;
        $sortBy = $request->sortBy;
        $pageSize = 9;
        if($page){
            $page = ceil($page);
            if($page<1){
                $page=1;
            }
        }
        $skip = ($page-1)*$pageSize;
        // first load
        $slug = $request->slug;
        if($slug){
            $idCate = Category::where('slug','=',$slug)->first()->id;
            $arrIdCate=[$idCate];
        }
        if(isset($request->arrIdCate)){
            $arrIdCate=$request->arrIdCate;
        }
        // first load
        $query = productM::where('status','=','1');
        if(isset($arrIdBrand )){
            $query->whereIn('idBrand',$request->arrIdBrand);
        }
        if(isset($minPrice)&&isset($maxPrice)){
                $query->whereBetween('price',[$minPrice,$maxPrice]);
        }
        if($arrIdCate){
                $query->whereIn('idCate',$arrIdCate);
        }
        if($sortBy=='price_increase'){
            $query->orderBy('price','asc');
        }
        if($sortBy=='price_down'){
            $query->orderBy('price','desc');
        }
        if($sortBy=='newest'){
            $query->orderBy('id','asc');
        }
        if($sortBy=='oldest'){
            $query->orderBy('id','desc');
        }
        if($sortBy=='default'){
            $query->orderBy('id','asc');
        }
        if($sortBy=='sales'){
            $query->where('discount','!=','0');
        }
        $products = $query->skip($skip)->take($pageSize)->get();
        $total = $query->count();
        $totalPage = ceil($total / $pageSize);
        return response()->json(['products'=>$products,'total'=>$total,'totalPage'=>$totalPage]);  

    }
}