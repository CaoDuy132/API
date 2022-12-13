<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\Api\CatePostsController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\InfoController;
use App\Http\Controllers\API\BillController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FacebookController;
use App\Http\Controllers\Api\GoogleController;
use App\Http\Controllers\API\RatingProductController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\CateController;
use App\Http\Controllers\API\SizeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routeget('/user', function (Request $request) {
//     return $request->user();
// });

// ===================
Route::middleware([checkLogin::class])->group(function () {
        Route::post('/addUserRole',[UserRoleController::class,'store']);
        Route::get('/allUSRole',[UserRoleController::class,'show']);
        Route::post('/editUserRole',[UserRoleController::class,'update']);    
        Route::post('/deleteUserRole',[UserRoleController::class,'destroy']);
        Route::post('/switchUserRole',[UserRoleController::class,'switch']);
        // ===================================================

        Route::post('addSize',[SizeController::class,'store']);
        Route::get('allSize',[SizeController::class,'all1']);
        Route::get('allSize1',[SizeController::class,'all2']);
        Route::post('switchSize',[SizeController::class,'switch']);
        Route::post('updateSizeName',[SizeController::class,'update']);
        Route::post('updateSizeInfo',[SizeController::class,'update2']);
        Route::post('deleteSize',[SizeController::class,'delete']);

        Route::post('/getStorage',[ProductController::class,'getStorage']);
        Route::post('/getStorage2',[ProductController::class,'getStorage2']);
        Route::post('/getStorage3',[ProductController::class,'getStorage3']);

        // ===================================================
        Route::post('/addUser',[UserController::class,'store']);
        Route::get('/allUser',[UserController::class,'all']);
        Route::post('/singleUser',[UserController::class,'single']);
        Route::post('/editUser',[UserController::class,'edit']);
        Route::post('/deleteUser',[UserController::class,'destroy']);
        Route::post('/switchUser',[UserController::class,'switch']);
        Route::post('/editemail',[UserController::class,'editemail']);
        // ===================================================
        Route::get('/allCate',[CateController::class,'all']);
        Route::post('/addCategory',[CateController::class,'addCate']);
        Route::post('/deleteCategory',[CateController::class,'deleteCate']);
        Route::post('/editCategory',[CateController::class,'editCate']);
        Route::post('/allCate/change-status',[CateController::class,'changeStatus']);
        Route::post('/updateOneCate',[CateController::class,'update']);
        // ===================================================
        Route::get('/allBrand',[BrandController::class,'all']);
        Route::post('/addBrand',[BrandController::class,'store']);
        Route::post('/editBrand',[BrandController::class,'edit']);
        Route::post('/switchBrand',[BrandController::class,'switch']);
        Route::post('/deleteBrand',[BrandController::class,'destroy']);
        // ===================================================

        Route::post('/addProd',[ProductController::class,'store']);
        Route::post('/getSingle',[ProductController::class,'single']);
        Route::post('/loadImageSingleProd',[ProductController::class,'loadImageSingleProd']);
        Route::post('/updatedSelectedImage',[ProductController::class,'updatedSelectedImage']);
        Route::post('/deleteImageGallery',[ProductController::class,'deleteImageGallery']);
        Route::post('/deleteProductImage',[ProductController::class,'deleteProductImage']);
        Route::post('/recallImageGallery',[ProductController::class,'recallImageGallery']);
        Route::post('/editProd',[ProductController::class,'editProduct']);
        Route::post('/switchProductGender',[ProductController::class,'switchProductGender']);
        Route::post('/switchHighlightProduct',[ProductController::class,'switchHighlightProduct']);
        
        // ===================================================

        Route::post('/updateproductName',[ProductController::class,'updateproductName']);
        Route::post('/updateProductPrice',[ProductController::class,'updateProductPrice']);
        Route::post('/updateProductDiscount',[ProductController::class,'updateProductDiscount']);
        // ===================================================



        Route::post('/editColorName',[ProductController::class,'editColorName']);
        Route::post('/updateQuantity',[ProductController::class,'updateQuantity']);
        Route::post('/switchStorage',[ProductController::class,'switchStorage']);
        Route::post('/switchProduct',[ProductController::class,'switchProduct']);
        Route::post('/deleteProduct',[ProductController::class,'deleteProduct']);
        Route::post('/loadDeleteProduct',[ProductController::class,'loadDeleteProduct']);
        Route::post('/restoreProduct',[ProductController::class,'restoreProduct']);
        Route::post('/deleteProduct2',[ProductController::class,'deleteProduct2']);
        Route::post('/deleteStorageColor',[ProductController::class,'deleteStorageColor']);
        Route::post('/addMoreColorStorage',[ProductController::class,'addMoreColorStorage']);
        Route::post('/switchStorageSize',[ProductController::class,'switchStorageSize']);


        // ====================================================
        Route::post('/addPosts',[PostsController::class,'store']);
        Route::get('/allPosts',[PostsController::class,'index']);
        Route::post('/detailPosts',[PostsController::class,'show']);
        Route::get('/allCategoriesPosts',[CatePostsController::class,'index']);
        Route::post('/updatePosts',[PostsController::class,'update']);
        Route::post('/deleteImageCoverPosts',[PostsController::class,'deleteImageCover']);
        Route::post('/deleteSoftManyPosts',[PostsController::class,'deleteSoftManyItem']);
        Route::get('/loadTrashPosts',[PostsController::class,'loadTrash']);
        Route::post('/restoreManyPosts',[PostsController::class,'restoreMany']);
        Route::post('/deleteSoftSinglePosts',[PostsController::class,'deleteSoftSingleItem']);
        Route::post('/changeFastTitlePosts',[PostsController::class,'changeFastTitle']);
        Route::post('/changeFastStatusPosts',[PostsController::class,'changeFastStatus']);
        Route::post('/forceDeleteManyPosts',[PostsController::class,'forceDeleteMany']);
        Route::post('/restoreSinglePosts',[PostsController::class,'restoreSingleItem']);
        Route::post('/forceDeleteSinglePosts',[PostsController::class,'forceDeleteSingleItem']);
        Route::post('/addCategoriesPosts',[CatePostsController::class,'store']);
        Route::post('/updateCategoriesPosts',[CatePostsController::class,'update']);
        Route::post('/deleteCategoriesPosts',[CatePostsController::class,'destroy']);
        Route::post('/changeNameFastCatePosts',[CatePostsController::class,'changeNameCatePosts']);
        Route::get('/allProdInEditBlog',[PostsController::class,'getAllProd']);
        // =============================================================
        Route::get('/allSlider',[SliderController::class,'sliders']);
        Route::get('/sliders',[SliderController::class,'sliders']);
        Route::get('/allSliderTrash',[SliderController::class,'trash']);
        Route::post('/addSlider',[SliderController::class,'addSlider']);
        Route::post('/deleteSlider',[SliderController::class,'deleteSlider']);
        Route::post('/detroySlider',[SliderController::class,'detroy']);
        Route::post('/restoreSlider',[SliderController::class,'restore']);
        Route::post('/editSlider',[SliderController::class,'editSlider']);
        Route::post('/allSlider/change-status',[SliderController::class,'changeStatus']);
        Route::post('/updateOneSlider',[SliderController::class,'update']);
        Route::post('/checkAction',[SliderController::class,'checkAction']);
        Route::get('/infoShop',[InfoController::class,'index']);  
        Route::post('/editInfo',[InfoController::class,'editInfo']);  
        Route::post('/updateLogo',[InfoController::class,'updateLogo']);  
        Route::post('/updateOneLogo',[InfoController::class,'update']);  
        Route::get('/topbills',[BillController::class,'get5Bills']);


        // =============================================================
        Route::post('/billdetail',[BillController::class,'detail']);
        Route::post('/updateSttBill',[BillController::class,'update']);
        Route::get('/allbills',[BillController::class,'all']);
        Route::POST('/chart',[BillController::class,'chart']);
});

        Route::post('/allProd',[ProductController::class,'all']);
        // ===========================================================================

        Route::get('/products',[ProductController::class,'all2']);
        Route::get('/products1',[ProductController::class,'all3']);
        Route::get('/highlightprod',[ProductController::class,'highlightProd']);
        Route::get('/productsHomeFilter',[ProductController::class,'productsHomeFilter']);
        Route::get('/productsHotTrend',[ProductController::class,'prodsHotTrend']);
        Route::get('/productsBestSeller',[ProductController::class,'prodsBestSeller']);
        Route::get('/productsFeature',[ProductController::class,'prodsFeature']);
        // ====================================================
        Route::get('/brands',[BrandController::class,'all2']);
        Route::get('/categrories',[CateController::class,'all2']);
        Route::get('/singleProd/{slug}',[ProductController::class,'singleProductUser']);
        Route::get('/singlecateprod/{id}',[ProductController::class,'singlecateprod']);
        Route::get('/singlebrandprod/{id}',[ProductController::class,'singlebrandprod']);
        Route::post('/checkUserLogin',[UserController::class,'checkLogin']);
        // ================================================================
        Route::get('/allBlogsClient',[PostsController::class,'allBlogClient']);
        Route::post('/detailsBlogClient',[PostsController::class,'getDetail']);
        Route::get('/allCateBlogClient',[PostsController::class,'allCateBlogClient']);
        Route::get('/viewMoreBlog/{id?}',[PostsController::class,'viewMoreBlog']);
        Route::get('/viewMoreBlogNormal/{id?}',[PostsController::class,'viewMoreBlogNormal']);
        Route::post('/getBlogByCate',[CatePostsController::class,'getBlogByCate']);
        Route::get('/viewMoreBlogByCate/{cate?}/{id?}',[CatePostsController::class,'viewMoreBlogByCate']);
        Route::get('/viewMoreBlogByCateNormal/{cate?}/{id?}',[CatePostsController::class,'viewMoreBlogByCateNormal']);
        // Route::post('/viewMoreBlogByCate',[CatePostsController::class,'viewMoreBlogByCate']);
        Route::post('/tagBlog',[PostsController::class,'getBlogByTag']);
        Route::get('/viewMoreBlogByTag/{idtag?}/{id?}',[PostsController::class,'viewMoreBlogByTag']);
        Route::get('/viewMoreBlogByTagNormal/{idtag?}/{id?}',[PostsController::class,'viewMoreBlogByTagNormal']);
        //=================================================================
        Route::get('/allSlider',[SliderController::class,'index']);
        Route::get('/infoShopClient',[InfoController::class,'index']);
        //=================================================================
        Route::post('/submitBill',[BillController::class,'store']);
        //===============================================================
        Route::get('/allProductShop',[ShopController::class,'sortView']);
        Route::get('/allProductMen',[ShopController::class,'sortViewMen']);
        Route::get('/allProductWomen',[ShopController::class,'sortViewWomen']);
        Route::get('/allCateClient',[CateController::class,'allCateClient']);
        Route::get('/allCateClientMen',[CateController::class,'allCateClientMen']);
        Route::get('/allCateClientWomen',[CateController::class,'allCateClientWomen']);
        Route::get('/allBrandClient',[BrandController::class,'allBrandClient']);
        Route::get('/allBrandClientMen',[BrandController::class,'allBrandClientMen']);
        Route::get('/allBrandClientWomen',[BrandController::class,'allBrandClientWomen']);
        // =================================================================
        Route::post('/client/acitve',[CustomerController::class,'active']);
        Route::post('/auth/login',[CustomerController::class,'_login']);
        Route::post('/auth/register',[CustomerController::class,'_register']);
        Route::get('/auth/logout',[CustomerController::class,'logout']);
        Route::post('/auth/login-google',[GoogleController::class,'_login']);
        Route::post('/auth/login-facebook',[FacebookController::class,'_login']);
        Route::middleware('auth:sanctum')->get('/myAccountClient',function(){
              return response()->json(auth()->user());
        });
        Route::middleware([checkPath::class])->group(function () {
                Route::post('/getUserBills',[BillController::class,'getSingleUserBill']);
                Route::post('/getUserBills2',[BillController::class,'getSingleUserBill2']);
                Route::post('/singleBill',[BillController::class,'singleBill']);
                Route::post('/updateBill',[BillController::class,'updateBill']);
        });
        /////////////////////////////////
        Route::post('/searchProductShop',[ShopController::class,'searchView']);
        Route::get('/shop/{slug}',[ShopController::class,'shopDetail']);
        Route::get('/getCateHomeTopClient_1',[CateController::class,'getCateHomeClientTop_1']);
        Route::get('/getCateHomeTopClient_4',[CateController::class,'getCateHomeClientTop_4']);

        // ===============================
        Route::post('/addRating',[RatingProductController::class,'addRating']);
        Route::get('/getAllRating',[RatingProductController::class,'getAllRating']);
        Route::post('/actionRating',[RatingProductController::class,'actionRating']);
        Route::get('/viewMoreRating/{id?}',[RatingProductController::class,'viewMoreRating']);
        Route::get('/viewMoreRatingWhenReload/{id?}',[RatingProductController::class,'viewMoreRatingWhenReload']);
        Route::get('/filterRatingByStatus/{pr?}',[RatingProductController::class,'filterRatingByStatus']);
        Route::get('/viewmoreRatingByFilter/{pr?}/{prs?}',[RatingProductController::class,'viewmoreRatingByFilter']);
        Route::get('/viewMoreRatingByFilterWhenReload/{id?}/{is?}',[RatingProductController::class,'viewMoreRatingByFilterWhenReload']);
        Route::get('/loadRealTimeRatingNof',[RatingProductController::class,'loadRealTime']);
        Route::get('/loadNotificationUserOnline/{id?}',[CustomerController::class,'loadNotificationUser']);
        Route::get('/dashboardRating',[RatingProductController::class,'dashboardRating']);