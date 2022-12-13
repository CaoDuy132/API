<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\billM;
use App\Models\billdetailM;
use App\Models\CustomerM;
use App\Models\ProductM;
use App\Models\storageM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\File;
class BillController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBill(Request $request,billM $billM,billdetailM $billdetailM,storageM $storageM)
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric',
            'stt'=>'required|numeric',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>'Dữ liệu không hợp lệ']);
        }else{
            $old= billM::where('id','=',$request->id)->value('status');
            $arr = billdetailM::where('idBill','=',$request->id)->select('idStorage as ids','quantity as qty')->get();
            foreach ($arr as $key => $value) {
                $ids= $value['ids'];
                $qty= $value['qty'];
                $oldQty=storageM::where('id','=',$ids)->value('quantity');
                $new=($oldQty-$qty);
                storageM::where('id','=',$ids)->update(['quantity'=>$new,'updated_at'=>now()]);
            }
            if($old==3||$old==0){
                return response()->json(['check'=>false]);
            }else{
                billM::where('id','=',$request->id)->update(['status'=>$request->stt,'updated_at'=>now()]);
                return response()->json(['check'=>true]);
            }
            
        }
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function singleBill(Request $request,billM $billM ,billdetailM $billdetailM,ProductM $ProductM)
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric'
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false]);
        }else{
            $id=$request->id;
            $arr=[];
            $status=billM::where('id','=',$request->id)->value('status');
            $result = billdetailM::where('idBill','=',$id)->select('idStorage','quantity')->get();
            $result = json_decode($result,true);
            foreach ($result as  $value) {
                $idS= $value['idStorage'];
                $qty = $value['quantity'];
                $products= DB::Table('storage')
                        ->join('products','storage.idProd','=','products.id')
                        ->where('storage.id','=',$idS)
                        ->select('storage.color as color','products.name as name','products.slug as slug','products.price as price','products.discount as discount','products.image as image')->get();
                $products=json_decode($products,true);
                foreach ($products as $value1) {
                    $uri = $value1['image'];
                    array_push($arr,['name'=>$value1['name'],'qty'=>$qty,'slug'=>$value1['slug'],'price'=>$value1['price'],'discount'=>$value1['discount'],'image'=>$uri]);
                }
            }
            return response()->json(['check'=>true,'result'=>$arr,'status'=>$status]);
        }   

    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSingleUserBill2(Request $request,CustomerM $CustomerM)
    {
        $validation = Validator::make($request->all(), [
            'email'=>'required|email'
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false]);
        }else{
            $email = $request->email;
            $idCus = CustomerM::where('email','=',$email)->value('id');
            $bills = DB::Table('bills')->join('customers','bills.idCustiomer','=','customers.id')->where('customers.id','=',$idCus)->select('bills.id as idBill','bills.total as total','customers.name as customername','bills.status as status','bills.created_at as created_at')->get();
            return response()->json(['check'=>true,'result'=>$bills]);
        }
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSingleUserBill(Request $request,CustomerM $CustomerM)
    {
        $validation = Validator::make($request->all(), [
            'email'=>'required|email'
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false]);
        }else{
            $email = $request->email;
            $provider_id = $request->provider_id;
            $idCus = CustomerM::where('email','=',$email)->where('provider_id','=',$provider_id)->value('id');
            $bills = DB::Table('bills')->join('customers','bills.idCustiomer','=','customers.id')->where('customers.id','=',$idCus)->select('bills.id as idBill','bills.total as total','customers.name as customername','bills.status as status','bills.created_at as created_at')->get();
            return response()->json($bills);
        }
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function all2()
    // {
    //     $result=[];
    //     $rs = DB::Table('bills')->select('id')->get();
    //     $idBills=json_decode($rs,true);
    //     foreach ($idBills as  $value) {
    //         $res=DB::Table('bills')->join('customers','bills.idCustiomer','=','customers.id')->where('bills.id','=',$value)->select('bills.id as idBill','bills.total as total','customers.name as customername','bills.status as status','bills.created_at as created_at')->get();
    //         $res1= json_decode($res,true);
    //         $qty = DB::Table('billdetail')->where('billdetail.idBill','=',$value)->value(DB::Raw('count(id)'));
    //        foreach ($res1 as $key => $value1) {
    //         array_push($result,['idBill'=>$value1['idBill'],'name'=>$value1['customername'],'total'=>$value1['total'],'status'=>$value1['status'],'created_at'=>$value1['created_at'],'qty'=>$qty]);
    //        }
    //     }
    //     return response()->json(['check'=>true,'result'=>$result]);
    // }
    //      /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    public function all()
    {
        $result=[];
        $rs = DB::Table('bills')->select('id')->get();
        $idBills=json_decode($rs,true);
        foreach ($idBills as  $value) {
            $res=DB::Table('bills')->join('customers','bills.idCustiomer','=','customers.id')->where('bills.id','=',$value)->select('bills.id as idBill','bills.total as total','customers.name as customername','bills.status as status','bills.created_at as created_at')->get();
            $res1= json_decode($res,true);
            $qty = DB::Table('billdetail')->where('billdetail.idBill','=',$value)->value(DB::Raw('count(id)'));
           foreach ($res1 as $key => $value1) {
            array_push($result,['idBill'=>$value1['idBill'],'name'=>$value1['customername'],'total'=>$value1['total'],'status'=>$value1['status'],'created_at'=>$value1['created_at'],'qty'=>$qty]);
           }
        }
        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request,billdetailM $billdetailM,billM $billM,ProductM $ProductM)
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric'
        ],[
            'id.required'=>'Thiếu mã đơn hàng',
            'id.numeric'=>'Mã đơn hàng không hợp lệ',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $result = billdetailM::where('idBill','=',$request->id)->select('idStorage','quantity')->get();
            $arr=[];
            foreach ($result as $value) {
                $result1 = DB::Table('storage')->join('products','storage.idProd','=','products.id')->where('storage.id',$value['idStorage'])->select('storage.color as color','storage.quantity as tonkho','products.name as productname','products.price as price','products.discount as discount','products.image as image')->get();
                $result1=json_decode($result1,true);
                foreach ($result1 as $key => $value1) {
                    $ob=['color'=>$value1['color'],'tonkho'=>$value1['tonkho'],'productname'=>$value1['productname'],'price'=>$value1['price'],'discount'=>$value1['discount'],'booked'=>$value['quantity'],'image'=>'http://127.0.0.1:3000/images/'.$value1['image']];
                    array_push($arr,$ob);
                }
            }
            return response()->json(['check'=>true,'result'=>$arr]);
        }
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
    public function store(Request $request,billdetailM $billdetailM,CustomerM $CustomerM,billM $billM)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|max:70|min:2',
            'phone'=>'required|max:10',
            'address'=>'required|min:10|max:150',
            'reciver'=>'required|max:70|min:2',
            'cart'=>'required',
            'total'=>'required|numeric'
        ],[
            'name.required'=>'Thiếu tên khách hàng',
            'name.max'=>'Tên khách hàng quá dại',
            'name.min'=>'Tên khách hàng không hợp lệ',
            'phone.required'=>'Thiếu số điện thoại khách hàng',
            'phone.max'=>'Số điện thoại phải là số điện thoại 10 số',
            'address.required'=>'Thiếu địa chỉ nhận hàng',
            'address.max'=>'Địa chỉ khách hàng quá dài',
            'address.min'=>'Địa chỉ khách hàng phải ít nhất 10 ký tự',
            'reciver.required'=>'Thiếu tên người nhận',
            'reciver.max'=>'Tên người nhận quá dài',
            'reciver.min'=>'Tên người nhận quá ngắn',
            'cart.required'=>'Thiếu thông tin danh sách hàng hóa',
            'total.required'=>'Thiếu thanh toán đơn hàng',
            'total.numeric'=>'Thanh toán đơn hàng không hợp lệ',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $pattern='/^[0][3|5|7|8|9][0-9]{8}+$/';
            if(!preg_match($pattern, $request->phone)){
                return response()->json(['check' => false,'message'=>'Số điện thoại không hợp lệ']);            
            }else{
                // return response()->json($cart);
                $check = CustomerM::where('email','=',$request->email)->count('id');
                if($check==0){
                    if($request->note==''){
                        $idCus = CustomerM::insertGetId(['name'=>$request->name,'phone'=>$request->phone,'email'=>$request->email]);
                        $idBill= billM::insertGetId(['idCustiomer'=>$idCus,'recieverName'=>$request->reciver,'address'=>$request->address,'recieverPhone'=>$request->phone,'total'=>$request->total,'created_at'=>now()]);
                        billM::where('id','=',$idBill)->update(['idBill'=>"TN-".substr(md5($idBill),0,5)]);
                        $cart = json_decode($request->cart,true);
                        foreach ($cart as $value) {
                            DB::Table('billdetail')->insert(['idBill'=>$idBill,'idStorage'=>$value[0],'quantity'=>$value[1],'created_at'=>now()]);
                        }
                        return response()->json(['check'=>true]);

                    }else{
                        $idCus = CustomerM::insertGetId(['name'=>$request->name,'phone'=>$request->phone,'email'=>$request->email]);
                        $idBill=billM::insertGetId(['idCustiomer'=>$idCus,'recieverName'=>$request->reciver,'address'=>$request->address,'recieverPhone'=>$request->phone,'total'=>$request->total,'note'=>$request->note,'created_at'=>now()]);
                        billM::where('id','=',$idBill)->update(['idBill'=>"TN-".substr(md5($idBill),0,5)]);
                        $cart = json_decode($request->cart,true);
                        foreach ($cart as $value) {
                            DB::Table('billdetail')->insert(['idBill'=>$idBill,'idStorage'=>$value[0],'quantity'=>$value[1],'created_at'=>now()]);
                        }
                        return response()->json(['check'=>true]);
                    }
                   
                }else{
                    $idCus = CustomerM::where('email','=',$request->email)->value('id');
                    if($request->note==''){
                        $idBill= billM::insertGetId(['idCustiomer'=>$idCus,'recieverName'=>$request->reciver,'address'=>$request->address,'recieverPhone'=>$request->phone,'total'=>$request->total,'created_at'=>now()]);
                        billM::where('id','=',$idBill)->update(['idBill'=>"TN-".substr(md5($idBill),0,5)]);
                        $cart = json_decode($request->cart,true);
                        foreach ($cart as $value) {
                            DB::Table('billdetail')->insert(['idBill'=>$idBill,'idStorage'=>$value[0],'quantity'=>$value[1],'created_at'=>now()]);
                        }
                        return response()->json(['check'=>true]);

                    }else{
                        $idBill=billM::insertGetId(['idCustiomer'=>$idCus,'recieverName'=>$request->reciver,'address'=>$request->address,'recieverPhone'=>$request->phone,'total'=>$request->total,'note'=>$request->note,'created_at'=>now()]);
                        billM::where('id','=',$idBill)->update(['idBill'=>"TN-".substr(md5($idBill),0,5)]);
                        $cart = json_decode($request->cart,true);
                        foreach ($cart as $value) {
                            DB::Table('billdetail')->insert(['idBill'=>$idBill,'idStorage'=>$value[0],'quantity'=>$value[1],'created_at'=>now()]);
                        }
                        return response()->json(['check'=>true]);

                    }
                }

            }
            
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function chart(Request $request,billdetailM $billdetailM,CustomerM $CustomerM,ProductM $ProductM,billM $billM)
    {
        $result = DB::Table('products')->select('gender',DB::raw('count(*) as total'))->groupBy('gender')->get();
        $result1 = DB::Table('products')->join('categrories','products.idCate','=','categrories.id')->select('categrories.name','products.idCate',DB::raw('sum(products.seen) as total'))->groupBy('products.idCate','categrories.name')->get();
        $result2 = DB::Table('products')->join('brands','products.idBrand','=','brands.id')->select('brands.name','products.idBrand',DB::raw('sum(products.seen) as total'))->groupBy('products.idBrand','brands.name')->get();
        $result3 = DB::Table('products')->join('productimage','products.id','=','productimage.idProd')->where('productimage.choose','=',1)->select('products.name as name','seen','productimage.image as image')->orderBy('seen','desc')->take(5)->get();
        $result4 = DB::Table('bills')->select('bills.created_at as date',DB::raw('sum(total) as total'))->groupBy('bills.created_at')->orderBy('bills.created_at','ASC')->get();
        $total= DB::Table('bills')->select(DB::raw('count(id) as total'))->value('total');
        $result6 = DB::Table('bills')->select('status as status',DB::raw('count(status) as count'))->groupBy('status')->get();
        $totalProduct=DB::Table('products')->select(DB::raw('count(products.id) as countProd'))->value('countProd');
        $countCate=DB::Table('categrories')->select(DB::raw('count(categrories.id) as countProd'))->value('countCate');
        return response()->json(['check'=>true,'result'=>[$result,$result1,$result2,$result3,$result4,$result6],'totalbill'=>$total,'countProd'=>$totalProduct,'countCate'=>$countCate]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get5Bills()
    {
        $result5= DB::Table('bills')->join('customers','bills.idCustiomer','=','customers.id')->where('bills.status','=',1)->select('bills.id as idBill','customers.name as customername','bills.total as total','bills.address as billaddress','bills.created_at as billcreate','bills.updated_at as sendbilldate')->orderBy('bills.created_at','DESC')->take(5)->get();
        return response()->json($result5);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,billM $billM)
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric',
            'status'=>'required|numeric'

        ],[
            'id.required'=>'Thiếu mã đơn hàng',
            'id.numeric'=>'Mã đơn hàng không hợp lệ',
            'status.required'=>'Thiếu tình trạng đơn hàng',
            'status.numeric'=>'Tình trạng đơn hàng không hợp lệ',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            billM::where('id','=',$request->id)->update(['status'=>$request->status,'updated_at'=>now()]);
            return response()->json(['check' => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
