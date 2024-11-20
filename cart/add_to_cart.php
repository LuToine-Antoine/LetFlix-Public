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
    header('Location: ' . $_SERVER['HTTP_REFERER']);

}
?>