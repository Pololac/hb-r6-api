<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <p>&#129411;</p>
    <p>&#128020;</p>

    
    <?php

    //REQUETE GET

    $client = curl_init("https://emojihub.yurace.pro/api/all/group/animal-bird");
    
    // Pour mettre le résultat de la requête en valeur de retour et non l'afficher à l'écran par défaut
    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($client);
    
    // var_dump(json_decode($response, true));
    
    $emoji = json_decode($response, true);
    
    $turkey = $emoji[0]['htmlCode'][0];
    
    for ($i=0; $i<count($emoji); $i++){
        if ($emoji[$i]['name'] === 'duck'){
            $duck = $emoji[$i]['htmlCode'][0];
        }
    }

    curl_close($client);

    ?>

    <p><?=$turkey?></p>
    <p><?=$duck?></p>

    <?php

//REQUETE POST

    // $list = ['name' => 'Test', 'description' => "This is an empty list with no books added yet."];

    $credentials = ['username' => 'mor_2314', 'password' => "83r5^_"];

    $client = curl_init("https://fakestoreapi.com/auth/login");

    // Pour effectuer une requête POST
    curl_setopt($client, CURLOPT_POST, true);

    // On fixe les headers de la requête
    curl_setopt($client, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // On fixe les champs du corps de la requête en utilisant les identifiants
    curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($credentials));

    // Pour mettre le résultat dans la valeur de retour
    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($client);

    var_dump(json_decode($response, true));

    ?>


</body>
</html>

