<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackSubmitted;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {

        //        $allowedDomains = ['https://feedback.generaldrugcentre.com/', 'http://localhost:8080/'];
//        $origin = $request->headers->get('origin');
//
//        if (!in_array($origin, $allowedDomains)) {
//            return response()->json(['message' => 'Unauthorized origin'], 403);
//        }


        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'store' => 'required|in:physical,online',
            'department' => 'required|in:wholesales,retail',
            'rating' => 'required|integer|min:1|max:5',
            'staffId' => 'nullable|exists:staffs,id',
            'feedbackType' => 'required|in:positive,negative',
        ]);

        $feedback = Feedback::create([
            'full_name' => $validated['fullName'],
            'phone_number' => $validated['phoneNumber'],
            'store' => ucfirst($validated['store']),
            'department' => ucfirst($validated['department']),
            'invoice_number' => $validated['invoiceNumber'] ?? null,
            'rating' => $validated['rating'],
            'staff_id' => $validated['staffId'] ?? null,
            'feedback_type' => ucfirst($validated['feedbackType']),
            'feedback' => $validated['feedback'],
        ]);

        Mail::to('info@generaldrugcentre.com')->queue(new FeedbackSubmitted($feedback));


        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'data' => $feedback
        ], 201);
    }
}
