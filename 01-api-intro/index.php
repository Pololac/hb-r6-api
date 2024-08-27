<?php

require_once 'users.php';

// var_dump($users); 

header("Content-Type: application/json; charset=UTF-8");    // Entête de réponse qui indique au client que j'envoie de la donnée au format JSON

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");   // Autorise la connexion depuis l'app cliente JS

$usersJson = json_encode($users);      // convertit le tableau en string

echo $usersJson;    // Nécessaire pour ajouter du contenu au corps de la réponse
