<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <meta name="description" content="Correspondence Management -  E-in-C, PUBLIC HEALTH ENGINEERING DEPARTMENT : MIZORAM. Developed by Raunak Lalmuanpuia, Software Engineer, MSeGS.">
    <meta name="keywords" content="Mizoram, Correspondence Management , phed, egovmz">
    <meta name="author" content="Raunak Lalmuanpuia">



    <title inertia>{{ config('app.name', 'PHED') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

</head>
<body style="background-color: #fbfbfc" class="font-sans antialiased">
@inertia
</body>
</html>
