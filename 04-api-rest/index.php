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

if ($uri === "/users" && $httpMethod == 'GET'){
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    $usersWithUri = [];

    for ($i=0; $i<count($users); $i++){
        $user = ["uri" => "/users/" . $i+1, ...$users[$i]];
        $usersWithUri[] = $user;
    }

    echo json_encode($usersWithUri);
    exit;
}

if ($uri === "/users" && $httpMethod == 'POST'){
    $rawData = file_get_contents('php://input');    // Lecture du corps de la requête
    // echo $rawData;

    $data = json_decode($rawData, true);        // "true" pour créer un tableau associatif (sinon, pas défaut ça crée un objet)

    // TODO : Gestion d'erreurs (champs requis, vérification de l'existence des clés...)
    // Si erreur de validation : code 400

    $query = "INSERT INTO users (lastname, firstname, email, active) VALUES (:lastname, :firstname, :email, :active)";
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        'lastname' => $data['lastname'],
        'firstname' => $data['firstname'],
        'email'=>$data['email'],
        'active'=>$data['active']
    ]);

    if (!$success){
        http_response_code(500);
        echo json_encode([
            'error' => 'Impossible d\'enregistrer le nouvel utilisateur'
        ]);
        exit;
    }

    $id = $pdo->lastInsertId();
    $userUri = "/users/$id";

    http_response_code(201);    //Created

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

if (count($uriParts) === 2 && $uriParts[0] === 'users'){     // 2 parties dans mon URI : élément seul

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
    // echo json_encode(['rip']);
    
    if ($httpMethod === 'DELETE'){
        $query = "DELETE * FROM users WHERE id=:id"; 
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        
        http_response_code(204);    //No Content
    }

}


