<?php
if (isset($_COOKIE['user'])){
    header("Location: http://localhost/letflix/");
}
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Leflix - Nouveau compte</title>
    <meta charset="utf-8">
    <link href="../css/headers.css" rel="stylesheet">
    <link href="../css/form.css" rel="stylesheet">
    <link href="../css/footer.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/default/letflix-logo.png">
</head>

<body>
    <header>
        <article id="header_pic_login">
            <a id="header_picture" href="http://localhost/letflix/">
                <img src="../images/default/letflix_long_icon.png" alt="LetFlix Logo: lettre en rouge ">
            </a>
            <section id="goHome">
                <a href="../index.php">Retour vers l'accueil</a>
            </section>
        </article>
    </header>

    <main>
        <section id="header_login">

                <form enctype="multipart/form-data" class="form" action="create_account.php" method="POST">
                    <p>Merci de compléter les informations ci-dessous :</p>
                    <input class="input" type="text" name="first_name" placeholder="Prénom" required>
                    <input class="input" type="text" name="last_name" placeholder="Nom" required>
                    <input class="input" type="text" name="username" placeholder="Pseudo (facultatif)">
                    <input class="input" type="email" name="email" placeholder="Email" required>
                    <input class="input" type="password" name="password" placeholder="Password" required>
                    <input class="input" type="text" name="biography" placeholder="Biographie">
                    <input class="input" type="text" name="adress" placeholder="Adresse" required>
                    <input class="input" type="text" name="country" placeholder="Pays" required>
                    <div class="update_user_image">
                        <img class="actual_image" src="../images/default/letflix-logo.png" alt="Avatar par défaut LT">
                    </div>
                    <input class="input" type="file" name="avatar" accept="image/jpeg, image/jpg, image/png, image/gif">
                    <input class="submit" type="submit" name="submit" value="S'inscrire">
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

<?php
require '../config.php';
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=". DB_NAME ,DB_USER , DB_PASSWORD);
$pdo->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_POST['submit'])) {
    $first_name = htmlspecialchars($_POST['first_name']);
    $first_name = strip_tags($first_name);
    $last_name = htmlspecialchars($_POST['last_name']);
    $last_name = strip_tags($last_name);
    $username = htmlspecialchars($_POST['username'])?: NULL;
    if ($username) {
        $username = strip_tags($username); // Empêche d'entrer des balises php et html dans le code
    }
    $email = htmlspecialchars($_POST['email']);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password']);
    $password = strip_tags($password);
    $biography = htmlspecialchars($_POST['biography'])?: NULL;
    if ($biography){
    $biography = strip_tags($biography);
    }
    $adress = htmlspecialchars($_POST['adress']);
    $adress = strip_tags($adress);
    $country = htmlspecialchars($_POST['country']);
    $country = strip_tags($country);
    $hash = password_hash($password, PASSWORD_DEFAULT);


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
    if ($uploadOk == 0) {
        ?> <p> <?php echo "Sorry, your file was not uploaded."; ?></p>
    <?php } else {
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
           ?> <p> <?php echo "The file has been uploaded."; ?></p>
        <?php } else {
            ?> <p> <?php echo "Sorry, there was an error uploading your file."; ?></p>
        <?php }
    }


    try {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, profil_icon, email, password, biography, adress, country) VALUES (:first_name, :last_name, :username, :file, :email, :password, :biography, :adress, :country)");
        $succes = $stmt->execute(['first_name' => $first_name, 'last_name' => $last_name, 'username' => $username, 'file' => $target_file, 'email' => $email, 'password' => $hash, 'biography' => $biography, 'adress' => $adress, 'country' => $country]);

        $id_user_request = $pdo->prepare("SELECT id_user FROM users WHERE email = :email");
        $id_user_request->execute(['email' => $email]);
        $id_user = $id_user_request->fetch(PDO::FETCH_ASSOC);

        $create_cart = $pdo->prepare("INSERT INTO cart (id_user_cart) VALUES (:id_user)");
        $create_cart->execute(['id_user' => $id_user['id_user']]);

        if ($succes) {?>
            <h1>Utilisateur ajouté avec succès, vous allez être redirigé sous peu vers la page d'accueil.</h1>
            <?php
        } else {?>
            <h1>Erreur lors de l'ajout, veuillez re-essayer.</h1>
            <?php

        }
        header("Refresh:3; url=http://localhost/letflix/");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }}
$pdo = null;
?>
