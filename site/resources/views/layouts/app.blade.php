<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Actualités' }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: Georgia, 'Times New Roman', serif;
            color: #333;
        }

        header {
            background: #fff;
            border-bottom: 3px solid #000;
            padding: 1.5rem 0;
        }

        header .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        header a {
            text-decoration: none;
            color: #000;
            transition: color 0.2s;
        }

        header a:hover {
            color: #666;
        }

        .content {
            background: #fff;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>
            <a href="/">Actualités</a>
        </h1>
    </div>
</header>


    <div class="content">
        @yield('content')
    </div>

</body>
</html>
