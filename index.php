<html>
<meta charset="utf-8">

<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.2.8/d3.min.js" type="text/JavaScript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.2.8/d3.min.js" type="text/JavaScript"></script>
  <script src="https://rawgit.com/moment/moment/2.2.1/min/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.1/d3.min.js"></script>
  <script src="js/1121_jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <!-- css pour l'autoplementation -->
  <link rel="stylesheet" href="css/jquery-ui.css">
  <!-- FIN css pour l'autopletion -->
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
  <meta name="viewport" content="width=device-width" />
</head>
<?php
include(dirname(__FILE__) . '/includes/accesserver.php');
include(dirname(__FILE__) . '/includes/editeur.php');
?>

<body>
  <div class="recherche">
    <form action="">
      <label for="Ville">
        <h2>Entrez le nom d'une commune</h2>
      </label>
      <input type="text" id="Ville" name="Ville"><br>
    </form>
    <button id="Go">RECHERCHER</button>
  </div>
  <div id="txtHint">
  </div>
  <br>
  <div id="comment">
    <h4>Comment ça marche</h4>
    <p class="txtCourant">Saisissez le nom d’une commune située dans la zone de diffusion de votre quotidien <b>"<?php echo personalisation($_GET['Editeur']); ?>"</b> dans le champ de recherche ci-dessus pour connaître candidats en lice au second tour des élections législatives (19 juin 2022).</p>
    <hr>
    <img id="visuel" class="visuel" src="css/images/visuel.png">
    <hr>
    <p class="txtCourant">Source : Ministère de l’Intérieur.</p>
  </div>
  <footer style="background-image:url(css/images/Signature<?php echo $_GET['Editeur']; ?>.svg);"></footer>
</body>

<script type="text/javascript">
  window.onload = autocompletion();
  document.getElementById("Go").addEventListener("click", showCustomer);



  /* Fonction sert à l'autocompletion */
  function autocompletion() {
    var gpA10 = [<?php echo "'", include(dirname(__FILE__) . '/includes/menu.php'), "'"; ?>];
    // console.log(gpA10)
    $("#Ville").autocomplete({
      source: gpA10
    });
  }

  /* Fonction Ajax */
  function showCustomer(str) {
    var MenuA = document.getElementById('Ville').value;
    var commune = MenuA.slice(0, length - 5);
    var comment = document.getElementById('comment');

    var dep = (MenuA.slice(-4)).replace('(', '').replace(')', '');
    // console.log(dep)

    var xhttp;
    if (str == "") {
      document.getElementById("txtHint").innerHTML = "";
      comment.style.display = "block";
      return;
    }
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("txtHint").innerHTML = this.responseText;
        comment.style.display = "none";
        selectMap(dep);
        afficheMasque();
      }
    };


    /* Methode GET -> passe une seule variable */
    /* Methode POST -> passe plusieurs variables */
    // xhttp.open("GET", "getuser.php?Ville="+MenuA,true);
    // xhttp.send();
    xhttp.open("POST", "getuser.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("Commune=" + commune + "&Dep=" + dep);
  };
