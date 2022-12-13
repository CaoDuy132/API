<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductM;
use App\Models\productGalleryM;
use App\Models\storageM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use App\Models\sizeM;
use Illuminate\Support\Collection;

use function Ramsey\Uuid\v1;

class ProductController extends Controller
{       
    public function highlightProd()
    {
        $result = DB::Table('products')
        ->join('categrories', 'products.idCate', '=', 'categrories.id')
        ->join('brands', 'products.idBrand', '=', 'brands.id')
        ->where('products.status', '=', 1)
        ->where('products.week', '=', 1)
        ->select('products.id as idProd','products.image as image', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();
        $arr = [];
        if(count($result)==0){
            return response()->json(['check'=>false]);
        }else{
            $result1 = json_decode($result,true);
            foreach ($result1 as  $value1) {
                $item=['name'=>$value1['name'],'slug'=>$value1['slug'],'link'=>'http://127.0.0.1:3000/api/singleProd/'.$value1['slug'].'.html','image'=>'http://127.0.0.1:3000/images/'.$value1['image'],'price'=>$value1['price'],'discount'=>$value1['discount'],'seen'=>$value1['seen'],'brand'=>$value1['brandname'],'categrories'=>$value1['catename']];
                array_push($arr,$item);
            }
                return response()->json($arr);
        }
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */ 
    public function switchHighlightProduct(Request $request, productM $productM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $idProd = $request->id;
            $old = productM::where('id','=',$idProd)->value('week');
            $status = productM::where('id','=',$idProd)->value('status');
            $count= DB::Table('products')->where('week','=',1)->count('id');
            if($count>=5){
                $id2=productM::where('id', '!=', $idProd)->where('week','=',1)->orderBy('updated_at','ASC')->take(1)->value('id');
                productM::where('id', '=',$id2)->update(['week' => 0,'updated_at'=>now()]);
                if ($old == 0) {
                    productM::where('id', '=', $idProd)->update(['week' => 1,'updated_at'=>now()]);
                } else {
                    productM::where('id', '=', $idProd)->update(['week' => 0,'updated_at'=>now()]);
                }
                return response()->json(['check' => true]);
                
            }else{
                if($status==0){
                    return response()->json(['check' => false,'message'=>'Sản phẩm chưa được mở']);
                }else{
                    if ($old == 0) {
                        productM::where('id', '=', $idProd)->update(['week' => 1,'updated_at'=>now()]);
                    } else if($old==1) {
                        productM::where('id', '=', $idProd)->update(['week' => 0,'updated_at'=>now()]);
                    }
                    return response()->json(['check' => true]);
                }
            }

            }

    }
    
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */ 
    public function editColorName(Request $request,storageM $storageM,productM $product)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'idProd' => 'required|numeric',
            'color'=>'required',
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'color.required' => 'Thiếu tên màu sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
            'qty.required' => 'Thiếu số lượng sản phẩm ',
            'qty.numeric' => 'Số lượng sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            storageM::where('id','=',$request->id)->update(['color'=>$request->color,'updated_at'=>now()]);
            return response()->json(['check' => true]);
        }
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */ 
    
