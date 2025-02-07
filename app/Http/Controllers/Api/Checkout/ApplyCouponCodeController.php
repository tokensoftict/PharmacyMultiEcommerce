<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Models\Coupon;
use App\Models\VoucherCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ApplyCouponCodeController extends ApiController
{

    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userGroup = $checkoutUser->customer_group_id;
        $userType = $checkoutUser->customer_type_id;
        $cartTotal = $checkoutUser->calculateShoppingCartTotal();

        $discount = NULL;
        $discountType = "";

        $voucher = VoucherCode::where('app_id', ApplicationEnvironment::$id)->where('code', $request->get('code'));
        $coupon = Coupon::where('app_id', ApplicationEnvironment::$id)->where('code', $request->get('code'));


        if($voucher->count() > 0){
            $discountType = "Voucher";
            $discount = $voucher->first();

        } elseif ($coupon->count() > 0){
            $discountType = "Coupon";
            $discount = $coupon->first();

        } else {
            return $this->sendErrorResponse("Invalid Coupon or Voucher Code, Please check code and try again", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(!$discount) {
            return $this->sendErrorResponse("Invalid Coupon or Voucher Code, Please check code and try again", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($discount->status_id == status("Pending")){
            return $this->sendErrorResponse("Invalid Coupon or Voucher Code, Please check code and try again", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($discount->customer_type_id != NULL){

            if($discount->customer_type_id != $userType){

                return $this->sendErrorResponse("Invalid Coupon or Voucher Code, Please check code and try again", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }

        }


        if($discount->customer_group_id != NULL){

            if($discount->customer_group_id != $userGroup){

                return $this->sendErrorResponse( 'Invalid Coupon or Voucher Code, Please check code and try again', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

            }
        }


        //now check if coupon is in a valid date range

        $valid_from = $discount->valid_from;

        $valid_to = $discount->valid_to;

        if(!(date('Y-m-d') >= $valid_from )) {
            return $this->sendErrorResponse( 'Invalid Coupon or Voucher Code, Please check code and try again', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(!(date('Y-m-d') <= $valid_to )){
            return $this->sendErrorResponse( 'Invalid Coupon or Voucher Code, Please check code and try again', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }


        //check if the coupon is for specifc customer

        if($discount->customer_type_id != NULL && $discount->user_id != NULL) {
            if($checkoutUser->id != $discount->user_id){
                return $this->sendErrorResponse( 'Invalid Coupon or Voucher Code, Please check code and try again', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }
        }


        if($discountType === "Voucher"){
            if($discount->usage_status !="NOT-USED"){
                return $this->sendErrorResponse( 'Invalid Coupon or Voucher Code, Please check code and try again', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }
        }


        if($discountType == "Coupon"){

            if($discount->order_amount != NULL) {

                if($cartTotal === 0) return $this->sendErrorResponse( 'Your Shopping cart is empty!', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

                if (!($cartTotal >= $discount->order_amount)) {
                    return $this->sendErrorResponse( 'Your shopping cart total must be or more than ' . money($discount->order_amount), ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $count = $discount->couponUsageHistories()->where()->where('user_type_id',$checkoutUser->id)->where('user_type_type', get_class($checkoutUser))->count();

            if($count >= $discount->noofuse) {
                return $this->sendErrorResponse("You have reached the maximum usage allowed for this coupon", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }

        }


        //all validation has been check lets apply the coupon
        if($discount->type == 'Percentage'){
            $value = ($discount->type_value / 100) * $cartTotal;
            $value = ceil($value);
            $value = -$value;

        } else {
            $value = $discount->type_value;
            if(($cartTotal - $value) == 0 || ($cartTotal - $value) < 0){
                return $this->sendErrorResponse("You have reached the maximum usage allowed for this coupon", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }
            $value = -$value;
        }

        $append_name = ($discount->type =="Percentage" ? $discount->type_value."%" : money($discount->type_value));

        $order_total = [
            'name'=> $discount->name.'['.$discount->code.'] '.$append_name,
            'id'=>$discount->id,
            'disabled'=>true,
            'autocheck'=>true,
            'amount'=>$value,
            'amount_formatted'=>money($value),
            'type'=>'Discount',
            'discount_id'=>$discount->id,
            'discount_type'=>$discountType
        ];

        $checkoutUser->saveCouponData($order_total);

        return $this->sendSuccessMessageResponse("Code has been applied successfully!!..");
    }

}