</script>
<script type="text/javascript">
  // =======================================================
  // =            Fonction sert à afficher la carte            =
  // =========================================================

  /*----------  Sert à sélectionner la fonction carte correspondante à var "dep" (n° du dep)  ----------*/

  function selectMap(a) {
    if (a === '16') {
      let map16 = new Map(16, 9400, [-0.00138, 45.603744], [90, 145], 250, 250, 16);
      return map16.afficheMap();
    }
    if (a === '17') {
      let map17 = new Map(17, 9300, [-0.00138, 45.603744], [240, 185], 250, 320, 16);
      return map17.afficheMap();
    }
    if (a === '24') {
      let map24 = new Map(24, 8600, [0.7572205, 45.1469486], [130, 125], 250, 250, 16);
      return map24.afficheMap();
    }
    if (a === '33') {
      let map33 = new Map(33, 8600, [-0.612943, 44.827778], [105, 157], 250, 300, 16);
      return map33.afficheMap();
    }
    if (a === '40') {
      let map40 = new Map(40, 8600, [-0.612943, 44.827778], [140, -50], 250, 250, 16);
      return map40.afficheMap();
    }
    if (a === '47') {
      let map47 = new Map(47, 11000, [0.4502368, 44.2470173], [120, 155], 250, 250, 16);
      return map47.afficheMap();
    }
    if (a === '64') {
      let map64 = new Map(64, 10000, [-0.7532809, 43.3269942], [180, 80], 320, 220, 16);
      return map64.afficheMap();
    }
  }

  /*----------  Fin Sert à sélectionner la fonction carte correspondante à var "dep" (n° du dep)  ----------*/

  /*----------  Constructeur  ----------*/
  function Map(d, s, c, t, w, h, z) {
    this.departement = d;
    this.departement2 = z;
    this.scale = s;
    this.center = c;
    this.translate = t;
    this.width = w;
    this.height = h;

    /*----------  Prototype  ----------*/
    this.afficheMap = function() {
      var promises = [
        d3.json('js/maps/communes-' + this.departement + '.geojson'),
        d3.csv('js/datas/Done_T1.csv')
      ]

      Promise.all(promises).then(function(value) {
        var map = value[0]
        var data = value[1]
        map.features.forEach(d => {
          var result = data.filter(dep => d.properties.code == dep.code)
          d.properties.ColorT1 = (result[0] !== undefined) ? result[0].ColorT1 : ''
          d.properties.Color_hachT1 = (result[0] !== undefined) ? result[0].Color_hachT1 : '"none"'
          d.properties.Abs_Commune = (result[0] !== undefined) ? result[0].Abs_Commune : '"none"'
        })
        parseData(map)
      })

      console.log(s)
      console.log([t[0], t[1]])

      function parseData(data) {

        var aProjection = d3.geoMercator()
          .scale(s)
          .center([c[0], c[1]])
          .translate([t[0], t[1]]);

        var geoPath = d3.geoPath().projection(aProjection);

        var toto = d3.select("svg#map").attr("width", w).attr("height", h).selectAll("g").data(data.features)
          .enter()
          .append('g')
          .attr('id', d => d.properties.code)
          .attr("opacity", ".5")
          .append("path")
          .attr("d", geoPath)
          .attr("class", d => d.properties.ColorT1);

        var tata = d3.select("svg#map").attr("width", w).attr("height", h).selectAll("g").data(data.features)
          .append("path")
          .attr("d", geoPath)
          .attr("class", d => d.properties.Color_hachT1)
          .style('stroke', 'white')
          .style('stroke-width', '0.5');

        var svg = d3.select("svg#map"),
          width = +svg.attr("width"),
          height = +svg.attr("height");

        var MenuA = document.getElementById('Ville').value;
        var commune = MenuA.slice(0, length - 5);
        var comment = document.getElementById('comment');

        var LatCommune = document.getElementById('LatCommune').value;
        var LongCommune = document.getElementById('LongCommune').value;
        var markers = [{
          Long: LongCommune,
          Lat: LatCommune
        }];

        var dep = (MenuA.slice(-4)).replace('(', '').replace(')', '');

        var carte2 = d3.select('svg#map')
        d3.json('js/maps/circos-' + dep + '.geojson').then(function(geojson) {
          svg.append("g")
            .attr('id', 'carte2')
            .selectAll("path")
            .data(geojson.features)
            .enter()
            .append("path")
            .attr("d", geoPath)
            .style('fill', 'none')
            .style('stroke', 'black')
            .style('stroke-width', '2')
            .style('stroke-opacity', '1')

          d3.select("svg#map").selectAll("circle").data(markers)
            .enter()
            .append("circle")
            .attr('id', 'point')
            .attr('r', 10 + 'px')
            .attr('class', 'rouge')
            .attr("d", geoPath)
            .attr("cx", d => aProjection([d.Long, d.Lat])[0])
            .attr("cy", d => aProjection([d.Long, d.Lat])[1])

          d3.select("svg#map").selectAll("text").data(markers)
            .enter()
            .append("g")
            .append("text")
            .attr('id', 'loc')
            .attr('filter', 'url(#solid)')
            .attr('fill', 'white')
            .attr('text-anchor', 'middle')
            // .attr('class', positionTxt(aProjection([d.Long, d.Lat])[0], ''))
            .text(commune)
            .attr("x", d => aProjection([d.Long, d.Lat])[0])
            .attr("y", d => aProjection([d.Long, d.Lat])[1] - 17 + 'px')
          var communeEncours = document.getElementById(CodeCommune);
          communeEncours.setAttribute('opacity', '1');
        })

        // ABSTENTION
        var min = Math.round(d3.min(data.features, d => d.properties.Abs_Commune))
        var max = Math.round(d3.max(data.features, d => d.properties.Abs_Commune))
        var mean = Math.round(d3.mean(data.features, d => d.properties.Abs_Commune))
        console.log(min * 2, max)

        var areaScale = d3.scaleLinear()
          .domain([min, max])
          .range(['white', '#2D1500']);
        //CARTE ABS
        // var mm =
        d3.select('svg#Absmap')
          .attr("width", w).attr("height", h)
          .append('g')
          .attr('id', 'carteLabstention')
          .selectAll('path')
          .data(data.features)
          .enter()
          .append('path')
          .attr('d', geoPath)
          .style('stroke', 'none')
          .style('fill', d => areaScale(d.properties.Abs_Commune));

        //LEGENDE
        var width = 320,
          height = 40;

        var svgAbs = d3.select("div#deptLabstention")
          .append("svg")
          .attr("width", width)
          .attr("height", height)
          .attr("preserveAspectRatio", "xMinYMin meet")
          .attr("viewBox", "-70 -10 480 60")
          .attr("id", 'echelleabs');

        var scale = d3.scaleQuantile()
          .domain([min, mean, max])
          .range([-108, 0, 92]);

        // Add scales to axis
        var x_axis = d3.axisTop()
          .scale(scale);

        //Append group and insert axis
        svgAbs.append("g")
          .attr("transform", "translate(158, 20)")
          .attr("id", 'graduationLabstention')
          .call(x_axis);

        //COLORBAR
        var colors = d3.scaleLinear()
          .domain([0, 10, 100])
          .range(['white', '#2D1500']);

        var legend = svgAbs.append('g')
          .attr('transform', 'translate(40, 10)')
          .attr('id', 'legendLabstention');

        legend.selectAll('.colorbar')
          .data(d3.range(10))
          .enter()
          .append("rect")
          .attr("y", 10)
          .attr("height", 25)
          .attr("x", (d, i) => 10 + i * 20)
          .attr("width", 25)
          .attr("fill", d => colors(d));

        // ABSTENTION FIN

        /*----------  Sert à récupérer le nom de la commune pour afficher texte ----------*/

        var MenuA = document.getElementById('Ville').value;
        var commune = MenuA.slice(0, length - 5);

        var v = '';

        function positionTxt(a, v) {
          if (a < 55) {
            v = 'locR noir'
            return v;
          }
          if (a > 56) {
            v = 'loc noir'
            // else{
            return v;
          }
        }
        console.log(width)
        // d3.select("svg#map").selectAll("text").data(markers)
        //         .enter()
        //           .append("text")
        //             .attr('id','loc')
        //             .attr('class',positionTxt(aProjection([d.Long,d.Lat])[0],''))
        //             .text(commune)
        //             .attr("x", d => aProjection([d.Long,d.Lat])[0])
        //             .attr("y", d => aProjection([d.Long,d.Lat])[1]-7+'px') 
        var communeEncours = document.getElementById(CodeCommune);
        communeEncours.setAttribute('opacity', '1');

      }

      var CodeCommune = document.getElementById('CodeCommune').value;
      // console.log(CodeCantons)      

    }

  }

  // <!--====  End of Fonction sert à afficher la carte  ====-->

  // ==================================================================================
  // =            Fonction sert à afficher ou masquer les blocs si contenu            =
  // ==================================================================================
  function afficheMasque() {
    var elus_C = document.getElementById('elus_C').innerHTML;
    var qualifies_C = document.getElementById('qualifies_C').innerHTML;
    var NonQualifies_C = document.getElementById('NonQualifies_C').innerHTML;
    // console.log(elus_C)
    var elus = document.getElementById('elus');
    var qualifies = document.getElementById('qualifies');
    var nonQualifies = document.getElementById('NonQualifies');

    if (elus_C === '') {
      elus.style.display = 'none';
    }
    if (qualifies_C === '') {
      qualifies.style.display = 'none';
    }
    if (NonQualifies_C === '') {
      nonQualifies.style.display = 'none';
    }
  }
  // <!--====  End of Fonction sert à afficher ou masquer les blocs si contenu  ====-->
</script>
<script src="js/camelize.js" type="text/javascript"></script>

</html>