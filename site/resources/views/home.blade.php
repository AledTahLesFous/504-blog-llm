<!DOCTYPE html>
<html>
<head>
    <title>Articles récents</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .article { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Derniers articles</h1>

    <div id="articles">
        <!-- Les articles seront injectés ici par AJAX -->
    </div>

    <script>
        function loadArticles() {
            $.get('/articles/latest', function(data){
                $('#articles').html(data); // injecte le HTML reçu
            });
        }

        // Charger immédiatement au premier affichage
        loadArticles();

        // Puis actualiser toutes les 10 secondes
        setInterval(loadArticles, 10000);
    </script>
</body>
</html>
