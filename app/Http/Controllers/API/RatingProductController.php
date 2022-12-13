<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductM;
use App\Models\RatingP;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

Carbon::setLocale('vi');
class RatingProductController extends Controller
{
    protected $hostDashboard;

    public function __construct()
    {
        //  Thay biến hostDashboard thành tên host của dashboard
        $this->hostDashboard = 'http://127.0.0.1:8000/';
    }

    public function addRating(Request $request)
    {
        if ($request->has('rating_data')) {
            RatingP::create([
                'idProd' => $request->prod,
                'idCustomer' => $request->customer,
                'num_star' => $request->rating_data,
                'content_review' => $request->user_review,
                'created_at' => now()
            ]);
            #####################################################

            $output = '';
            $result = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
            $count = $result->count();

            foreach ($result as $key => $value) {
                $output .= '             <li data-stars="' . $value->num_star . '" data-id="' . $value->id . '" class="li-each-nof-rating-realtime">
                <a href="/rating?sort-by=waiting">
                  <div class="bg-danger text-center pt-2 fw-bold text-white image image-name-circle-rating-nof">
                      ' . ucwords(mb_substr($value->customer->name, 0, 1)) . '
                  </div>
                  <div class="content">
                    <h6>
                      ' . $value->customer->name . '
                      <span class="text-regular">
                        đã có đánh giá.
                      </span>
                    </h6>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <p>
                      ' . $value->content_review . '
                    </p>
                    <span>' . Carbon::parse($value->created_at)->diffForHumans() . '</span>
                  </div>
                </a>
              </li>';
            }
            Http::post($this->hostDashboard . 'api/loadNotificationRating', [
                'output' => $output,
                'count' => $count
            ]);
            #####################################################
            return response()->json('Đang chờ chúng tôi phê duyệt , xin cảm ơn quý khách !');
        }
        // ======================
        if ($request->has('action')) {
            $average_rating = 0;
            $total_review = 0;
            $five_star_review = 0;
            $four_star_review = 0;
            $three_star_review = 0;
            $two_star_review = 0;
            $one_star_review = 0;
            $total_user_rating = 0;
            $review_content = array();

            $result = RatingP::with('customer', 'product')->where('idProd', '=', $request->prod)->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();

            foreach ($result as $row) {
                $review_content[] = array(
                    'customer'        =>    $row->customer,
                    'content_review'    =>    $row->content_review,
                    'star_rating'        =>    $row->num_star,
                    'datetime'        =>    Carbon::parse($row->created_at)->diffForHumans()
                );

                if ($row->num_star == '5') {
                    $five_star_review++;
                }

                if ($row->num_star == '4') {
                    $four_star_review++;
                }

                if ($row->num_star == '3') {
                    $three_star_review++;
                }

                if ($row->num_star == '2') {
                    $two_star_review++;
                }

                if ($row->num_star == '1') {
                    $one_star_review++;
                }

                $total_review++;

                $total_user_rating = $total_user_rating + $row->num_star;
            }


            $average_rating = $total_user_rating / $total_review;

            $output = array(
                'average_rating'    =>    number_format($average_rating, 1),
                'total_review'        =>    $total_review,
                'five_star_review'    =>    $five_star_review,
                'four_star_review'    =>    $four_star_review,
                'three_star_review'    =>    $three_star_review,
                'two_star_review'    =>    $two_star_review,
                'one_star_review'    =>    $one_star_review,
                'review_data'        =>    $review_content,
                'count'=>count($result),
            );
            return response()->json($output);
        }

        if ($request->has("average")) {
            $averge = 0;
            $total_star = 0;
            $review = 0;
            $ratings = RatingP::with('customer', 'product')->where('idProd', '=', $request->prod)->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
            foreach ($ratings as $value) {
                $review++;
                $total_star = $total_star + $value->num_star;
            }
            $averge = $total_star / $review;
            $rs = number_format($averge, 1);
            return response()->json($rs);
        }
    }

