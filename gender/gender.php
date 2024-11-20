<?php // Ici on change la page où on veut aller : utile pour la requête tmdb
session_start();

if (!isset($_SESSION['compteur'])) {
    $_SESSION['compteur'] = 1;
}

if (isset($_POST['zero'])) {
    $_SESSION['compteur'] = 1;
}
else if (isset($_POST['next'])) {
    $_SESSION['compteur']++;
}
else if ($_SESSION['compteur'] > 1 && isset($_POST['before'])) {
    $_SESSION['compteur']--;
}
?>

    <!DOCTYPE html>

    <html lang="fr">
    <head>
        <title>Leflix</title>
        <meta charset="utf-8">
        <link href="../css/headers.css" rel="stylesheet">
        <link href="../css/movies.css" rel="stylesheet">
        <link href="../css/footer.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
    </head>

<body>

    <?php
    require '../config.php';
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
    $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_COOKIE['user'])){
        $user['id_user'] = $_COOKIE['user'] ;

        try {$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id_user");
            $stmt->execute(['id_user' => $user['id_user']]);
            $user_data = $stmt->fetch();
        }
        catch (PDOException $e) {   echo "Error: " . $e->getMessage();
        }
    }
    ?>
    <header id="header_pic_login">

        <a id="header_picture" href="http://localhost/letflix/">
            <img src="../images/default/letflix_long_icon.png" alt="LetFlix Logo: lettre en rouge ">
        </a>

        <?php if(!isset($_COOKIE['user'])){?>
            <nav id="header_login">
                <p>Se connecter : </p>
                <form action="../account/login.php" method="POST">
                    <label>
                        <input type="email" name="email" placeholder="Email" required>
                    </label>
                    <label>
                        <input type="password" name="password" placeholder="Mot de passe" required>
                    </label>
                    <label>
                        <input class="submit_login" type="submit" name="submit" value="Se connecter">
                    </label>
                </form>
                <a class="redirecta" href="../account/create_account.php">Pas de compte ? Se créer un compte</a>
                <section id="goHome">
                    <a href="../index.php">Retour vers l'accueil</a>
                </section>
            </nav>
        <?php } else{?>
            <nav id="header_logged">
                <img class="avatar" src="<?php echo $user_data['profil_icon']; ?>" alt="Photo de profil de <?php echo $user_data['first_name']; ?>">
                <form action="../account/login.php" method="POST">
                    <label>
                        <input class="submit_login"  type="submit" name="submit" value="Se Déconnecter">
                    </label>
                </form>
                <form action="../account/user.php" method="POST">
                    <label>
                        <input id="header_profil" type="submit" name="submit" value="Profil">
                    </label>
                </form>
                <a href="../cart/cart.php" id="cart">
                    <img title="Accéder au panier" src="../images/default/cart.png" alt="Logo de panier noir en fond png">
                </a>
                <section id="goHome">
                    <a href="../index.php">Retour vers l'accueil</a>
                </section>
            </nav>
        <?php } ?>
    </header>

    <main>
        <section class="movies_genres">
            <?php

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.themoviedb.org/3/genre/movie/list?language=fr-FR.desc&api_key=" . TMDB_API_KEY,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "accept: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            }

            foreach (json_decode($response, true)['genres'] as $genre) {?>
                <form action="" method="GET">
                    <input type="hidden" name="genre_id" value='<?php echo $genre['id']?>'>
                    <input class="button_genre" type="submit" name="genre" value='<?php echo $genre['name']?>'>
                </form>
            <?php }?>
        </section>

        <?php

        curl_close($curl);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=fr-FR&with_genres=" . $_GET['genre_id'] . "&page=" . $_SESSION['compteur'] . "&sort_by=popularity.desc&api_key=" . TMDB_API_KEY,    CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $response= json_decode($response, true);
        ?>

        <section class="movies_placement">
            <?php foreach ($response['results'] as $movie) { ?>
                <article class="movies_index">
                <div class="movie_group">
                    <img class="form_id_film_img" src='<?php echo "https://image.tmdb.org/t/p/w500" . $movie['poster_path']; ?>' alt='<?php echo "Affiche du film " . $movie['title']; ?>'>
                    <form class="form_id_film" action='../cart/article.php' method='POST'>
                        <input type='hidden' name='movie_id' value='<?php echo $movie['id']?>'>
                        <input type="submit" name="id" value="Plus de détails">
                    </form>
                </div>
                <div class="movies_infos">
                <h2><?php echo $movie['title']?></h2>
                <?php if(!isset($_COOKIE['user'])){ ?>
                    </div>
                    </article>
                <?php }
                if(isset($_COOKIE['user'])){ ?>
                    <form action='../cart/add_to_cart.php' method='POST'>
                        <input type='hidden' name='movie_id' value='<?php echo $movie['id']?>'>
                        <input type='hidden' name='movie_title' value='<?php echo $movie['title'] ?>'>
                        <input type='hidden' name='movie_overview' value='<?php echo $movie['overview'] ?>'>
                        <input type='hidden' name='movie_poster' value='<?php echo $movie['poster_path'] ?>'>
                        <input class="add_to_cart" type='submit' name='submit' value='Ajouter au panier'>
                    </form>
                    </article>
                <?php }
            } ?>
        </section>

        <section class="change_page">

                <p>Vous êtes à la page <?php echo $_SESSION['compteur']; ?></p>

                <form method="post">

                    <button type="submit" name="before"> < Précédente </button>
                    <button type="submit" name="zero"> Page 1 </button>
                    <button type="submit" name="next"> Suivante > </button>

                </form>
            </section>
    </main>
    <footer class="footer">
        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
        <p>Politique de confidentialité : <a href="../confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
    </footer>
</body>
</html>
<?php curl_close($curl);