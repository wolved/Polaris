<!-- 
    Header Module
    Bevat: doctype, html, head, meta tags, Tailwind CDN, eigen CSS, en opent body.
    Included bovenaan elke pagina via: include 'includes/header.php';
-->
<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Polaris - Centraal interventie- en meldingsportaal voor operationeel beheer en coördinatie.">
    <meta name="author" content="Polaris">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Polaris' : 'Polaris - Interventieportaal'; ?></title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind configuratie, aangepaste kleuren voor het Polaris thema
        // Aangezien we tijdelijk werken met een cdn doen we het via hier.
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#e7eaf0',
                            100: '#c3c9d9',
                            200: '#9ba5bf',
                            300: '#7280a5',
                            400: '#546591',
                            500: '#354a7e',
                            600: '#2f4376',
                            700: '#27396b',
                            800: '#1f3061',
                            900: '#121f4e',
                            950: '#0a1330',
                        },
                        slate: {
                            750: '#253248',
                        },
                        accent: {
                            DEFAULT: '#3b82f6',
                            light: '#60a5fa',
                            dark: '#2563eb',
                        },
                    },
                    fontFamily: {
                        header: ['Bebas Neue', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <!-- Eigen stylesheet -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="bg-slate-50 text-slate-800 font-body antialiased min-h-screen flex flex-col">