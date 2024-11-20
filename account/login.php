<?php
    if (isset($_COOKIE['user'])) {
        unset($_COOKIE['user']);
        setcookie('user', '', time()-1, '/');
        header("Location: http://localhost/letflix/");
    } else{
        require '../config.php';
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
        $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['submit'])){
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();


                if ($user && password_verify($password, $user['password'])) {
                    setcookie("user", $user['id_user'], time() + 604800, "/"); /*On reste connecté 7 jours*/?>
                    <!DOCTYPE html>

                    <html lang="fr">
                    <head>
                        <title>Leflix</title>
                        <meta charset="utf-8">
                        <link href="../css/headers.css" rel="stylesheet">
                        <link href="../css/footer.css" rel="stylesheet">
                        <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
                    </head>

                    <body>
                        <header>
                            <article id="header_pic_login">
                                <a id="header_picture" href="http://localhost/letflix/">
                                    <img src="../images/default/letflix_long_icon.png" alt="LetFlix Logo: lettre en rouge ">
                                </a>
                            </article>
                        </header>

                        <section id="login_successfully">
                            <h1>Connexion réussie, vous allez être redirigé sous peu vers la page d'accueil.</h1>
                        </section>
                    <footer class="footer">
                        <h1>Réalisé avec 💖 par © LuToine | 2019-2024</h1>
                        <p>Politique de confidentialité : <a href="../confidentiality/politique_confidentialite.pdf" target="_blank">Cliquez pour voir</a></p>
                        <p>Utilisation de l'Api de <a href="https://developer.themoviedb.org/reference/intro/getting-started" target="_blank"> TMDB </a> </p>
                    </footer>
                    </body>
                    </html>
                <?php

                } else {?>
                    <h1>Erreur lors de la connexion; re-essayez.</h1>
                    <?php
                }
                header("Refresh:2; url=http://localhost/letflix/");

            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }}}
?>