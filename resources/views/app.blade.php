<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @routes
    <style>
        @font-face {
            font-family: 'Barlow';
            src: url('{{ asset('font/Barlow-Regular.woff') }}') format('woff');
            font-weight: 400;
            font-display: swap;
        }
        @font-face {
            font-family: 'Barlow';
            src: url('{{ asset('font/Barlow-SemiBold.woff') }}') format('woff');
            font-weight: 600;
            font-display: swap;
        }
        @font-face {
            font-family: 'Barlow';
            src: url('{{ asset('font/Barlow-Bold.woff') }}') format('woff');
            font-weight: 700;
            font-display: swap;
        }
        @font-face {
            font-family: 'Barlow';
            src: url('{{ asset('font/Barlow-ExtraBold.woff') }}') format('woff');
            font-weight: 800;
            font-display: swap;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>