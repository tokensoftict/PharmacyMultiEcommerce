<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .selected {
            @apply border-red-600 bg-red-50 text-red-700 ring-2 ring-red-500;
        }
    </style>
</head>
<body class="bg-white text-gray-800 font-sans">

<!-- Header -->
<div class="p-4 text-center border-b border-red-500">
    <h1 class="text-xl font-bold text-red-600">Customer Feedback</h1>
    <p class="text-sm text-gray-500">We value your opinion. Please fill out the form below.</p>
</div>

<!-- Feedback Form -->
<form class="p-4 space-y-4 pb-24" onsubmit="handleSubmit(event)">

    <!-- Full Name -->
    <div>
        <label class="block mb-1 font-medium">Full Name</label>
        <input type="text" name="fullName" required placeholder="Enter your full name"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" />
    </div>

    <!-- Phone Number -->
    <div>
        <label class="block mb-1 font-medium">Phone Number</label>
        <input type="tel" name="phone" required inputmode="numeric" placeholder="e.g. 08012345678"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" />
    </div>

    <!-- Department (as buttons) -->
    <div>
        <label class="block mb-2 font-medium">Department</label>
        <div id="departmentButtons" class="flex gap-3">
            <button type="button" class="dept-btn flex-1 border border-gray-300 rounded-lg px-4 py-3 text-center hover:bg-red-50"
                    data-value="Wholesales">Wholesales</button>
            <button type="button" class="dept-btn flex-1 border border-gray-300 rounded-lg px-4 py-3 text-center hover:bg-red-50"
                    data-value="Retail">Retail</button>
            <button type="button" class="dept-btn flex-1 border border-gray-300 rounded-lg px-4 py-3 text-center hover:bg-red-50"
                    data-value="Online">Online</button>
        </div>
        <input type="hidden" name="department" id="departmentInput" required>
    </div>

    <!-- Invoice Number -->
    <div>
        <label class="block mb-1 font-medium">Invoice Number</label>
        <input type="text" name="invoice" required placeholder="Enter invoice number"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" />
    </div>

    <!-- Staff Name -->
    <div>
        <label class="block mb-1 font-medium">Staff Name</label>
        <input type="text" name="staff" required placeholder="Enter staff name"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" />
    </div>

    <!-- Feedback Type -->
    <div>
        <label class="block mb-2 font-medium">Feedback Type</label>
        <div id="feedbackButtons" class="flex gap-4">
            <button type="button" class="fb-btn flex-1 border border-gray-300 rounded-lg px-4 py-3 text-green-600 bg-green-50 font-medium hover:bg-green-100"
                    data-value="Positive">😊 Positive</button>
            <button type="button" class="fb-btn flex-1 border border-gray-300 rounded-lg px-4 py-3 text-red-600 bg-red-50 font-medium hover:bg-red-100"
                    data-value="Negative">😞 Negative</button>
        </div>
        <input type="hidden" name="feedback_type" id="feedbackInput" required>
    </div>

    <!-- Appraisal/Complain -->
    <div>
        <label class="block mb-1 font-medium">Appraisal / Complaint</label>
        <textarea name="message" rows="4" required placeholder="Share your thoughts, compliments or complaints..."
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
    </div>
</form>

<!-- Sticky Submit Button -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg">
    <button type="submit" form="feedbackForm"
            class="w-full bg-red-600 hover:bg-red-700 text-white text-lg font-semibold py-3 rounded-xl">
        Submit Feedback
    </button>
</div>

<!-- JavaScript for selection logic -->
<script>
    const deptButtons = document.querySelectorAll('.dept-btn');
    const deptInput = document.getElementById('departmentInput');
    deptButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            deptButtons.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            deptInput.value = btn.dataset.value;
        });
    });

    const fbButtons = document.querySelectorAll('.fb-btn');
    const fbInput = document.getElementById('feedbackInput');
    fbButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            fbButtons.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            fbInput.value = btn.dataset.value;
        });
    });

    function handleSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        console.log('Form Submitted:', Object.fromEntries(formData));
        alert("Thank you for your feedback!");
        e.target.reset();
        deptButtons.forEach(b => b.classList.remove('selected'));
        fbButtons.forEach(b => b.classList.remove('selected'));
    }
</script>

</body>
</html>
