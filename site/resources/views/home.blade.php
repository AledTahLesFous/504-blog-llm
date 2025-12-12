<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #fff;
            color: #333;
            line-height: 1.6;
        }
        
        header {
            border-bottom: 3px solid #000;
            padding: 1.5rem 0;
            background: #fff;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            font-family: 'Georgia', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .date {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.3rem;
            font-family: Arial, sans-serif;
        }
        
        main {
            padding: 2rem 0;
        }
        
        .articles-list {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .article {
            border-bottom: 1px solid #ddd;
            padding: 1.5rem 0;
        }
        
        .article:first-child {
            padding-top: 0;
        }
        
        .article-meta {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 0.5rem;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .article h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .article h2 a {
            color: #000;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .article h2 a:hover {
            color: #666;
        }
        
        .article-subtitle {
            font-size: 1rem;
            color: #666;
            font-family: Arial, sans-serif;
            line-height: 1.5;
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            .article h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Actualités</h1>
            <div class="date" id="current-date"></div>
        </div>
    </header>

    <main>
        <div class="articles-list" id="articles">
            <!-- Les articles seront injectés ici par AJAX -->
        </div>
    </main>

    <script>
        // Afficher la date du jour
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('fr-FR', options);
        
        function loadArticles() {
            $.get('/articles/latest', function(data){
                $('#articles').html(data);
            });
        }

        loadArticles();
        setInterval(loadArticles, 3600000);
    </script>
</body>
</html>
