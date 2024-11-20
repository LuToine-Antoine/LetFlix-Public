<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Leflix</title>
    <meta charset="utf-8">
    <link href="../css/headers.css" rel="stylesheet">
    <link href="../css/movies.css" rel="stylesheet">
    <link href="../css/article.css" rel="stylesheet">
    <link href="../css/footer.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
</head>

<body>
    <header id="header_pic_login">

        <?php
        require '../config.php';
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
        $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_COOKIE['user'])){
            $user['id_user'] = $_COOKIE['user'] ;

            try {   $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id_user");  $stmt->execute(['id_user' => $user['id_user']]);    $user_data = $stmt->fetch();
            }
            catch (PDOException $e) {   echo "Error: " . $e->getMessage();
            }
        }
        ?>

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
                <a id="redirecta" href="../account/create_account.php">Pas de compte ? Se créer un compte</a>
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

                <a href="cart.php" id="cart">
                    <img title="Accéder au panier" src="../images/default/cart.png" alt="Logo de panier noir en fond png">
                </a>
                <section id="goHome">
                    <a href="../index.php">Retour vers l'accueil</a>
                </section>
            </nav>
        <?php } ?>
    </header>

    <main>
        <?php
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $_POST['movie_id'] . "?api_key=" . TMDB_API_KEY . "&language=fr-FR&append_to_response=credits",
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

            if ($err) {
                echo "cURL Error #:" . $err;
            }

            $movie_stats = json_decode($response, true);
        curl_close($curl);
        ?>


        <h1><?php echo $movie_stats['title']?></h1>


            <img class="movie_poster" src="<?php echo "https://image.tmdb.org/t/p/w500" . $movie_stats['poster_path'];?>" alt="Affiche du film <?php echo $movie_stats['title']?>">
            <article class="article_text_group">
                <h2>Synopsis</h2>
                <p><?php echo $movie_stats['overview'];?></p>
                <section class="movie_informations">
                    <div class="director">
                        <h2>Réalisateur</h2>
                        <?php
                        foreach ($movie_stats['credits']['crew'] as $crew){

                            if ($crew['job'] == "Director") { ?>
                                <div class="director_group">
                                    <img src="<?php echo "https://image.tmdb.org/t/p/w500" . $crew['profile_path'];?>" alt="Affiche du film <?php echo $crew['name']?>">
                                    <form method="GET" action="../search/director.php">
                                        <input type="hidden" name="actor_id" value="<?php echo $crew['id']?>">
                                        <input class="button_director_search" type="submit" name="submit" value="<?php echo $crew['name']?>">
                                    </form>
                                </div>
                        <?php }} ?>
                    </div>
                    <div class="revenue">
                        <?php if($movie_stats['revenue'] != 0) { ?>
                            <h2>Revenus du film</h2>
                        <p><?php echo $movie_stats['revenue'];?> $</p>
                    </div>
                </section>
                <p id="price">Prix : 3€ </p>
                <?php } ?>
            <?php if (!isset($_COOKIE['user'])){ ?>
                <p>Connectez-vous pour ajouter ce film à votre panier</p>
            </article>
        </aside>

        <?php } if (isset($_COOKIE['user'])){?>
                <form action='../cart/cart.php' method='POST'>
                    <input type='hidden' name='movie_id' value='<?php echo $movie_stats['id']?>'>
                    <input type='hidden' name='movie_title' value='<?php echo $movie_stats['title'] ?>'>
                    <input type='hidden' name='movie_overview' value='<?php echo $movie_stats['overview'] ?>'>
                    <input type='hidden' name='movie_poster' value='<?php echo $movie_stats['poster_path'] ?>'>
                    <input class="add_to_cart" type='submit' name='submit' value='Ajouter au panier'>
                </form>
            </article>
        </aside>
        <?php } ?>

        <h2 id="cast_title">Acteurs</h2>
        <section class="cast">
            <?php
            foreach ($movie_stats['credits']['cast'] as $actor) {?>
                <div class="cast_group">
                    <?php if(isset($actor['profile_path'])) { ?>
                    <img src="<?php echo "https://image.tmdb.org/t/p/w500" . $actor['profile_path'];?>" alt="Photo de <?php echo $actor['name']?>">
                    <?php } ?>
                    <p><?php echo $actor['name']?></p>
                </div>
            <?php } ?>
        </section>

        <?php
            curl_close($curl);
        ?>
    </main>
    <footer class="footer">
        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
        <p>Politique de confidentialité : <a href="../confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
    </footer>
</body>
