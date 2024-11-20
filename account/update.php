<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Leflix</title>
    <meta charset="utf-8">
    <link href="../css/headers.css" rel="stylesheet">
    <link href="../css/movies.css" rel="stylesheet">
    <link href="../css/text_main.css" rel="stylesheet">
    <link href="../css/form.css" rel="stylesheet">
    <link href="../css/footer.css" rel="stylesheet">
    <link href="../css/user.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
</head>

    <body>
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
                    <img class="avatar" src="<?php echo $user_data['profil_icon']; ?>" alt="Photo de profil de <?php echo $user_data['first_name']; ?>">
                    <form action="../account/login.php" method="POST">
                        <label>
                            <input class="submit_login"  type="submit" name="submit" value="Se Déconnecter">
                        </label>
                    </form>
                    <form action="user.php" method="POST">
                            <label>
                                <input type="hidden" name="user_id" value="<?php echo $_COOKIE['user'] ?>">
                                <input id="header_profil" type="submit" name="submit" value="Profil">
                            </label>
                        </form>
                    <section id="goHome">
                        <a href="../index.php">Retour vers l'accueil</a>
                    </section>
                    <a href="../cart/cart.php" id="cart">
                        <img title="Accéder au panier" src="../images/default/cart.png" alt="Logo de panier noir en fond png">
                    </a>

                </nav>
        </header>

        <main>
            <section class="update_infos">
                <?php
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
                $pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $user['id_user'] = $_COOKIE['user'] ;
                try {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id_user");
                    $stmt->execute(['id_user' => $user['id_user']]);
                    $user_data = $stmt->fetch();
                }
                catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
                <form enctype="multipart/form-data" class="form" action="" method="POST">
                    <p>Modifiez vos infos : </p>
                    <input class="input" type="text" name="first_name" placeholder="Prénom" value="<?php echo $user_data['first_name']?>" required>
                    <input class="input" type="text" name="last_name" placeholder="Nom" value="<?php echo $user_data['last_name']?>" required>
                    <input class="input" type="text" name="username" placeholder="Pseudo (facultatif)" value="<?php echo $user_data['username']?>"">
                    <input class="input" type="text" name="biography" placeholder="Biographie" value="<?php echo $user_data['biography']?>">
                    <input class="input" type="text" name="adress" placeholder="Adresse" value="<?php echo $user_data['adress']?>" required>
                    <input class="input" type="text" name="country" placeholder="Pays" value="<?php echo $user_data['country']?>" required>
                    <input class="input" type="email" name="email" placeholder="Email" value="<?php echo $user_data['email']?> (Non modifiable)" disabled>
                    <input class="input" type="password" name="password" placeholder="Password (Non modifiable)" disabled >
                    <div class="update_user_image">
                        <img class="actual_image" src="<?php echo $user_data['profil_icon']?>" alt="Profile icon of <?php echo $user_data['first_name']?>">
                    </div>
                    <input class="input" type="file" name="avatar" accept="image/jpeg, image/jpg, image/png, image/gif">
                    <input class="submit" type="submit" name="submit" value="Modifier">
                </form>
                <?php
                if (isset($_POST['submit']) && $_POST['submit'] == "Modifier") {
                    $first_name = isset($_POST['first_name']) ? strip_tags(htmlspecialchars($_POST['first_name'])) : '';
                    $last_name = isset($_POST['last_name']) ? strip_tags(htmlspecialchars($_POST['last_name'])) : '';
                    $username = isset($_POST['username']) ? strip_tags(htmlspecialchars($_POST['username'])) : '';
                    $biography = isset($_POST['biography']) ? strip_tags(htmlspecialchars($_POST['biography'])) : '';
                    $adress = isset($_POST['adress']) ? strip_tags(htmlspecialchars($_POST['adress'])) : '';
                    $country = isset($_POST['country']) ? strip_tags(htmlspecialchars($_POST['country'])) : '';
                    $id_user = $user_data['id_user']; // Utilisez les données de l'utilisateur existant pour l'ID utilisateur

                    // File modification
                    $target_dir = "../images/users/";
                    $target_file = $target_dir . basename($_FILES['avatar']['name']);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                    // Check if the image sent is an image
                    if(isset($_POST['submit'])) {
                        $check = getimagesize($_FILES['avatar']['tmp_name']);
                        if($check !== false) {
                            $uploadOk = 1;
                        } else {
                            $uploadOk = 0;
                        }
                    }

                    // Check file size
                    if ($_FILES['avatar']['size'] > 500000) {
                        $uploadOk = 0;
                    }

                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                        $uploadOk = 0;
                    }

                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 0) { ?>
                    <p><?php echo "Sorry, your file was not uploaded.";?></p>
                    <?php } else {
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) { ?>
                            <p><?php echo "The file has been uploaded.";?></p>
                        <?php } else { ?>
                            <p><?php echo "Sorry, there was an error uploading your file.";?></p>
                        <?php }
                    }
                    try {
                        $stmt = $pdo->prepare("
                        UPDATE users SET first_name = :first_name, last_name = :last_name, 
                        username = :username, profil_icon = :file , biography = :biography, adress = :adress, country = :country WHERE id_user = :id_user");
                        $success = $stmt->execute(['first_name' => $first_name,
                            'last_name' => $last_name,
                            'username' => $username,
                            'file' => $target_file,
                            'biography' => $biography,
                            'adress' => $adress,
                            'country' => $country,
                            'id_user' => $id_user
                        ]);
                        if (!$success) {
                            echo "Erreur lors dela modification de l'utilisateur";
                        }
                        else{ ?>
                            <p>Utilisateur modifié avec succès, vous allez être rediriger sous peu vers la page d'accueil.</p>
                            <?php header("Refresh:2; url=http://localhost/letflix/");
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }}
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