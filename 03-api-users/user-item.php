<?php

require_once __DIR__ . '/data/users.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");

if (!isset($_GET['id'])){
    http_response_code(400);      // 400 : Bad request
    exit;
}

$id = intval($_GET['id']);

if ($id === 0){     // intval a échoué : l'ID n'est pas un nombre
    http_response_code(400);      // 400 : Bad request
    exit;
}


// SOLUTION 1 : 
    // $user = null;

    // for ($i = 0; $i < count($users); $i++) {
    //     if ($id === $users[$i]['id']){
    //         $user = $users[$i];
    //         break;
    //     }
    // }

    // if ($user === null) {
    //     http_response_code(404); //Not found
    //     exit;
    // }

    // echo json_encode($user);


// SOLUTION 2 (plus courte) : 
    $usersFound = array_filter($users, fn ($user) => $user['id'] === $id );

    if (empty($usersFound)){
        http_response_code(404);    //Not found
        exit;       // Arrête le script complet alors que le "break" sort juste du "if"
    }

    echo json_encode(reset($usersFound));      // Fonction Reset sort la premiere valeur du tableau (sinon affichage de l'index de $usersFound)