    public function getAllRating()
    {
        $result =  RatingP::with('customer', 'product')->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
        $count =  RatingP::with('customer', 'product')->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
        $total = RatingP::with('customer', 'product')->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
        return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
    }

    public function viewMoreRating($id = null)
    {
        $result =  RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
        $count =  RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
        $total = RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
        return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
    }

    public function viewMoreRatingWhenReload($id = null)
    {
        $result = [];
        $max =  RatingP::with('customer', 'product')->where('id', '>=', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
        $countMax = $max->count();

        $min = RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
        $countMin = RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
        foreach ($max as $key => $value) {
            $result[] = $value;
        }
        foreach ($min as $key => $value) {
            $result[] = $value;
        }
        return response()->json(['result' => $result, 'count' => $countMax + $countMin, 'countMin' => $countMin]);
    }
    public function filterRatingByStatus($sortby)
    {
        switch ($sortby) {
            case 'all':
                $result =  RatingP::with('customer', 'product')->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
            case 'waiting':
                $result =  RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
            case 'approved':
                $result =  RatingP::with('customer', 'product')->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
            case 'spam':
                $result =  RatingP::with('customer', 'product')->where('status', '=', 3)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('status', '=', 3)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('status', '=', 3)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
        }
    }

    public function viewmoreRatingByFilter($sortby, $id)
    {
        switch ($sortby) {
            case 'all':
                $result =  RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
            case 'waiting':
                $result =  RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 1)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 1)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 1)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
            case 'approved':
                $result =  RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 2)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
            case 'spam':
                $result =  RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 3)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $count =  RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 3)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();
                $total = RatingP::with('customer', 'product')->where('id', '<', $id)->where('status', '=', 3)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                return response()->json(['result' => $result, 'count' => $count, 'total' => $total]);
                break;
        }
    }
    public function viewMoreRatingByFilterWhenReload($sortby = null, $id = null)
    {
        $result = [];
        switch ($sortby) {
            case 'all':

                $max =  RatingP::with('customer', 'product')->where('id', '>=', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                $countMax = $max->count();

                $min = RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $countMin = RatingP::with('customer', 'product')->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();

                foreach ($max as $key => $value) {
                    $result[] = $value;
                }
                foreach ($min as $key => $value) {
                    $result[] = $value;
                }
                return response()->json(['result' => $result, 'count' => $countMax + $countMin, 'countMin' => $countMin]);
                break;
            case 'waiting':

                $max =  RatingP::with('customer', 'product')->where('status', '=', 1)->where('id', '>=', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                $countMax = $max->count();

                $min = RatingP::with('customer', 'product')->where('status', '=', 1)->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $countMin = RatingP::with('customer', 'product')->where('status', '=', 1)->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();

                foreach ($max as $key => $value) {
                    $result[] = $value;
                }
                foreach ($min as $key => $value) {
                    $result[] = $value;
                }
                return response()->json(['result' => $result, 'count' => $countMax + $countMin, 'countMin' => $countMin]);
                break;
            case 'approved':

                $max =  RatingP::with('customer', 'product')->where('status', '=', 2)->where('id', '>=', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                $countMax = $max->count();

                $min = RatingP::with('customer', 'product')->where('status', '=', 2)->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $countMin = RatingP::with('customer', 'product')->where('status', '=', 2)->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();

                foreach ($max as $key => $value) {
                    $result[] = $value;
                }
                foreach ($min as $key => $value) {
                    $result[] = $value;
                }
                return response()->json(['result' => $result, 'count' => $countMax + $countMin, 'countMin' => $countMin]);
                break;
            case 'spam':

                $max =  RatingP::with('customer', 'product')->where('status', '=', 3)->where('id', '>=', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
                $countMax = $max->count();

                $min = RatingP::with('customer', 'product')->where('status', '=', 3)->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
                $countMin = RatingP::with('customer', 'product')->where('status', '=', 3)->where('id', '<', $id)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->count();

                foreach ($max as $key => $value) {
                    $result[] = $value;
                }
                foreach ($min as $key => $value) {
                    $result[] = $value;
                }
                return response()->json(['result' => $result, 'count' => $countMax + $countMin, 'countMin' => $countMin]);
                break;
        }
    }
    public function actionRating(Request $request)
    {
        if ($request->action == 'delete') {
            foreach ($request->arr as $key => $value) {
                RatingP::where('id', '=', $value)->delete();
            }
            $output = '';
            $result = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
            $count = $result->count();
    
            foreach ($result as $key => $value) {
                $output .= '             <li data-stars="' . $value->num_star . '" data-id="' . $value->id . '" class="li-each-nof-rating-realtime">
                <a href="/rating?sort-by=waiting">
                  <div class="bg-danger text-center pt-2 fw-bold text-white image image-name-circle-rating-nof">
                      ' . ucwords(mb_substr($value->customer->name, 0, 1)) . '
                  </div>
                  <div class="content">
                    <h6>
                      ' . $value->customer->name . '
                      <span class="text-regular">
                        đã có đánh giá.
                      </span>
                    </h6>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <p>
                      ' . $value->content_review . '
                    </p>
                    <span>' . Carbon::parse($value->created_at)->diffForHumans() . '</span>
                  </div>
                </a>
              </li>';
            }
            Http::post($this->hostDashboard . 'api/loadNotificationRating', [
                'output' => $output,
                'count' => $count
            ]);
            return response()->json(['status' => 200, 'msg' => 'Đã xóa thành công !']);
        } elseif ($request->action == 'approve') {
            foreach ($request->arr as $key => $value) {
                RatingP::where('id', '=', $value)->update([
                    'status' => 2
                ]);
            }
            $output = '';
            $result = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
            $count = $result->count();
    
            foreach ($result as $key => $value) {
                $output .= '             <li data-stars="' . $value->num_star . '" data-id="' . $value->id . '" class="li-each-nof-rating-realtime">
                <a href="/rating?sort-by=waiting">
                  <div class="bg-danger text-center pt-2 fw-bold text-white image image-name-circle-rating-nof">
                      ' . ucwords(mb_substr($value->customer->name, 0, 1)) . '
                  </div>
                  <div class="content">
                    <h6>
                      ' . $value->customer->name . '
                      <span class="text-regular">
                        đã có đánh giá.
                      </span>
                    </h6>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <p>
                      ' . $value->content_review . '
                    </p>
                    <span>' . Carbon::parse($value->created_at)->diffForHumans() . '</span>
                  </div>
                </a>
              </li>';
            }
            Http::post($this->hostDashboard . 'api/loadNotificationRating', [
                'output' => $output,
                'count' => $count
            ]);
            return response()->json(['status' => 200, 'msg' => 'Đã duyệt thành công !']);
        } elseif ($request->action == 'spam') {
            foreach ($request->arr as $key => $value) {
                RatingP::where('id', '=', $value)->update([
                    'status' => 3
                ]);
            }
            $output = '';
            $result = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
            $count = $result->count();
    
            foreach ($result as $key => $value) {
                $output .= '             <li data-stars="' . $value->num_star . '" data-id="' . $value->id . '" class="li-each-nof-rating-realtime">
                <a href="/rating?sort-by=waiting">
                  <div class="bg-danger text-center pt-2 fw-bold text-white image image-name-circle-rating-nof">
                      ' . ucwords(mb_substr($value->customer->name, 0, 1)) . '
                  </div>
                  <div class="content">
                    <h6>
                      ' . $value->customer->name . '
                      <span class="text-regular">
                        đã có đánh giá.
                      </span>
                    </h6>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <p>
                      ' . $value->content_review . '
                    </p>
                    <span>' . Carbon::parse($value->created_at)->diffForHumans() . '</span>
                  </div>
                </a>
              </li>';
            }
            Http::post($this->hostDashboard . 'api/loadNotificationRating', [
                'output' => $output,
                'count' => $count
            ]);
            return response()->json(['status' => 200, 'msg' => 'Đã spam thành công !']);
        } elseif ($request->action == 'unspam') {
            foreach ($request->arr as $key => $value) {
                RatingP::where('id', '=', $value)->update([
                    'status' => 2
                ]);
            }
            $output = '';
            $result = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
            $count = $result->count();
    
            foreach ($result as $key => $value) {
                $output .= '             <li data-stars="' . $value->num_star . '" data-id="' . $value->id . '" class="li-each-nof-rating-realtime">
                <a href="/rating?sort-by=waiting">
                  <div class="bg-danger text-center pt-2 fw-bold text-white image image-name-circle-rating-nof">
                      ' . ucwords(mb_substr($value->customer->name, 0, 1)) . '
                  </div>
                  <div class="content">
                    <h6>
                      ' . $value->customer->name . '
                      <span class="text-regular">
                        đã có đánh giá.
                      </span>
                    </h6>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                    <p>
                      ' . $value->content_review . '
                    </p>
                    <span>' . Carbon::parse($value->created_at)->diffForHumans() . '</span>
                  </div>
                </a>
              </li>';
            }
            Http::post($this->hostDashboard . 'api/loadNotificationRating', [
                'output' => $output,
                'count' => $count
            ]);
            return response()->json(['status' => 200, 'msg' => 'Đã bỏ spam thành công !']);
        }
    }
    public function loadRealTime()
    {
        $output = '';
        $result = RatingP::with('customer', 'product')->where('status', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        $count = $result->count();

        foreach ($result as $key => $value) {
            $output .= '             <li data-stars="' . $value->num_star . '" data-id="' . $value->id . '" class="li-each-nof-rating-realtime">
            <a href="/rating?sort-by=waiting">
              <div class="bg-danger text-center pt-2 fw-bold text-white image image-name-circle-rating-nof">
                  ' . ucwords(mb_substr($value->customer->name, 0, 1)) . '
              </div>
              <div class="content">
                <h6>
                  ' . $value->customer->name . '
                  <span class="text-regular">
                    đã có đánh giá.
                  </span>
                </h6>
                <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                <i class="fas fa-star star-light mr-1 main_stars_' . $value->id . '"></i>
                <p>
                  ' . $value->content_review . '
                </p>
                <span>' . Carbon::parse($value->created_at)->diffForHumans() . '</span>
              </div>
            </a>
          </li>';
        }

        return response()->json(['count' => $count, 'result' => $output]);
    }

    public function dashboardRating()
    {       
            $arrId = [];
            $result = [];
            $all = RatingP::with('customer','product')->count();
            $waiting = RatingP::with('customer','product')->where('status','=',1)->count();
            $approved = RatingP::with('customer','product')->where('status','=',2)->count();
            $spam = RatingP::with('customer','product')->where('status','=',3)->count();
            $ratings = RatingP::with('customer','product')->get();
            foreach ($ratings as $key => $value) {
              $arrId[] = $value->idProd;
            }
   
            $arrId = array_values(array_unique($arrId,SORT_REGULAR));
            $products = [];
            for ($i=0; $i < count($arrId); $i++) { 
              $count = RatingP::with('customer','product')->where('idProd','=',$arrId[$i])->count();
              $products[$arrId[$i]] = $count;
            }
            arsort($products);
            foreach ($products as $key => $value) {
                $data = ProductM::with('reviews')->where('id','=',$key)->first();
                $result[$value] =  $data;
                if(count($result) == 5){
                         break;
                }
            }
            return response()->json(['all'=>$all,'waiting'=>$waiting,'approved'=>$approved,'spam'=>$spam,'products'=>$result]);
            
    }
}
