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
    <link href="css/headers.css" rel="stylesheet">
    <link href="css/movies.css" rel="stylesheet">
    <link href="css/text_main.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/default/letflix-logo.png">
    </head>

<body>
    <header id="header_pic_login">

        <?php
        require 'config.php';
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

        <div id="header_picture">
            <img src="images/default/letflix_long_icon.png" alt="LetFlix Logo: lettre en rouge ">
        </div>

        <?php if(!isset($_COOKIE['user'])){?>
            <nav id="header_login">
                <p>Se connecter : </p>
                <form action="account/login.php" method="POST">
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
                <a class="redirecta" href="account/create_account.php">Pas de compte ? Se créer un compte</a>
            </nav>
        <?php } else{ ?>
            <nav id="header_logged">
                <img class="avatar" src="<?php echo substr_replace($user_data['profil_icon'], "", 0, 3); ?>" alt="Photo de profil de <?php echo $user_data['first_name']; ?>">
                <form action="account/login.php" method="POST">
                    <label>
                        <input class="submit_login"  type="submit" name="submit" value="Se Déconnecter">
                    </label>
                </form>
                <form action="account/user.php" method="POST">
                    <label>
                        <input type="hidden" name="user_id" value="<?php echo $_COOKIE['user'] ?>">
                        <input id="header_profil" type="submit" name="submit" value="Profil">
                    </label>
                </form>
                <a href="cart/cart.php" id="cart">
                    <img title="Accéder au panier" src="images/default/cart.png" alt="Logo de panier noir en fond png">
                </a>
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
                <form action="gender/gender.php" method="GET">
                    <input type="hidden" name="genre_id" value='<?php echo $genre['id']?>'>
                    <input class="button_genre" type="submit" name="genre" value='<?php echo $genre['name']?>'>
                </form>
            <?php }

            curl_close($curl);

            ?>
        </section>
        <?php
        include 'search/search_bar.php';
        ?>
        <section id="website_text">
            <h1 id="title_align">Bienvenue sur <span id="text_title">Letflix</span></h1>
            <p>
                Bienvenue sur Letflix, la plateforme incontournable pour tous les amateurs de cinéma ! Chez Letflix, nous avons pour mission de vous offrir une expérience cinématographique exceptionnelle, où que vous soyez. Notre catalogue diversifié regorge de joyaux cinématographiques, des classiques intemporels aux derniers succès en salle, en passant par des films indépendants remarquables.
                <br>
                Naviguez à travers notre interface conviviale, explorez nos recommandations personnalisées et plongez dans un monde de divertissement sans fin. Que vous recherchiez des drames poignants, des comédies hilarantes, des thrillers palpitants ou des documentaires inspirants, Letflix a tout ce qu'il vous faut pour combler vos envies cinéphiles.
            </p>
            <h2>Notre sélection du moment : </h2>
        </section>
        <article>
            <section>
                <?php


                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);


                    curl_setopt_array($curl, [ // Creation de la session pour accès a l'API TMDB
                        CURLOPT_URL => "https://api.themoviedb.org/3/authentication/guest_session/new?api_key=" . TMDB_API_KEY,
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
                    } else {

                        $curl = curl_init();

                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                        curl_setopt_array($curl, [ // Récupération des films populaires (sans adultes 👀)
                            CURLOPT_URL => "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=fr-FR&page="
                                . $_SESSION['compteur']. "&sort_by=popularity.desc&api_key=" . TMDB_API_KEY,
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
                        }}
                        $response = json_decode($response, true);
                        ?>
                <section class="movies_placement">
                    <?php
                    foreach ($response['results'] as $movie) {
                        ?>
                        <article class="movies_index">
                        <div class="movie_group">
                            <img class="form_id_film_img" src='<?php echo "https://image.tmdb.org/t/p/w500" . $movie['poster_path']; ?>' alt='<?php echo "Affiche du film " . $movie['title']; ?>'>
                            <form class="form_id_film" action='cart/article.php' method='POST'>
                                <input type='hidden' name='movie_id' value='<?php echo $movie['id']?>'>
                                <input type="submit" name="id" value="Plus de détails">
                            </form>
                        </div>
                        <div class="movies_infos">
                        <h3><?php echo $movie['title']?></h3>
                        <?php if(!isset($_COOKIE['user'])){ ?>
                            </div>
                            <p class="price_display">3€</p>
                            </article>
                        <?php } ?>
                        <?php if(isset($_COOKIE['user'])){ ?>
                            <p class="price_display">3€</p>
                            <form action='cart/add_to_cart.php' method='POST'>
                                <input type='hidden' name='movie_id' value='<?php echo $movie['id']?>'>
                                <input type='hidden' name='movie_title' value='<?php echo $movie['title'] ?>'>
                                <input type='hidden' name='movie_overview' value='<?php echo $movie['overview'] ?>'>
                                <input type='hidden' name='movie_poster' value='<?php echo $movie['poster_path'] ?>'>
                                <input class="add_to_cart" type='submit' name='submit' value='Ajouter au panier'>
                            </form>
                            </article>
                        <?php } ?>
                        <?php
                    }
                    ?>
                </section>

                <section class="change_page">

                    <p>Vous êtes à la page <?php echo $_SESSION['compteur']; ?></p>

                    <form method="POST">

                        <button type="submit" name="before"> < Précédente </button>
                        <button type="submit" name="zero"> Page 1 </button>
                        <button type="submit" name="next"> Suivante > </button>

                    </form>
            </section>
        </main>
    <footer class="footer">
        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
        <p>Politique de confidentialité : <a href="confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
    </footer>
    </body>
</html>
