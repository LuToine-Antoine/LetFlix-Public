<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Leflix</title>
    <meta charset="utf-8">
    <link href="../css/headers.css" rel="stylesheet">
    <link href="../css/movies.css" rel="stylesheet">
    <link href="../css/text_main.css" rel="stylesheet">
    <link href="../css/user.css" rel="stylesheet">
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
                <a class="redirecta" href="../account/create_account.php">Pas de compte ? Se créer un compte</a>
            </nav>
        <?php } else{?>
            <nav id="header_logged">
                <form action="../account/login.php" method="POST">
                    <label>
                        <input class="submit_login"  type="submit" name="submit" value="Se Déconnecter">
                    </label>
                </form>
                <section id="goHome">
                    <a href="../index.php">Retour vers l'accueil</a>
                </section>
                <a href="../cart/cart.php" id="cart">
                    <img title="Accéder au panier" src="../images/default/cart.png" alt="Logo de panier noir en fond png">
                </a>

            </nav>
        <?php }

            require '../config.php';
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
            $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($_POST['submit'])){
                $user['id_user'] = $_COOKIE['user'] ;

            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id_user");
                $stmt->execute(['id_user' => $user['id_user']]);
                $user_data = $stmt->fetch();
            }
            catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }}
        ?>
    </header>

    <main>
        <section class="show_infos">
            <img class="avatar" src="<?php echo $user_data['profil_icon']; ?>" alt="Photo de profil de <?php echo $user_data['username']; ?>">
            <article class="user_text_group">
                <?php
                    if ($user_data['username']){?>
                        <h1>Voici votre profil <?php echo $user_data['username'] ?></h1>
                    <?php }else{?>
                        <h1>Voici votre profil <?php echo $user_data['first_name'], " ",  $user_data['last_name'] ?></h1>
                    <?php } ?>
                <p>Nom : <span><?php echo $user_data['last_name'] ?></span></p>
                <p>Prénom : <span><?php echo $user_data['first_name'] ?></span></p>
                <p>Pseudo : <span><?php echo $user_data['username'] ?></span></p>
                <p>Adresse mail : <span><?php echo $user_data['email'] ?></span></p>
                <p>Adresse : <span><?php echo $user_data['adress'] ?></span></p>
                <p>Pays : <span><?php echo $user_data['country'] ?></span></p>
                <p>Biographie : <span><?php echo $user_data['biography'] ?></span></p>
                <p>Membre depuis : <span><?php echo $user_data['creation_date']; ?></span></p>
                <form method="post" action="update.php">
                    <input class="update_button" type="submit" name="submit" value="Mettre à jour">
                </form>
            </article>
        </section>
    </main>
    <footer class="footer">
        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
        <p>Politique de confidentialité : <a href="../confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
    </footer>
</body>
</html>