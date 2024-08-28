<?php

require_once 'functions/db.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getConnection();

// var_dump($_SERVER);

[
    'REQUEST_URI' => $uri,
    'REQUEST_METHOD' => $httpMethod
] = $_SERVER;


// SUR L'URI "/users"

if ($uri === "/users" && $httpMethod === 'GET'){
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt -> fetchAll(PDO::FETCH_ASSOC);

//Solution 1 : 
    // $usersWithUri = [];

    // for ($i=0; $i<count($users); $i++){
    //     $user = [
    //         "uri" => "/users/" . $i+1,
    //         ...$users[$i]     // "Spread operator" pour afficher les données du 2e tableau au même niveau que le 1er tableau
    //     ];
    //     $usersWithUri[] = $user;
    // }

    // echo json_encode($usersWithUri);
    // exit;

//Solution 2 : 
    // foreach ($users as &$user){     // Le "&" permet de travailler sur les éléments originaux et non sur copies mais risque de pb d'effets de bord
    //     $user['uri'] = '/users/' . $user['id'];
    // }
    // echo json_encode($users);
    // exit;

// --- Solution 3a : Cf cours "F° : anonymes & fléchées" ---
    // $output = array_map(function (array $user){
    //     return [
    //         'uri' => '/users/' . $user['id'],
    //         ...$user
    //     ];
    // }, $users);

    // echo json_encode($output);

// --- Solution 3b : f° fléchée ---
    $output = array_map(fn (array $u) => ['uri' => '/users/' . $u['id'],...$u], $users);
    echo json_encode($output);
}

if ($uri === "/users" && $httpMethod === 'POST'){
    $rawData = file_get_contents('php://input');    // Lecture du corps de la requête
    // echo $rawData;

    $data = json_decode($rawData, true);        // "true" pour créer un tableau associatif (sinon, pas défaut ça crée un objet)

    // TODO : Gestion d'erreurs (champs requis, vérification de l'existence des clés...)
    // Si erreur de validation : code 400

    $query = "INSERT INTO users (lastname, firstname, email, active) VALUES (:lastname, :firstname, :email, :active)";  // ICI marqueurs nommés et pas anonymes car plus simple pr s'y retrouver
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        'lastname' => $data['lastname'],
        'firstname' => $data['firstname'],
        'email'=> $data['email'],
        'active'=> $data['active']
    ]);

    if (!$success){
        http_response_code(500);
        echo json_encode([
            'error' => 'Impossible d\'enregistrer le nouvel utilisateur'
        ]);
        exit;
    }

    http_response_code(201);    //Created

    $id = $pdo->lastInsertId();
    $userUri = "/users/$id";
    echo json_encode([
        'id' => $id,
        'uri'=> $userUri,
        ... $data       // "Spread operator" pour afficher les données de ce tableau au même niveau que le tableau principal
    ]);

    exit;
}


// SUR L'URI "/users/id"

    // Solution avec regex plus courte
// if (preg_match("/^\/users\/(\d+)$/", $uri, $matches) && $httpMethod === 'GET') {        // le nombre entre parenthèse passe dans le $matches en position 2
//     $id = $matches[1];


$uriParts = explode('/', ltrim($uri, '/'));   //Pour détecter si ID entré dans URL
// var_dump($uriParts);

if (count($uriParts) === 2 && $uriParts[0] === 'users'){     // 2 parties dans mon URI => élément seul

    $id = intval($uriParts[1]);
    
    if ($id === 0){     // intval a échoué : l'ID n'est pas un nombre
        http_response_code(400);      // 400 : Bad request
        echo json_encode([
            'error' => 'Le format de l\'ID est incorrect'
        ]);
        exit;
    }

    //GET : 
    if ($httpMethod === 'GET'){
        $query = "SELECT * FROM users WHERE id=:id"; 
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]); 
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user === false){
            http_response_code(404);
            echo json_encode([
               'error' => 'Utilisateur non trouvé'
            ]);
            exit;
        }
    
        echo json_encode([
            'uri' => '/user/' . $user['id'],
            ... $user
        ]);
    }

    //DELETE : 
    
    if ($httpMethod === 'DELETE'){
        $query = "DELETE FROM users WHERE id=:id"; 
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        
        // Vérifie si une ligne a été affectée
        if ($stmt->rowCount() > 0) {
            // Si la suppression a réussi, renvoie un code de réponse 204 (No Content)
            http_response_code(204); //No Content
            echo json_encode([
                'message' => 'Utilisateur avec l\'ID ' . $id . 'supprimé'
            ]);

        } else {
            // Si aucune ligne n'a été affectée, renvoie un code de réponse 404 (Not Found)
            http_response_code(404);
            echo json_encode([
                'error' => 'Aucun utilisateur trouvé avec l\'ID ' . $id
            ]);
        }
    }
}
