<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Leflix</title>
    <meta charset="utf-8">
    <link href="../css/headers.css" rel="stylesheet">
    <link href="../css/movies.css" rel="stylesheet">
    <link href="../css/text_main.css" rel="stylesheet">
    <link href="../css/footer.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
</head>

<body>
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
                <a id="redirecta" href="../account/create_account.php">Pas de compte ? Se créer un compte</a>
            </nav>
        <?php } else{?>
            <nav id="header_logged">
                <form action="../account/login.php" method="POST">
                    <label>
                        <input class="submit_login"  type="submit" name="submit" value="Se Déconnecter">
                    </label>
                </form>
                <form action="../account/user.php" method="POST">
                    <label>
                        <input type="hidden" name="user_id" value="<?php echo $_COOKIE['user'] ?>">
                        <input id="header_profil" type="submit" name="submit" value="Profil">
                    </label>
                </form>
                <a href="cart.php" id="cart">
                    <img title="Accéder au panier" src="../images/default/cart.png" alt="Logo de panier noir en fond png">
                </a>
            </nav>
        <?php } ?>
    </header>
    <main>
    <?php

    require '../config.php';

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
    $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    if (isset($_GET['id_article'])) {
        $id_article = $_GET['id_article'];
        $cart_info = $pdo->prepare("DELETE FROM articles WHERE id_article = :id_article");
        $cart_info->execute(['id_article' => $id_article]);
        $cart = $cart_info->fetchAll(PDO::FETCH_ASSOC);

        header("Location: http://localhost/letflix/cart/cart.php");
    }
    else if(isset($_GET['pay'])) {
        $id_cart = $_GET['pay'];
        $cart_info = $pdo->prepare("DELETE FROM articles");
        $cart_info->execute();
        $cart = $cart_info->fetchAll(PDO::FETCH_ASSOC);?>
        <h1> Merci pour vos achats 💕 </h1>
    <?php
        header("Refresh:3; url=http://localhost/letflix/cart/cart.php");

    }
    else if(isset($_GET['delete_all'])) {
        $id_cart = $_GET['delete_all'];
        $cart_info = $pdo->prepare("DELETE FROM articles");
        $cart_info->execute();
        $cart = $cart_info->fetchAll(PDO::FETCH_ASSOC);
        header("Location: http://localhost/letflix/cart/cart.php");
    }
    ?>
    </main>
    <footer class="footer">
        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
        <p>Politique de confidentialité : <a href="../confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
    </footer>
</body>
</html>
