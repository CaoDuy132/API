<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\infoM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class InfoController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $infos = infoM::orderBy('id')->limit(1)->get();
        return response()->json($infos);
    }
    public function editInfo(Request $request){
        $validation = Validator::make($request->all(), [
            'shopName' => 'required',
            'address' => 'required',
            'email' => 'required',
            'phoneNumber' => 'required',
            'introShop' => 'required',
        ],
        [
            'shopName.required' => 'Vui lòng nhập tên shop',
            'address.required' => 'Vui lòng nhập địa chỉ',
            'email.required' => 'Vui lòng nhập địa chỉ email',
            'phoneNumber.required' => 'Vui lòng nhập số điện thoại',
            'introShop.required' => 'Vui lòng nhập giới thiệu shop',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' =>false, 'msg' => $validation->errors() ]);
        }else{          
            $id = $request -> id;
            $shopName = $request -> shopName;
            $address = $request -> address;
            $phoneNumber = $request -> phoneNumber;
            $email = $request -> email;
            $introShop = $request -> introShop;
            if(infoM::all()->count()>0){
                infoM::find($id)->update([
                    'shopName'=>$shopName,
                    'address'=>$address,
                    'phoneNumber' => $phoneNumber,
                    'email'=>$email,
                    'introShop'=>$introShop,
                ]);
                return response()->json(['status'=>200, 'msg'=>'Cập nhật thông tin thành công!']);
            }else{
                infoM::create([
                    'shopName'=>$shopName,
                    'address'=>$address,
                    'phoneNumber' => $phoneNumber,
                    'email'=>$email,
                    'introShop'=>$introShop,
                    'logo'=>"" 
                ]);
                return response()->json(['status'=>201, 'msg'=>'Cập nhật thông tin thành công!']);
            }                      
        }
        return response()->json(['status' =>200]);   
    }
    public function updateLogo(Request $request){                      
        $file = $request -> file('file');
        $file_path = public_path('logos/' . $file);
        $fileExt = $file -> extension();
        $fileName = time().'-logo.'.$fileExt;
        if(infoM::all()->count() > 0){
            $id = $request->id;
            $info = infoM::find($id);
            $logoName=$info->logo;
            if($logoName != "" || $logoName != null){
                if(file_exists(public_path('logos/' . $logoName))) {
                    unlink(public_path('logos/' . $logoName));
                }
                $info->update(['logo'=>$fileName]);
                $file->move('logos/', $fileName);
                return response()->json(['status'=>200, 'msg'=>'Cập nhật logo thành công!']);
            }else{
                $info->update(['logo'=>$fileName]);
                $file->move('logos/', $fileName);
                return response()->json(['status'=>200, 'msg'=>'Cập nhật logo thành công!']);  
            }           
        }else{
            $file->move('logos/', $fileName);
            infoM::create([
                'logo' =>$fileName,
            ]);
            return response()->json(['status'=>201, 'msg'=>'Cập nhật logo thành công!']);
        }       
    }
}