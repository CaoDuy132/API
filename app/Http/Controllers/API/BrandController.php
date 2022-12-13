<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\brandM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ProductM;
use App\Models\storageM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class BrandController extends Controller
{

            /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function switch(brandM $brandM,Request $request )
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric',
        ],[
            'id.required'=>'Thiếu mã thương hiệu',
            'id.numeric'=>'Mã thương hiệu không hợp lệ',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $old = brandM::where('id','=',$request->id)->value('status');
            if($old==0){
                brandM::where('id','=',$request->id)->update(['status'=>1]);
                return response()->json(['check' => true]);
            }else{
                brandM::where('id','=',$request->id)->update(['status'=>0]);
                return response()->json(['check' => true]);
            }
        }
    }
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all2(){
        $result = DB::Table('brands')->where('status',1)->select('id')->get();
        $arr=[];
        $result=json_decode(json_encode($result),true);
        foreach ($result as $value) {
            $result3 =brandM::where('id','=',$value['id'])->select('id','name','status')->get();
            $count = DB::table('products')->join('brands','products.idBrand','=','brands.id')->where('idBrand','=',$value['id'])->selectRaw('count("products.id") as count')->groupby('idBrand')->value('count');
            foreach ($result3 as  $value1) {
                array_push($arr,['id'=>$value1['id'],'name'=>$value1['name'],'status'=>$value1['status'],'count'=>$count]);
            }

        }
        return response()->json($arr);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(){
        
        $result = DB::Table('brands')->select('id')->get();
        $arr=[];
        $result=json_decode(json_encode($result),true);
        foreach ($result as $value) {
            $result3 =brandM::where('id','=',$value['id'])->select('id','name','status')->get();
            $count = DB::table('products')->join('brands','products.idBrand','=','brands.id')->where('idBrand','=',$value['id'])->selectRaw('count("products.id") as count')->groupby('idBrand')->value('count');
            foreach ($result3 as  $value1) {
                array_push($arr,['id'=>$value1['id'],'name'=>$value1['name'],'status'=>$value1['status'],'count'=>$count]);
            }

        }
        return response()->json($arr);

    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,brandM $brandM)
    {
        $validation = Validator::make($request->all(), [
            'newBrand' => 'required|max:50|min:2',
        ],[
            'newBrand.required'=>'Thiếu thương hiệu',
            'newBrand.max'=>'Thương hiệu quá dài',
            'newBrand.min'=>'Thương hiệu ít nhất 2 ký tự',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check = count(brandM::where('name','=',$request->newBrand)->get());
            if($check!=0){
                return response()->json(['check' => false,'message'=>'Đã tồn tại thương hiệu']);
            }else{
                brandM::create(['name'=>$request->newBrand]);
                return response()->json(['check' => true]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\brandM  $brandM
     * @return \Illuminate\Http\Response
     */
    public function show(brandM $brandM,Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\brandM  $brandM
     * @return \Illuminate\Http\Response
     */
    public function edit(brandM $brandM,Request $request)
    {
        $validation = Validator::make($request->all(), [
            'newBrand' => 'required|max:50|min:2',
            'id'=>'required|numeric',
        ],[
            'id.required'=>'Thiếu mã thương hiệu',
            'id.numeric'=>'Mã thương hiệu không hợp lệ',
            'newBrand.required'=>'Thiếu thương hiệu',
            'newBrand.max'=>'Thương hiệu quá dài',
            'newBrand.min'=>'Thương hiệu ít nhất 2 ký tự',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check = count(brandM::where('name','=',$request->newBrand)->get());
            if($check!=0){
                return response()->json(['check' => false,'message'=>'Đã tồn tại thương hiệu']);
            }else{
                brandM::where('id','=',$request->id)->update(['name'=>$request->newBrand]);
                return response()->json(['check' => true]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\brandM  $brandM
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, brandM $brandM)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\brandM  $brandM
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,brandM $brandM)
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric',
        ],[
            'id.required'=>'Thiếu mã thương hiệu',
            'id.numeric'=>'Mã thương hiệu không hợp lệ',
        
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check = count(ProductM::where('idBrand','=',$request->id)->get());
            if($check!=0){
                return response()->json(['check' => false,'message'=>'Vẫn còn tồn tại sản phẩm của hãng']);
            }else{
                brandM::where('id','=',$request->id)->delete();
                return response()->json(['check' => true]);

            }
        }
    }   
    public function allBrandClient(){
        $allBrandClient = brandM::with('prods')->where('status','=','1')->orderBy('id', 'desc')->get();
        return response()->json($allBrandClient);
    }
    public function allBrandClientMen(){
        $allBrandClientMen = BrandM::with(['prods' =>
        function($query) {
            $query->where('status','=', 1)->where('gender','=', 1); }])->where('status', 1)->get();
       return response()->json($allBrandClientMen);
    }
    public function allBrandClientWomen(){
        $allBrandClientWomen = BrandM::with(['prods' =>
        function($query) {
            $query->where('status','=', 1)->where('gender','=', 0); }])->where('status', 1)->get();
       return response()->json($allBrandClientWomen);
    }
}
