<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents | Procept</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Inter Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dkp-style.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">


    {{-- SweetAlert2 & Custom Alert Handler --}}
    <script>
        window.__FLASH__ = {
            success: @json(session('success')),
            error: @json(session('error')),
            warning: @json(session('warning')),
            info: @json(session('info')),
            errors: @json($errors->all())
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="{{ asset('js/dkp-sweetalert.js') }}" defer></script>
    {{-- pdf-lib for client-side watermarked downloads --}}
    <script src="{{ asset('vendor/pdf-lib/pdf-lib.min.js') }}"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 10% 15%, rgba(206, 230, 249, 0.7) 0%, rgba(240, 246, 252, 0.45) 45%, #f4f8fc 100%);
            min-height: 100vh;
        }
    </style>
</head>