    public function switchProductGender(Request $request, productM $product)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'gender' => 'required|numeric|min:0|max:1',
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'gender.required' => 'Thiếu mã giới tính',
            'gender.numeric' => 'Mã giới tính không hợp lệ',
            'gender.min' => 'Mã giới tính không được chấp nhận',
            'gender.max' => 'Mã giới tính không được chấp nhận',

        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            productM::where('id','=',$request->id)->update(['gender'=>$request->gender]);
            return response()->json(['check' => true]);
        }
    }
    
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */ 

    public function updateProductDiscount(Request $request, productM $product)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'discount' => 'required|numeric|min:0|max:99',
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'discount.required' => 'Thiếu giá sản phẩm mới',
            'discount.numeric' => 'Giảm giá sản phẩm không hợp lệ',
            'discount.min' => 'Giảm giá sản phẩm không được thấp hơn 0',
            'discount.max' => 'Giảm giá sản phẩm không được hơn 99&',

        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = productM::where('id','=',$request->id)->count('id');
            if($check==0){
                return response()->json(['check' => false, 'message' =>'Không tồn tại sản phẩm']);
            }else{
                productM::where('id','=',$request->id)->update(['discount'=>$request->discount]);
                return response()->json(['check' => true]);
            }
        }
    }
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */ 
    public function updateProductPrice(Request $request, productM $product)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'price' => 'required|numeric|min:1000',
           
           
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'price.required' => 'Thiếu giá sản phẩm mới',
            'price.numeric' => 'Giá sản phẩm không hợp lệ',
            'price.min' => 'Giá sản phẩm không được thấp hơn 1000',

        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = productM::where('id','=',$request->id)->count('id');
            if($check==0){
                return response()->json(['check' => false, 'message' =>'Không tồn tại sản phẩm']);
            }else{
                productM::where('id','=',$request->id)->update(['price'=>$request->price]);
                return response()->json(['check' => true]);
            }
        }
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */ 
    public function updateproductName(Request $request, productM $product)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'name' => 'required',
            'slug' => 'required',
           
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'name.required' => 'Thiếu tên sản phẩm mới',
            'slug.required' => 'Thiếu tên 2 sản phẩm mới',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = productM::where('slug','=',$request->slug)->count('id');
            if($check!=0){
                return response()->json(['check' => false, 'message' =>'Đã tồn tại tên sản phẩm']);
            }else{
                productM::where('id','=',$request->id)->update(['name'=>$request->name,'slug'=>$request->slug]);
                return response()->json(['check' => true]);
            }
        }
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function addMoreColorStorage(Request $request,storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'idProd' => 'required|numeric',
            'qty' => 'required|numeric',
            'idSize' => 'required|numeric',
            'color'=>'required',
        ], [
            'idProd.required' => 'Thiếu mã sản phẩm ',
            'color.required' => 'Thiếu tên màu sản phẩm ',
            'idProd.numeric' => 'Mã sản phẩm không hợp lệ ',
            'qty.required' => 'Thiếu số lượng sản phẩm ',
            'qty.numeric' => 'Số lượng sản phẩm không hợp lệ ',
            'idSize.required' => 'Thiếu mã size sản phẩm ',
            'idSize.numeric' => 'Mã size sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = count(storageM::where('idProd','=',$request->idProd)->where('idSize','=',$request->idSize)->where('color','=',$request->color)->get());
            if($check!=0){
                return response()->json(['check' => false, 'message' => 'Đã tồn tại màu sắc']);
            }else{
                storageM::create(['color'=>$request->color,'idProd'=>$request->idProd,'idSize'=>$request->idSize,'quantity'=>$request->qty]);
                $storage =DB::Table('storage')->join('tbl_size','storage.idSize','tbl_size.id')
                ->where('storage.idProd', '=', $request->idProd)
                ->select('storage.id as id','storage.idProd as idProd','tbl_size.id as idSize',
                'tbl_size.sizename as sizename','storage.color as color','storage.quantity as quantity','storage.status as status')->get();
                return response()->json(['check' => true,'storage'=>$storage,'idProd'=>$request->idProd]);
            }
        }

    }
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function deleteStorageColor(Request $request,storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $id = storageM::where('id', '=', $request->id)->value('idProd');
            storageM::where('id','=', $request->id)->delete();           
            return response()->json(['check' => true,'idProd'=> $id]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct2(Request $request, productM $product, productGalleryM $productGalleryM, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = count(productM::where('status', '=', 2)->where('id', '=', $request->id)->get());
            if ($check == 0) {
                return response()->json(['check' => false, 'message' => 'Không tìm thấy sản phẩm']);
            } else {
                $result = productGalleryM::where('idProd', '=', $request->id)->select('image')->get();
                foreach ($result as  $value1) {
                    if (file_exists(public_path('images/' . $value1['image']))) {
                        unlink(public_path('images/' . $value1['image']));
                    }
                }
                DB::table('posts_prod')->where('id_prod','=',$request->id)->delete();
                DB::table('rating_prod')->where('idProd','=',$request->id)->delete();
                productGalleryM::where('idProd', '=', $request->id)->delete();
                storageM::where('idProd', '=', $request->id)->delete();
                productM::where('id', '=', $request->id)->delete();
                return response()->json(['check' => true]);
            }
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function restoreProduct(Request $request, productM $product)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            productM::where('id', '=', $request->id)->update(['status' => 0]);
            return response()->json(['check' => true]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadDeleteProduct(Request $request, productM $product)
    {
        $result = DB::Table('products')
            ->join('categrories', 'products.idCate', '=', 'categrories.id')
            ->join('brands', 'products.idBrand', '=', 'brands.id')
            ->where('products.status', '=', 2)
            ->select('products.id as idProd', 'products.status as status', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.week', 'products.seen', 'products.created_at', 'products.updated_at', 'brands.name as brandname', 'categrories.name as catename')
            ->get();
        return response()->json(['check' => true, 'products' => $result]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(Request $request, productM $productM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            productM::where('id', '=', $request->id)->update(['status' => 2,'updated_at'=>now()]);
            return response()->json(['check' => true]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function switchProduct(Request $request, productM $productM, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $idProd = $request->id;
            $quantity = storageM::where('idProd', '=', $idProd)->sum('quantity');
            $on =storageM::where('idProd','=',$request->id)->where('status','=',1)->selectRaw('sum(quantity) AS quantity ')->value('quantity');
            $on=(int)$on;
            if ($quantity == 0) {
                productM::where('id', '=', $idProd)->update(['status' => 0]);
                return response()->json(['check' => false, 'message' => 'Sản phẩm có số lượng là 0']);
            }else if($on==0){
                $old = productM::where('id', '=', $idProd)->update(['status' => 0]);
                return response()->json(['check' => false, 'message' => 'Tồn kho sản phẩm chưa được mở']);
            } else {

                $old = productM::where('id', '=', $idProd)->value('status');
                if ($old == 0) {
                    productM::where('id', '=', $idProd)->update(['status' => 1]);
                } else {
                    productM::where('id', '=', $idProd)->update(['status' => 0]);
                }
                return response()->json(['check' => true]);
            }

        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function switchStorage(Request $request, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $result = storageM::where('id', '=', $request->id)->select('status', 'idProd')->get();
            $old = '';
            $id = '';
            foreach ($result as $key => $value1) {
                $old = $value1['status'];
                $id = $value1['idProd'];
            }
            if ($old == 0) {
                storageM::where('id', '=', $request->id)->update(['status' => 1,'updated_at'=>now()]);
                $storage =DB::Table('storage')->join('tbl_size','storage.idSize','tbl_size.id')
                ->where('storage.idProd', '=', $id)
                ->select('storage.id as id','storage.idProd as idProd','tbl_size.id as idSize',
                'tbl_size.sizename as sizename','storage.color as color','storage.quantity as quantity','storage.status as status')->get();
                return response()->json(['check' => true, 'storage' => $storage]);
            } else {
                storageM::where('id', '=', $request->id)->update(['status' => 0,'updated_at'=>now()]);
                $storage =DB::Table('storage')->join('tbl_size','storage.idSize','tbl_size.id')
                ->where('storage.idProd', '=', $id)
                ->select('storage.id as id','storage.idProd as idProd','tbl_size.id as idSize',
                'tbl_size.sizename as sizename','storage.color as color','storage.quantity as quantity','storage.status as status')->get();
                return response()->json(['check' => true, 'storage' => $storage]);
            }
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateQuantity(Request $request, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'qty' => 'required|numeric',
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
            'qty.required' => 'Thiếu số lượng sản phẩm ',
            'qty.numeric' => 'Số lượng sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            if ($request->qty == 0) {
                storageM::where('id', '=', $request->id)->update(['quantity' => $request->qty, 'status' => 0]);
            } else {
                storageM::where('id', '=', $request->id)->update(['quantity' => $request->qty]);
            }
            $id = storageM::where('id', '=', $request->id)->value('idProd');
            $storage = storageM::where('idProd', '=', $id)->get();
            return response()->json(['check' => true, 'storage' => $storage]);
        }
    }
    // ======================================================
    public function getStorage3(productM $productM, Request $request, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $result = DB::Table('storage')->join('tbl_size','storage.idSize','tbl_size.id')->where('storage.idProd', '=', $request->id)->select('storage.id as id','storage.idProd as idProd','tbl_size.id as idSize','tbl_size.sizename as sizename','storage.color as color','storage.quantity as quantity','storage.status as status')->get();
            return response()->json(['check' => true, 'storage' => $result]);
        }
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStorage2(productM $productM, Request $request, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $id = storageM::where('id', '=', $request->id)->value('idProd');
            $result = DB::Table('storage')->join('tbl_size','storage.idSize','tbl_size.id')->where('storage.idProd', '=', $id)->select('storage.id as id','storage.idProd as idProd','tbl_size.id as idSize','tbl_size.sizename as sizename','storage.color as color','storage.quantity as quantity','storage.status as status')->get();
            return response()->json(['check' => true, 'storage' => $result]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStorage(productM $productM, Request $request, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $result = DB::Table('storage')->join('tbl_size','storage.idSize','tbl_size.id')->where('storage.idProd', '=', $request->id)->select('storage.id as id','storage.idProd as idProd','tbl_size.id as idSize','tbl_size.sizename as sizename','storage.color as color','storage.quantity as quantity','storage.status as status')->get();
            return response()->json(['check' => true, 'storage' => $result]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function switchStorageSize(productM $productM, Request $request, storageM $storageM)
    {
        $validation = Validator::make($request->all(), [

            'idS' => 'required|numeric',
            'idSize' => 'required|numeric'
        ], [
            'idSize.required' => 'Thiếu mã size ',
            'idS.required' => 'Thiếu mã sản phẩm ',
            'idS.numeric' => 'Mã sản phẩm không hợp lệ ',
            'idSize.numeric' => 'Mã kích thước không hợp lệ ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            storageM::where('id','=',$request->idS)->update(['idSize'=>$request->idSize,'updated_at'=>now()]);
            return response()->json(['check' => true]);
        }
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editProduct(productM $productM, Request $request, productGalleryM $productGalleryM)
    {
        if (isset($_FILES['files'])) {
            $validation = Validator::make($request->all(), [
                'productName' => 'required|max:50|min:2',
                'slug' => 'required|max:80|min:2',
                'price' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0|max:100',
                'brandId' => 'required|numeric',
                'cateId' => 'required|numeric',
                'content' => 'required',
                'files' => 'required',
                'id' => 'required|numeric',
            ], [
                'id.required' => 'Thiếu mã sản phẩm',
                'id.numeric' => 'Mã sản phẩm không hợp lệ',
                'productName.required' => 'Thiếu tên sản phẩm',
                'slug.required' => 'Thiếu slug sản phẩm',
                'discount.required' => 'Thiếu giảm giá sản phẩm',
                'discount.numeric' => 'Discount sản phẩm không hợp lệ',
                'discount.min' => 'Discount sản phẩm không không được <0',
                'discount.max' => 'Discount sản phẩm không không được >100',
                'price.required' => 'Thiếu giá sản phẩm',
                'files.required' => 'Không có hình ảnh sản phẩm',
                'price.numeric' => 'Giá sản phẩm không hợp lệ',
                'price.min' => 'Giá sản phẩm phải lớn hơn 0',
                'brandId.required' => 'Thiếu thương hiệu sản phẩm',
                'cateId.required' => 'Thiếu loại sản phẩm',
                'content.required' => 'Thiếu nội dung sản phẩm',
                'productName.max' => 'Tên sản phẩm quá dài',
                'productName.min' => 'Tên sản phẩm ít nhất 2 ký tự',
            ]);
            if ($validation->fails()) {
                return response()->json(['check' => false, 'message' => $validation->errors()]);
            } else {
                $check = count(productM::where('slug', '=', $request->slug)->where('id','!=',$request->id)->where('price', '=', $request->price)->where('discount', '=', $request->discount)->get());
                if ($check != 0) {
                    return response()->json(['check' => false, 'message' => 'Đã tồn tại tên sản phẩm']);
                } else {
                    $filetype = $_FILES['files']['type'];
                    $accept = ['gif', 'jpeg', 'jpg', 'png', 'svg', 'jfif', 'JFIF', 'blob', 'GIF', 'JPEG', 'JPG', 'PNG', 'SVG', 'webpimage', 'WEBIMAGE', 'webpimage', 'webpimage', 'webpimage', 'webp', 'WEBP'];
                    $keyarr = [];
                    foreach ($filetype as $key => $value1) {
                        if (in_array($value1, $accept)) {
                            array_push($keyarr, $key);
                        }
                    }
                    foreach ($_FILES['files']['name'] as $key1 => $value11) {
                        if (!in_array($key1, $keyarr)) {
                            move_uploaded_file($_FILES['files']['tmp_name'][$key1], 'images/' . $value11);
                            productGalleryM::create(['idProd' => $request->id, 'image' => $value11, 'created_at' => now()]);
                        }
                    }
                }
                $imagenames = productGalleryM::where('idProd', '=', $request->id)->where('choose', '=', 2)->select('image')->get();
                foreach ($imagenames as  $value1) {
                    if (file_exists(public_path('images/' . $value1['image']))) {
                        unlink(public_path('images/' . $value1['image']));
                    }
                }
                productGalleryM::where('idProd', '=', $request->id)->where('choose', '=', 2)->delete();
                productM::where('id', '=', $request->id)
                    ->update(['name' => $request->productName, 'slug' => $request->slug, 'price' => $request->price, 'discount' => $request->discount, 'content' => $request->content, 'idCate' => $request->cateId, 'idBrand' => $request->brandId, 'week' => 0, 'seen' => 0, 'updated_at' => now()]);
                return response()->json(['check' => true]);
            }
        } else {
            $validation = Validator::make($request->all(), [
                'productName' => 'required|max:50|min:2',
                'slug' => 'required|max:80|min:2',
                'price' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0|max:100',
                'brandId' => 'required|numeric',
                'cateId' => 'required|numeric',
                'content' => 'required',
                'id' => 'required|numeric',
            ], [
                'id.required' => 'Thiếu mã sản phẩm',
                'id.numeric' => 'Mã sản phẩm không hợp lệ',
                'productName.required' => 'Thiếu tên sản phẩm',
                'slug.required' => 'Thiếu slug sản phẩm',
                'discount.required' => 'Thiếu giảm giá sản phẩm',
                'discount.numeric' => 'Discount sản phẩm không hợp lệ',
                'discount.min' => 'Discount sản phẩm không không được <0',
                'discount.max' => 'Discount sản phẩm không không được >100',
                'price.required' => 'Thiếu giá sản phẩm',
                'price.numeric' => 'Giá sản phẩm không hợp lệ',
                'price.min' => 'Giá sản phẩm phải lớn hơn 0',
                'brandId.required' => 'Thiếu thương hiệu sản phẩm',
                'cateId.required' => 'Thiếu loại sản phẩm',
                'content.required' => 'Thiếu nội dung sản phẩm',
                'productName.max' => 'Tên sản phẩm quá dài',
                'productName.min' => 'Tên sản phẩm ít nhất 2 ký tự',
            ]);
            if ($validation->fails()) {
                return response()->json(['check' => false, 'message' => $validation->errors()]);
            } else {
                $check = count(productM::where('slug', '=', $request->slug)->where('id','!=',$request->id)->where('price', '=', $request->price)->where('discount', '=', $request->discount)->get());
                if ($check != 0) {
                    return response()->json(['check' => false, 'message' => 'Đã tồn tại tên sản phẩm']);
                } else {
                    $imagenames = productGalleryM::where('idProd', '=', $request->id)->where('choose', '=', 2)->select('image')->get();
                    foreach ($imagenames as  $value1) {
                        if (file_exists(public_path('images/' . $value1['image']))) {
                            unlink(public_path('images/' . $value1['image']));
                        }
                    }
                    productGalleryM::where('idProd', '=', $request->id)->where('choose', '=', 2)->delete();
                    productM::where('id', '=', $request->id)
                        ->update(['name' => $request->productName, 'slug' => $request->slug, 'price' => $request->price, 'discount' => $request->discount, 'content' => $request->content, 'idCate' => $request->cateId, 'idBrand' => $request->brandId, 'week' => 0, 'seen' => 0, 'updated_at' => now()]);
                    return response()->json(['check' => true]);
                }
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function singleProductUser($slug,productGalleryM $productGalleryM,storageM $storageM)
    {
            $slug1 = $slug;
            $seen = productM::where('slug','=', $slug)->first();
            if($seen){
                $add = ($seen->seen)+1;
                $seen->update(['seen'=>$add]);
            }           
            $slug1 = str_replace(".html","", $slug1);
            $result = DB::Table('products')
            ->join('categrories', 'products.idCate', '=', 'categrories.id')
            ->join('brands', 'products.idBrand', '=', 'brands.id')
            ->where('products.status', '=', 1)
            ->where('products.slug', '=', $slug1)
            ->select('products.id as idProd', 'products.name','products.content', 'products.slug','discount as discount', 'products.price', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();
            $id='';
            if(count($result)==0){
                return response()->json(['check'=>false]);
            }else{
                $idCate=DB::Table('products')
                ->where('products.status', '=', 1)
                ->where('products.slug', '=', $slug1)->value('idCate');
                $idBrand=DB::Table('products')
                ->where('products.status', '=', 1)
                ->where('products.slug', '=', $slug1)->value('idBrand');
                $result4 =DB::Table('products')
                ->join('categrories', 'products.idCate', '=', 'categrories.id')
                ->join('brands', 'products.idBrand', '=', 'brands.id')
                ->where('products.status', '=', 1)
                ->where('idCate','=',$idCate)
                ->where('idBrand','=',$idBrand)
                ->orderByDesc('products.created_at')
                ->limit(4)
                ->select('products.name','products.slug','products.image as image', 'products.price','discount as discount')->get();
                $result5 = json_decode($result4,true);
                $relate=[];
                foreach ($result5 as  $value) {
                    array_push($relate,['name'=>$value['name'],'slug'=>$value['slug'],'image1'=>'http://127.0.0.1:3000/images/'.$value['image'],'price'=>$value['price'],'discount'=>$value['discount']]);
                }
                $result1 = json_decode($result,true);
                foreach ($result1 as  $value1) {
                    $id=$value1['idProd'];
                }
                $image=[];
                $result2=productGalleryM::where('idProd','=',$id)->select('image')->take(3)->get();
                foreach ($result2 as  $value1) {
                    array_push($image,'http://127.0.0.1:3000/images/'.$value1['image']);
                }
                $quantity=[];
                $result3=json_decode(DB::Table('storage')->join('tbl_size','storage.idSize','=','tbl_size.id')->where('storage.status','=',1)->where('tbl_size.status','=',1)->where('storage.idProd','=',$id)->select('storage.id as idStorage','color','tbl_size.sizename','quantity')->get(),true);
                foreach ($result3 as $value1) {
                    array_push($quantity,['id'=>$value1['idStorage'],'sizename'=>$value1['sizename'],'color'=>$value1['color'],'quantity'=>$value1['quantity']]);
                }

                return response()->json(['product'=>$result,'images'=>$image,'storage'=>$quantity,'relate'=>$relate]);
            }

    }
            /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function singlebrandprod($idbrand)
    {
        $id=$idbrand;
        $check = DB::Table('brands')->where('status',1)->where('id',$id)->count();
        if($check==0){
            return response()->json(['check'=>false,'message'=>'Không tồn tại thương hiệu']);
        }else{
            $arr1=productM::where('idBrand','=',$id)->select('id')->get();
            if(count($arr1)==0){
                return response()->json(['check'=>false,'message'=>'Không tồn sản phẩm thương hiệu']);
            }else{
                $arr2=[];
                foreach ($arr1 as $value) {
                    array_push($arr2,$value['id']);
                }
                $result = DB::Table('products')
                ->join('categrories', 'products.idCate', '=', 'categrories.id')
                ->join('brands', 'products.idBrand', '=', 'brands.id')
                ->where('products.status', '=',1)
                ->whereIn('products.id',$arr1)
                ->select('products.id as idProd','products.image as image', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();

            }
            return response()->json($result);
        }

    }
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function singlecateprod($idcate)
    {
        $id=$idcate;
        $check = DB::Table('categrories')->where('status',1)->where('id',$id)->count();
        if($check==0){
            return response()->json(['check'=>false,'message'=>'Không tồn tại loại hàng']);
        }else{
            $arr1=productM::where('idCate','=',$id)->select('id')->get();
            if(count($arr1)==0){
                return response()->json(['check'=>false,'message'=>'Không tồn sản phẩm loại hàng']);
            }else{
                $arr2=[];
                foreach ($arr1 as $value) {
                    array_push($arr2,$value['id']);
                }
                $result = DB::Table('products')
                ->join('categrories', 'products.idCate', '=', 'categrories.id')
                ->join('brands', 'products.idBrand', '=', 'brands.id')
                ->where('products.status', '=',1)
                ->whereIn('products.id',$arr1)
                ->select('products.id as idProd','products.image as image', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();

            }
            return response()->json($result);
        }

    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all2()
    {
        $result = DB::Table('products')
            ->join('categrories', 'products.idCate', '=', 'categrories.id')
            ->join('brands', 'products.idBrand', '=', 'brands.id')
            ->where('products.status', '=', 1)
            ->select('products.id as idProd','products.image as image', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();
        $arr = [];
        if(count($result)==0){
            return response()->json(['check'=>false]);
        }else{
            $result1 = json_decode($result,true);
            foreach ($result1 as  $value1) {
                $item=['name'=>$value1['name'],'slug'=>$value1['slug'],'link'=>'http://127.0.0.1:3000/api/singleProd/'.$value1['slug'].'.html','image'=>'http://127.0.0.1:3000/images/'.$value1['image'],'price'=>$value1['price'],'discount'=>$value1['discount'],'seen'=>$value1['seen'],'brand'=>$value1['brandname'],'categrories'=>$value1['catename']];
                array_push($arr,$item);
            }
                return response()->json($arr);
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all3()
    {
        $result = DB::Table('products')
            ->join('categrories', 'products.idCate', '=', 'categrories.id')
            ->join('brands', 'products.idBrand', '=', 'brands.id')
            ->where('products.status', '=', 1)
            ->where('products.gender','=',0)
            ->orderBy('products.created_at','desc')
            ->take(12)
            ->select('products.id as idProd','products.image as image','products.gender as gender', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();
        $result1 = DB::Table('products')
            ->join('categrories', 'products.idCate', '=', 'categrories.id')
            ->join('brands', 'products.idBrand', '=', 'brands.id')
            ->where('products.status', '=', 1)
            ->where('products.gender','=',1)
            ->orderBy('products.created_at','desc')
            ->take(12)
            ->select('products.id as idProd','products.image as image','products.gender as gender', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.seen', 'products.created_at as created_at', 'products.updated_at as updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();

        $arr = [];
        if(count($result)==0&&count($result1)==0){
            return response()->json(['check'=>false]);
        }else{
            $result2 = json_decode($result,true);
            $result3 = json_decode($result1,true);
            foreach ($result2 as  $value1) {
                $item=['name'=>$value1['name'],'slug'=>$value1['slug'],'gender'=>$value1['gender'],'link'=>'http://127.0.0.1:3000/api/singleProd/'.$value1['slug'].'.html','image'=>'http://127.0.0.1:3000/images/'.$value1['image'],'price'=>$value1['price'],'discount'=>$value1['discount'],'seen'=>$value1['seen'],'brand'=>$value1['brandname'],'categrories'=>$value1['catename']];
                array_push($arr,$item);
            }
            foreach ($result3 as  $value1) {
                $item=['name'=>$value1['name'],'slug'=>$value1['slug'],'gender'=>$value1['gender'],'link'=>'http://127.0.0.1:3000/api/singleProd/'.$value1['slug'].'.html','image'=>'http://127.0.0.1:3000/images/'.$value1['image'],'price'=>$value1['price'],'discount'=>$value1['discount'],'seen'=>$value1['seen'],'brand'=>$value1['brandname'],'categrories'=>$value1['catename']];
                array_push($arr,$item);
            }
                return response()->json($arr);
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(productM $productM, Request $request,storageM $storageM, productGalleryM $productGalleryM)
    {
        $idArr= productM::where('status','!=',2)->select('id')->get();
        $arr1=[];
        foreach ($idArr as $value) {
            $id=$value['id'];
            $result = DB::Table('products')
            ->join('categrories', 'products.idCate', '=', 'categrories.id')
            ->join('brands', 'products.idBrand', '=', 'brands.id')
            ->where('products.id', '=', $id)
            ->select('products.id as idProd','products.image as image', 'products.status as status','products.gender as gender', 'products.name', 'products.slug', 'products.price', 'products.discount', 'products.week', 'products.seen', 'products.created_at', 'products.updated_at', 'brands.name as brandname', 'categrories.name as catename')->get();
            $result1 = json_decode(json_encode($result),true);
            $image = productGalleryM::where('choose','=',1)->where('idProd','=',$id)->value('image');
            $total =storageM::where('idProd','=',$id)->where('status','=',1)->selectRaw('sum(quantity) AS quantity')->value('quantity');
            $on =storageM::where('idProd','=',$id)->where('status','=',1)->selectRaw('sum(quantity) AS quantity ')->value('quantity');
            if($on==null){
                $on=0;
            }
            $on=(int)$on;
            $total= (int)$total;
            $result3 = json_decode(DB::Table('storage')->where('status','=',1)->select(DB::Raw('sum(quantity) as total'),'idProd')->groupBy('storage.idProd')->get(),true);
            $arr=[];
            foreach ($result3 as $key => $value) {
               if($value['total']!=0){
                array_push($arr,$value['idProd']);
               }
            }
            DB::Table('products')->whereNotIn('id',$arr)->where('status','!=',2)->update(['status'=>0,'updated_at'=>now()]);
            foreach ($result1 as $value1) {     
                array_push($arr1 ,['idProd'=>$value1['idProd'],'gender'=>$value1['gender'],'image'=>'http://127.0.0.1:3000/images/'.$image,'on'=>$on,'status'=>$value1['status'],'total'=>$total,'name'=>$value1['name'],'slug'=>$value1['slug'],'price'=>$value1['price'],'discount'=>$value1['discount'],'week'=>$value1['week'],'seen'=>$value1['seen'],'brandname'=>$value1['brandname'],'catename'=>$value1['catename']]);
            }
            
        }
        return $arr1;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProductImage(Request $request, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',
            'name' => 'required|'
        ], [
            'name.required' => 'Thiếu tên hình ảnh ',
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',


        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            productGalleryM::where('idProd', '=', $request->id)->where('image', '=', $request->name)->update(['choose' => 2]);
            return response()->json(['check' => true,]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteImageGallery(Request $request, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [

            'id' => 'required|numeric',

        ], [
            'name.required' => 'Thiếu tên hình ảnh ',
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',


        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $images = productGalleryM::where('idProd', '=', $request->id)->where('choose', '=', 2)->select('image', 'choose')->get();
            $arr = [];
            foreach ($images as $value1) {
                $arr[] = ['link' => 'http://127.0.0.1:3000/images/' . $value1['image'], 'status' => $value1['choose'], 'imagename' => $value1['image']];
            }
            return response()->json(['check' => true, 'images' => $arr]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, productM $productM, storageM $storageM, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [
            'productName' => 'required|max:50|min:2',
            'slug' => 'required|max:80|min:2',
            'price' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0|max:100',
            'brandId' => 'required|numeric',
            'cateId' => 'required|numeric',
            'content' => 'required',
            'imageprod' => 'required',
            'files' => 'required',
            'colors' => 'required',
            'gender'=>'required|numeric|min:0|max:1',
        ], [
            'productName.required' => 'Thiếu tên sản phẩm',
            'slug.required' => 'Thiếu slug sản phẩm',
            'discount.required' => 'Thiếu giảm giá sản phẩm',
            'discount.numeric' => 'Discount sản phẩm không hợp lệ',
            'discount.min' => 'Discount sản phẩm không không được <0',
            'discount.max' => 'Discount sản phẩm không không được >100',
            'price.required' => 'Thiếu giá sản phẩm',
            'price.numeric' => 'Giá sản phẩm không hợp lệ',
            'price.min' => 'Giá sản phẩm phải lớn hơn 0',
            'gender.required'=>"Thiếu mã giới tính",
            'gender.min'=>"Mã giới tính không hợp lệ",
            'gender.max'=>"Mã giới tính không hợp lệ",
            'gender.numeric'=>"Mã giới tính không hợp lệ",
            'brandId.required' => 'Thiếu thương hiệu sản phẩm',
            'cateId.required' => 'Thiếu loại sản phẩm',
            'content.required' => 'Thiếu nội dung sản phẩm',
            'imageprod.required' => 'Thiếu hình đại diện sản phẩm',
            'files.required' => 'Thiếu hình ảnh sản phẩm',
            'colors.required' => 'Thiếu màu sắc sản phẩm',
            'productName.max' => 'Tên sản phẩm quá dài',
            'productName.min' => 'Tên sản phẩm ít nhất 2 ký tự',

        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $prodName = $request->productName;
            $slug = $request->slug;
            $price = $request->price;
            $discount = $request->discount;
            $brandId = $request->brandId;
            $cateId = $request->cateId;
            $gender = $request->gender;
            $content = $request->content;
            $imageprod = $request->imageprod;
            $colors=json_decode($request->colors,true);
            // echo gettype($colors);
            $filetype = $_FILES['files']['type'];
            $accept = ['gif', 'jpeg', 'jpg', 'png', 'svg', 'jfif', 'JFIF', 'blob', 'GIF', 'JPEG', 'JPG', 'PNG', 'SVG', 'webpimage', 'WEBIMAGE', 'webpimage', 'webpimage', 'webpimage', 'webp', 'WEBP'];
            $keyarr = [];
            foreach ($filetype as $key => $value1) {
                if (in_array($value1, $accept)) {
                    array_push($keyarr, $key);
                }
            }
            $check = count(productM::where('slug', '=', $slug)->get());
            if ($check != 0) {
                return response()->json(['check' => false, 'message' => 'Đã tồn tại sản phẩm']);
            } else {
                $id = productM::insertGetId(['name' => $prodName, 'slug' => $slug,'gender'=>$gender, 'price' => $price, 'idCate' => $cateId, 'idBrand' => $brandId, 'discount' => $discount, 'content' => $content]);
                foreach ($_FILES['files']['name'] as $key1 => $value1) {
                    if (!in_array($key1, $keyarr)) {
                        if ($value1 == $imageprod) {
                            move_uploaded_file($_FILES['files']['tmp_name'][$key1], 'images/' . $value1);
                            productGalleryM::create(['idProd' => $id, 'image' => $value1, 'choose' => 1, 'created_at' => now()]);
                            productM::where('id','=',$id)->update(['image'=>$value1]);
                        } else {
                            move_uploaded_file($_FILES['files']['tmp_name'][$key1], 'images/' . $value1);
                            productGalleryM::create(['idProd' => $id, 'image' => $value1, 'created_at' => now()]);
                        }
                    }
                }
                foreach ($colors as $value1) {
                    storageM::create(['idProd' => $id,'idSize'=>$value1[2], 'color' => $value1[0], 'quantity' => $value1[1]]);
                }
                return response()->json(['check' => true]);
            }
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\productM  $productM
     * @return \Illuminate\Http\Response
     */
    public function updatedSelectedImage(productM $productM, Request $request, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required|numeric',

        ], [
            'name.required' => 'Thiếu tên hình ảnh ',
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không hợp lệ ',


        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = count(productGalleryM::where('image', '=', $request->name)->get());
            if ($check == 0) {
                return response()->json(['check' => false, 'message' => 'Không tồn tại hình ảnh này']);
            } else {
                productGalleryM::where('image', '=', $request->name)->update(['choose' => 1]);
                productGalleryM::where('image', '!=', $request->name)->where('idProd', '=', $request->id)->where('choose', '!=', 2)->update(['choose' => 0]);
                productM::where('id', '=', $request->id)->update(['image'=>$request->name]);
                return response()->json(['check' => true]);
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\productM  $productM
     * @return \Illuminate\Http\Response
     */
    public function single(productM $productM, Request $request, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không đúng định dạng',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $result = DB::Table('products')
                ->where('products.id', $request->id)->get();
            $images = productGalleryM::where('idProd', '=', $request->id)->select('image', 'choose')->get();
            $arr = [];
            foreach ($images as $value1) {
                if ($value1['choose'] == 1) {
                    array_unshift($arr, ['link' => 'http://127.0.0.1:3000/images/' . $value1['image'], 'status' => $value1['choose'], 'imagename' => $value1['image']]);
                } else {
                    $arr[] = ['link' => 'http://127.0.0.1:3000/images/' . $value1['image'], 'status' => $value1['choose'], 'imagename' => $value1['image']];
                }
            }

            return response()->json(['check' => true, 'product' => $result, 'images' => $arr]);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\productM  $productM
     * @return \Illuminate\Http\Response
     */
    public function loadImageSingleProd(productM $productM, Request $request, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => 'Thiếu mã sản phẩm ',
            'id.numeric' => 'Mã sản phẩm không đúng định dạng',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $images = productGalleryM::where('idProd', '=', $request->id)->where('choose', '!=', 2)->select('image', 'choose')->get();
            $arr = [];
            foreach ($images as $value1) {
                if ($value1['choose'] == 1) {
                    array_unshift($arr, ['link' => 'http://127.0.0.1:3000/images/' . $value1['image'], 'imagename' => $value1['image'], 'status' => $value1['choose']]);
                } else {
                    $arr[] = ['link' => 'http://127.0.0.1:3000/images/' . $value1['image'], 'imagename' => $value1['image'], 'status' => $value1['choose']];
                }
            }
            return response()->json(['check' => true, 'images' => $arr]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\productM  $productM
     * @return \Illuminate\Http\Response
     */
    public function recallImageGallery(Request $request, productM $productM, productGalleryM $productGalleryM)
    {
        $validation = Validator::make($request->all(), [

            'name' => 'required|'
        ], [
            'name.required' => 'Thiếu tên hình ảnh ',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            productGalleryM::where('image', '=', $request->name)->update(['choose' => 0]);
            return response()->json(['check' => true,]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\productM  $productM
     * @return \Illuminate\Http\Response
     */
    public function destroy(productM $productM)
    {
        //
    }
    public function prodsHotTrend(){
        $prodTrend = productM::where('status', '=','1')->orderBy('seen','desc')->limit(3)->get();
        return response()->json($prodTrend);
    }
    public function prodsBestSeller(){
        $prodTrend = productM::where('status', '=','1')->orderBy('discount','desc')->limit(3)->get();
        return response()->json($prodTrend);
    }
    public function prodsFeature(){
        $prodTrend = productM::where('status', '=','1')->orderBy('price','asc')->limit(3)->get();
        return response()->json($prodTrend);
    }
    public function productsHomeFilter(Request $request){
        if($request->filterBy){
            $filter = $request->filterBy;
        }else{
            $filter='newest';
        }
        $page = $request->page;
        $pageSize = 8;
        if($page){
            $page = ceil($page);
            if($page<1){
                $page=1;
            }
        }
        $skip = ($page-1)*$pageSize;
        $query = productM::where('status', '=','1');
        if($filter=='men'){
            $query->where('gender', '0');
        }
        if($filter=='women'){
            $query->where('gender', '1');
        }
        if($filter=='newest'){
            $query->orderBy('id', 'desc');
        }
        $products=$query->skip($skip)->limit($pageSize)->get();
        $total = $query->count();
        $totalPage = ceil($total / $pageSize);
        return response()->json(['products'=>$products,'total'=>$total,'totalPage'=>$totalPage]);
    }
    
}
