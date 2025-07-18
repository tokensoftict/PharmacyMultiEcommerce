<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        /*
        $allowedDomains = ['https://feedback.generaldrugcentre.com/', 'http://localhost:8080/'];
        $origin = $request->headers->get('origin');

        if (!in_array($origin, $allowedDomains)) {
            return response()->json(['message' => 'Unauthorized origin'], 403);
        }


        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'department' => 'required|in:wholesales,retail,online',
            'invoiceNumber' => 'required|string|max:100',
            'staffName' => 'required|string|max:255',
            'feedbackType' => 'required|in:positive,negative',
            'feedback' => 'required|string',
        ]);

        $feedback = Feedback::create([
            'full_name' => $validated['fullName'],
            'phone_number' => $validated['phoneNumber'],
            'department' => $validated['department'],
            'invoice_number' => $validated['invoiceNumber'],
            'staff_name' => $validated['staffName'],
            'feedback_type' => $validated['feedbackType'],
            'feedback' => $validated['feedback'],
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'data' => $feedback
        ], 201);
    }
}
