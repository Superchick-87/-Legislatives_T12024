<?php

include ('Apostrophe.php');
// include ('accesserver.php');
    try{
    $connexion = new PDO("mysql:host=$serveur;dbname=$database;charset=utf8", $login, $pass);
    $connexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    $foncsqlMenu = "SELECT Commune, Dep FROM $table";
        
    $requeteidMenu = $connexion->prepare($foncsqlMenu);
    $requeteidMenu->execute();
    $Menu = $requeteidMenu->fetchall();


    $a=0;
    $b=0;
    while ($a < count($Menu)) {
      // echo apostropheencode($Menu[$a++]['Communes'])."','";
      echo apostropheencode($Menu[$a++]['Commune']." (".$Menu[$b++]['Dep']).")','";
    }
    // print_r($Menu[$a++]['Communes']);
}
catch(PDOException $e){
  echo 'Echec de la connexion : ' .$e->getmessage();
}
  ?> 