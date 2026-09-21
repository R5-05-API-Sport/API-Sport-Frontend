<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    $data = array(
        "username" => $_POST["username"],
        "password" => $_POST["password"]
    );

    $result = postRequest("authentification.alwaysdata.net/authentification.php", $data, " ");

    if ($result["status_code"] == 200) {
        header("Location: /joueur");
        setcookie("token", $result["data"]);
    } else {
        $erreur = "Le nom d'Utilisateur ou le mot de passe est incorrect";
    }
}
?>

<body>
    <div class="CentredContainer">
        <h1>Login</h1>
        <div class="container">
            <form action="login" method="post">
                <div class="row">
                    <div class="col-20">
                        <label for="username">Username : </label>
                    </div>
                    <div class="col-80">
                        <input type="text" id="username" name="username"/><br> 
                    </div>
                </div> 
                <div class="row">
                    <div class="col-20">
                        <label for="password">Password : </label>
                    </div>
                    <div class="col-80">
                        <input type="password" id="pass" name="password"/><br>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="Login"/>
                </div>
            </form>
        </div>
        <p><?php if (isset($erreur)) { echo $erreur; } ?></p>
    </div>
</body>
</html>
