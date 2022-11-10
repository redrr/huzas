<?php
//Küldő mail cím
$noreplyAddress = "noreply@wewrite.hu";

$name = $chosen = $email = "";
if (!empty($_POST["name"])) {
    $name = $_POST["name"];
}
if (!empty($_POST["email"])) {
    $email = $_POST["email"];
}
if (!empty($_POST["chosen"])) {
    $chosen = $_POST["chosen"];
}
try {
    $message = 'Szia '.$name.'!<br><br>Az általad húzott személy: '.$chosen.'.<br><br>Kellemes karácsonyi készülődést kívánunk!';
    $header = "From: ".$noreplyAddress."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";
    //endregion
    mail($email,"Karácsonyi húzás", $message, $header);
    echo true;
} catch (Exception $e) {
    echo false;
}
?>
