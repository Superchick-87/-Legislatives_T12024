<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.2.8/d3.min.js" type="text/JavaScript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.1/d3.min.js"></script>

<?php
include(dirname(__FILE__) . '/includes/accesserver.php');
include(dirname(__FILE__) . '/includes/ddc.php');
include(dirname(__FILE__) . '/includes/Apostrophe.php');

@$commune = apostropheencode($_POST['Commune']);
@$dep = $_POST['Dep'];

function replaceNan($tring)
{
  $tring = str_replace("/", "<br>", $tring);
  $tring = str_replace(", ", "<br>", $tring);
  // $tring = str_replace(",","<br>",$tring);
  return $tring;
}

/*----------  Connexion à la bdd  ----------*/
$connexion = new PDO("mysql:host=$serveur;dbname=$database;charset=utf8", $login, $pass);
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*----------  Récupération et affichage des données  ----------*/
$Renc = $connexion->query("SELECT*FROM $table WHERE Commune = '$commune' AND Dep = '$dep'");
$data = $Renc->fetch();

/*----------  Vérife des données  ----------*/
if (($data['Commune'] | $data['Dep']) == NULL) {
  echo "<p class='alerte'>Oh non ;-((</p>
    <img src='css/images/Erreur.png'>
    <p>Votre recherche ne donne aucun résultat !<br>Vous devez sélectionner une commune dans la liste après avoir tapé les premières lettres.</p>
    <br>";
} else {
  echo "<input id='CodeCommune' style='display:none;' type='text' value=" . $data['CodeCommune'] . ">";
  echo "<input id='LatCommune' style='display:none;' type='text' value=" . $data['LatCommune'] . ">";
  echo "<input id='LongCommune' style='display:none;' type='text' value=" . $data['LongCommune'] . ">";
  echo '
    <div class="hautLigne1 declHaut">
        <div id="Commune" class="titre">' . $data['Commune'] . '
        </div>
        <p class="txtCourant">Les habitants de la commune votent</br>dans la <b>' . $data['Circo'] . '</b> qui compte ' . number_format($data['PopCirco'], 0, ',', ' ') . ' habitants.</p>
       <br>
       <p class="txtCourant">En 2017, l\'élection dans la circonscription</br>avait été remportée par <b>' . justifListeBis($data['Sortants']) . '</b>.</p><br>
        <div class="chiffresBig">' . $data['Inscrits'] . '
        </div>
        <div class="candidats">Inscrits</div>
        <h2>L\'abstention au 1<sup>er</sup> tour</h2>
        <div class="blocListe_center">' . justifListe($data['Abstention_commune']) . '' . justifListe($data['Abstention_circo']) . '
        </div>
       <div style="display:none;" id="DDD" style="margin:0px auto;">
            <svg id="Absmap">
        </div>
        <div style="display:none;" id="deptLabstention"></div>    
        <hr>
        <div id="viz" style="margin:0px auto;">
            <svg id="map">
                   <defs>
                      <filter x="0" y="0" width="1" height="1" id="solid">
                        <feFlood flood-color="black" result="bg" />
                        <feMerge>
                          <feMergeNode in="bg"/>
                          <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                      </filter>
                      <pattern id="hach_grise" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#999898" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_bleuemarine" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#14387F" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_bleue" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#0771B8" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_bleueclaire" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#00B1EB" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_verte" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="##95C11F" stroke-width="6" />
                      </pattern>
                      </pattern>
                      <pattern id="hach_rose" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#ff89ff" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_orange" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#FBBA00" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_rouge" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#E30613" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_violette" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#B60F80" stroke-width="6" />
                      </pattern>
                      <pattern id="hach_marron" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                        <line x1="6" y1="0" x2="6" y2="50" stroke="#654519" stroke-width="6" />
                      </pattern>
                  </defs>
            </svg>
        </div>

    </div>
    <hr>
    <h2  id="elus" style="width:95%;">Elu(e) au premier tour</h2>
    <div id="elus_C" class="blocListe">' . justifListe($data['Elu_au_premier_tour']) . '</div>
    
    <h2 id="qualifies" style="width:95%;">Les candidats en lice au second tour (19 juin 2022)</h2>
    <div id="qualifies_C" class="blocListe">' . justifListe($data['Qualifies']) . '</div>
    <hr>
    
    <h2 id="NonQualifies" style="width:95%;">Éliminés au 1<sup>er</sup> tour (12 juin 2022)</h2>
    <div id="NonQualifies_C" class="blocListe">' . justifListe($data['Non_qualifies']) . '</div>

    <img class="visuelBas" src="css/images/visuelBas.png">';
};
