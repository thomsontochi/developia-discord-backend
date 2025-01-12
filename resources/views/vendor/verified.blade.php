<!DOCTYPE html>
<html>
<head>
    <title>Email Verified - Vendly</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full mx-4">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="mt-4 text-2xl font-semibold text-gray-900">Email Verified Successfully!</h2>
                    <p class="mt-2 text-gray-600">Your vendor account email has been verified.</p>
                    
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-4">You can now proceed with setting up your store.</p>
                        <a href="/api/v1/vendor/login" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Continue to Vendor Login
                        </a>
                    </div>

                    <div class="mt-6 text-xs text-gray-500">
                        <p>Note: In the future, you will be redirected to the vendor dashboard automatically.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

