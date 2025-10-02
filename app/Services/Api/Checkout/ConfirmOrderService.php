<?php

namespace App\Services\Api\Checkout;

use App\Models\PaymentMethod;
use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\SupermarketUser;
use App\Models\WholesalesAdmin;
use App\Models\WholesalesUser;

class ConfirmOrderService
{
    private WholesalesUser | SupermarketUser | WholesalesAdmin | SupermarketAdmin | SalesRepresentative| bool  $checkOutUser;
    public float $totalToPay = 0;
    public array $paymentAnalysisList = [];

    public function __construct()
    {
        $this->checkoutUser = getApplicationModel();
    }


    /**
     * @param bool $returnOnerror
     * @return array
     */
    private function computeConfirmOrder(bool $returnOnerror = true) : array
    {
        if(!$this->checkoutUser) {
            return [
                "status" => false,
                "message" => "Application user error, Please restart the application to complete your checkout"
            ];
        }

        if(count($this->checkoutUser->cart ?? []) == 0) {
            return [
                "status" => false,
                "message" => "Your Shopping cart is empty"
            ];
        }


        //get checkout user cart order
        $userSubTotal = $this->checkoutUser->calculateShoppingCartTotal();

        $subTotal = $this->checkoutUser->getUserCheckoutSubTotal($userSubTotal);
        if($subTotal['status']  === true) {
            $this->appendPaymentAnalysis($subTotal);
        } else {
            if($returnOnerror) {
                return [
                    'status' => false,
                    'message' => $subTotal['message']
                ];
            }
        }


        //get user delivery charges
        $userCheckoutDelivery = $this->checkoutUser->getUserCheckDeliveryTotal();
        if($userCheckoutDelivery['status']  === true) {
            $this->appendPaymentAnalysis( $userCheckoutDelivery);
        } else {
            if($returnOnerror) {
                return [
                    'status' => false,
                    'message' => $userCheckoutDelivery['message']
                ];
            }
        }



        //get user orderTotal
        $userOrderTotal = $this->checkoutUser->getUserCheckOutOrderTotal($userSubTotal);
        if($userOrderTotal['status']  === true) {
            $this->appendPaymentAnalysis( $userOrderTotal);
        } else {
            if($returnOnerror) {
                return [
                    'status' => false,
                    'message' => $userOrderTotal['message']
                ];
            }
        }


        //lets check if user has a coupon in its profile already applied
        if(!empty($this->checkoutUser->coupon_data) and $this->checkoutUser->coupon_data != "{}") {
            $coupon = $this->checkoutUser->coupon_data;
            $this->totalToPay = $this->totalToPay + ($coupon['amount'] ?? 0); // remove the coupon amount from the total to pay
            unset($coupon['id']); // remove the id because i dont want the mobile app to make this a check box
            $coupon['hasCoupon'] = true;
            $this->paymentAnalysisList[] = $coupon;
        }


        //calculate the PayStack charges
        if($this->checkoutUser->getCheckoutPaymentMethod()) {
            $paymentCharges = $this->checkoutUser->calculatePayStackCharges($this->totalToPay);
            if($paymentCharges['status']) {
                $this->appendPaymentAnalysis( $paymentCharges);
            }
        }

        return [
            "totalToPay" =>  $this->totalToPay,
            "totalToPayFormatted" => money($this->totalToPay),
            "paymentAnalysis" => $this->paymentAnalysisList
        ];
    }


    /**
     * @param bool $returnOnerror
     * @return int|float|array
     */
    public final function confirmOrderReturnTotal(bool $returnOnerror = true) : int|float|array
    {
        $confirmOrder =  $this->computeConfirmOrder($returnOnerror);

        if(isset($confirmOrder['status']) && $confirmOrder['status']  === false) {
            return $confirmOrder;
        }

        return $confirmOrder['totalToPay'];
    }


    /**
     * @param bool $returnOnerror
     * @return array
     */
    public final function confirmOrderReturnAnalysis(bool $returnOnerror = true) : array
    {
        $confirmOrder = $this->computeConfirmOrder($returnOnerror);

        if(isset($confirmOrder['status']) && $confirmOrder['status']  === false) {
            return $confirmOrder;
        }

        return [
            'status' => true,
            'confirmOrder' => $confirmOrder
        ];
    }


    /**
     * @param array $items
     * @return void
     */
    private function appendPaymentAnalysis(array $items) : void
    {
        foreach($items['items'] as $item) {
            $this->paymentAnalysisList[] = $item;
        }

        $this->totalToPay += $items['total'];
    }
}
