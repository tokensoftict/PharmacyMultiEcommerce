<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members grouped by department.
     */
    public function __invoke(Request $request)
    {
        $staffs = Staff::where('status', true)->get();

        $groupedStaffs = [
            'retail' => $staffs->where('department', 'Retail')->values(),
            'wholesales' => $staffs->where('department', 'Wholesales')->values(),
        ];

        return response()->json([
            'status' => true,
            'data' => $groupedStaffs
        ]);
    }
}
