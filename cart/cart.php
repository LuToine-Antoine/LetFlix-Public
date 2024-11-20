<?php
if (!isset($_COOKIE['user'])){
    header("Location: http://localhost/letflix/");
}
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Leflix</title>
    <meta charset="utf-8">
    <link href="../css/headers.css" rel="stylesheet">
    <link href="../css/cart.css" rel="stylesheet">
    <link href="../css/footer.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
</head>

<body>
    <header id="header_pic_login">

        <a id="header_picture" href="http://localhost/letflix/">
            <img src="../images/default/letflix_long_icon.png" alt="LetFlix Logo: lettre en rouge ">
        </a>

            <nav id="header_logged">
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
                <section id="goHome">
                    <a href="../index.php">Retour vers l'accueil</a>
                </section>
            </nav>
    </header>

    <main>
    <?php
        require '../config.php';

        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
        $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (isset($_POST['submit'])) {

            if (isset($_POST['movie_id'], $_POST['movie_title'], $_POST['movie_poster'], $_POST['movie_overview'])) {
                $movie_id = $_POST['movie_id'];
                $movie_title = $_POST['movie_title'];
                $movie_poster = $_POST['movie_poster'];
                $movie_overview = $_POST['movie_overview'];
                $id_user = $_COOKIE['user'];
                $price = "2.50";

            try {
                $checkStmt = $pdo->prepare("
                    SELECT COUNT(*) 
                    FROM articles 
                    INNER JOIN cart ON articles.id_cart_articles = cart.id_cart
                    INNER JOIN users ON users.id_user = cart.id_user_cart
                    WHERE id_tmdb = :movie_id AND cart.id_user_cart = :id_user
                    ");
                $checkStmt->execute(['movie_id' => $movie_id, 'id_user' => $id_user]);
                $intable = $checkStmt->fetchColumn();

                if ($intable == 0) {

                    $id_cart = $pdo->prepare("SELECT id_cart FROM cart WHERE id_user_cart = :id_user");
                    $id_cart->execute(['id_user' => $id_user]);
                    $id_cart = $id_cart->fetchColumn();

                    $stmt = $pdo->prepare("INSERT INTO articles (id_tmdb, id_cart_articles, name, price) VALUES (:movie_id, :id_cart_articles, :movie_name, :movie_price)");
                    $success = $stmt->execute(['movie_id' => $movie_id, 'movie_name' => $movie_title, 'movie_price' => $price, 'id_cart_articles' => $id_cart]);

                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

    }

    if(isset($_COOKIE['user'])){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        try {
            $id_user = $_COOKIE['user'];

            $cart_info = $pdo->prepare("
                SELECT id_article, id_cart_articles, id_tmdb, name, price
                FROM articles
                INNER JOIN cart ON articles.id_cart_articles = cart.id_cart
                INNER JOIN users ON users.id_user = cart.id_user_cart
                WHERE id_user_cart = :id_user
            ");
            $cart_info->execute(['id_user' => $id_user]);
            $cart = $cart_info->fetchAll(PDO::FETCH_ASSOC);

                $price = 0;
                foreach ($cart as $item) {
                    $price += $item['price'];
                }

            if ($cart) {?>
                <section id="cart_title">
                    <div id="pay_delete">
                    <h1>Votre panier :</h1>
                    <p>Total : <?php echo $price?> € </p>
                    <div class="form_id_film">
                        <form action='delete.php' method='GET'>
                            <input type='hidden' name='pay' value='<?php echo $cart[0]['id_cart_articles'] ?>'>
                            <input type="submit" name="id" value="Valider la commande">
                        </form>
                        <form id="delete" action='delete.php' method='GET'>
                            <input type='hidden' name='delete_all' value='<?php echo $cart[0]['id_cart_articles'] ?>'>
                            <input type="submit" name="id" value="Vider le panier">
                        </form>
                    </div>
                        </div>

                </section>
                <section class="cart_placement">
        <?php
                foreach ($cart as $cart) {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                    curl_setopt_array($curl, [CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $cart['id_tmdb'] . "?api_key=" . TMDB_API_KEY . "&language=fr",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => ["accept: application/json"
                        ],
                    ]);
                    $response = curl_exec($curl);
                    if (curl_errno($curl)) {
                        echo "cURL Error #: " . curl_error($curl);
                    } else {
                        $movie_stats = json_decode($response, true);
                        ?>

                        <section class="cart_placement">
                            <article class="cart_index">
                                <div class="cart_group">
                                    <div class="cart_infos">
                                        <img class="cart_id_film_img" src='<?php echo "https://image.tmdb.org/t/p/w500" . $movie_stats['poster_path']; ?>'
                                             alt='<?php echo "Affiche du film " . $movie_stats['title']; ?>'>
                                        <h3><?php echo $movie_stats['title']?></h3>
                                    </div>
                                    <div class="form_id_film">
                                    <form action='article.php' method='POST'>
                                        <input type='hidden' name='movie_id' value='<?php echo $movie_stats['id']?>'>
                                        <input type="submit" name="id" value="Plus de détails">
                                    </form>
                                    <form id="delete" action='delete.php' method='GET'>
                                        <input type='hidden' name='id_article' value='<?php echo $cart['id_article']?>'>
                                        <input type="submit" name="id" value="Supprimer">
                                    </form>
                                    </div>
                                </div>

                            </article>
                        </section>
                        <?php
                    }

                    curl_close($curl);
                }
            }else {?>
                    <section id="cart_title">
                        <h1>Votre panier est vide :(</h1>
                    </section>

            <?php }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
    </section>
    </main>
    <footer class="footer">
        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
        <p>Politique de confidentialité : <a href="../confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
    </footer>
</body>
</html>