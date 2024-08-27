<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <p>&#129303;</p>
    <p>&#128513;</p>

    <?php

    $client = curl_init("https://emojihub.yurace.pro/api/all");
    
    // Pour mettre le résultat de la requête en valeur de retour et non l'afficher à l'écran par défaut
    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($client);
    
    var_dump(json_decode($response, true));
    
    curl_close($client);


    ?>



</body>
</html>

