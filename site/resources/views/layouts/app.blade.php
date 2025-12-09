<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Le Blog "Les Connus"' }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        header {
            background: #111;
            padding: 20px;
            color: #fff;
            text-align: center;
            font-size: 1.4rem;
        }

        .content {
            padding: 40px 0;
        }
    </style>
</head>
<body>

    <header>
        Mon Blog Automatique
    </header>

    <div class="content">
        @yield('content')
    </div>

</body>
</html>
