/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * @namespace
 */
var iep = {};

/**
 * Si on est en dev
 * @type {boolean}
 */
iep.isDev = /(\.devsesamath\.net|\.local)$/.test(window.location.hostname) || window.location.href.substr(0, 7) === "file://";
iep.caracteresGrecs = ["alpha","beta","gamma","delta","epsilon","zeta","eta"," theta","kappa",
	"lambda","mu","nu","xi","pi","rho","sigma","tau","phi","chi",
	"psi","omega","Gamma","Delta","Xi","Pi","Sigma","Phi","Chi","Psi","Omega"];
iep.caracteresGrecsUtf8 = ["α","β","γ","δ","ε","ζ","η","θ","κ",
  "λ","μ","ν","ξ","π","ρ","σ","τ","φ","χ",
  "ψ","ω","Γ","Δ","Ξ","Π","Σ","Φ","Χ","Ψ","Ω"];

/**
 * Fonction de log en dev (ne fait rien en prod)
 * @param {*} … autant d'arguments que l'on veut, les erreur seront envoyées à console.error et le reste à console.log
 */
iep.log = function () {
  if (iep.isDev && typeof console !== "undefined" && console.log && console.error) {
    var i, arg;
    for (i = 0; i < arguments.length; i++) {
      arg = arguments[i];
      if (arg instanceof Error) console.error(arg);
      else console.log(arg);
    }
  }
}

/**
 * Le namespace svg
 * @type {string}
 */
iep.svgsn = "http://www.w3.org/2000/svg";

/**
 * Fonction renvoyant true si le nombre x a une valeur absolue inférieue à 10^-9
 * @param {float} x
 * @returns {boolean}
 */
iep.zero = function(x) {
  return (Math.abs(x)<1e-9);
};

/**
 * Fonction renvoyant true si les vecteurs u et v sont quasi colinéaires
 * @param {iep.vect} u
 * @param {iep.vect} v
 * @returns {boolean}
 */
iep.colineaires = function(u, v)
{
  var n1 = u.norme();
  var n2 = v.norme();
  if (iep.zero(n1) || iep.zero(n2)) return true;
  else return (iep.zero((u.x*v.y-u.y*v.x)/n1/n2));
};
/**
 * Fonction renvoyant true si les vecteurs u et v sont quasi colinéaires
 * de même sens
 * @param {iep.vect} u
 * @param {iep.vect} v
 * @returns {boolean}
 */
iep.colineairesMemeSens = function(u, v) {
  return iep.colineaires(u,v) && (u.x*v.x + u.y*v.y >= 0);
};
/**
 * Fonction renvoyant true si la valeur absolue de a est inférieure à 10^-7
 * Utilisé pour tester si un angle est quasi nul.
 * @param {number} a
 * @returns {boolean}
 */
iep.zeroAngle = function(a) {
    return (Math.abs(a)<1e-7);
};
// Constantes globales utilisés dans les calculs
iep.convDegRad = Math.PI/180;
iep.convRadDeg = 180/Math.PI;
iep.cos30 = Math.cos(30*iep.convDegRad);
iep.sin30 = 0.5;
/**
 * Fonction renvoyant la mesure principale en degrés de l'angle dont ang est une mesure
 * @param {Float} ang
 * @returns {Float}
 */
iep.mesurePrincDeg = function(ang) {
  var q = Math.floor((ang+180)/360);
  var ret = ang - 360*q;
  if (ret === -180) ret === 180;
  return ret;
};
/**
 * Fonction servant à parser un fichier xml contenu dans la chaîne txt
 * @param {string} txt
 * @returns {DOMParser}
 */
iep.parseXMLDoc = function(txt) {
  if (window.DOMParser) {
      parser=new DOMParser();
      xmlDoc=parser.parseFromString(txt,"text/xml");
    }
  else { // Internet Explorer
      xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
      xmlDoc.async=false;
      xmlDoc.loadXML(txt);
  }
  return xmlDoc;
};
/**
 * Fonction renvoyant true si ch contient le nom d'un instrument.
 * @param {type} ch
 * @returns {boolean}
 */
iep.estInstrument = function(ch) {
  return ch==="compas" || ch==="compasLeve" || ch==="crayon" || ch==="equerre"||
          ch==="rapporteur" || ch==="regle" || ch==="requerre";
};
/**
 * Fonction appelée pour une chaîne qui commence par un caractère de balise ouvrante.
 * Avant appel la chaîne doit commencer par une balise ouvrante
 * Renvoie -1 s'il n'y a pas de balise fermante correspondante
 * @param {string} ch
 * @returns {int} Renvoie l'indice du dernier caractère de la balise fermante correspondante
 * augmentée de 1
 */
iep.indiceFinBalise = function(ch) {
  var bal,balf,indf;
  var balisefont = false;
  if (ch.indexOf("<font") === 0) {
    balf = "</font>";
    balisefont = true;
  }
  else {
    indf = ch.indexOf(">");
    if (indf === -1) return ch.length; // Erreur
    bal = ch.substring(0,indf+1);
    balf = bal.substring(0,1)+"/"+bal.substring(1);
  }
  var ind = ch.indexOf(balf);
  if (ind === -1) {
    // Il arrive que des gens oublient de fermer une baise font et en ouvrent une autre
    if (balisefont) {
      var ind2 = ch.indexOf("<font",5);
      if (ind2 !== -1) return ind2;
    }
    return -1;
  } // Incorrect. Balise de fn oubliée ?
  else return ind + balf.length;
};
/**
 * Fonction analysant une chaîne commençant par £e( ou £i(
 * et renvoyant un objet formé de l'opérande et de l'exposant (ou indice)
 * @param {type} chaine
 * @returns {iep.analyseExposantOuIndice.res|Object}
 */
iep.analyseExposantOuIndice = function(chaine) {
  var ch = chaine.trim();
  var res = {};
  var indvirg = ch.indexOf(",");
  var indparf = ch.indexOf(")");
  res.erreur = (indvirg === -1) || (indparf === -1);
  if (res.erreur) {
    res.texte = "";
    return res;
  }
  var operande = ch.substring(3,indvirg);
  var exposant = ch.substring(indvirg+1,indparf);
  var texte = ch.substring(indparf+1);
  res.operande = operande;
  res.exposant = exposant;
  res.texte = texte;
  return res;
};
/**
 * Fonction appelée dans le cas ou pDebut est l'indice dans chaine d'une
* parenthèse ouvrante et renvoyant l'indice de la parenthèse
* fermante correspondante dans la chaîne.
*/
iep.indiceParentheseFermante = function(chaine,deb) {
  var p,ch;
  var somme = 1;
  p = deb + 1;
  while (p < chaine.length) {
    ch = chaine.charAt(p);
    if (ch === '(')
      somme++;
    else {
      if (ch === ')')
        somme--;
    }
    if (somme === 0) break;
    p++;
  }
  if (somme === 0) return p;
  else return - 1; // On renvoie -1 si pas trouvé
};
/**
 * Fonction renvoyant l'indice de la nb ième virgule dans la chaîne ch
 * à partir de l'indice deb en sauta,t le contenu des parenthèses
 * @param {type} ch
 * @param {type} deb
 * @param {type} nb
 * @returns {Integer}
 */
iep.indiceVirgule = function(chaine,deb,nb){
  var ch,indpf;
  var k = 0;
  var p = deb;
  while (p < chaine.length) {
    ch = chaine.charAt(p);
    if (ch === '(') {
      indpf = iep.indiceParentheseFermante(chaine,p);
      if (indpf === -1) return -1;
      else p = indpf;
    }
    else {
      if (ch === ",") k++;
      if (k === nb) return p;
    }
    p++;
  }
  return -1;
};
/**
 * Fonction renvoyant true si le premier caractère de la chaîne est un chiffre
 * @param {type} car
 * @returns {boolean}
 */
iep.chiffre = function(car) {
  var s = "0123456789";
  return (s.indexOf(car) !== -1);
};
// Fonctions reprises depuis le code source de InstrumentPoche en flash
iep.valeur_approchee = function(nombre, precision)  {
  var prec = Math.pow(10, precision);
  return Math.round(nombre/prec)*prec;
};
/**
 * Cette fonction permet de trouver les meilleures graduations a afficher pour un repere.
 * Elle retourne la plus petite de ces graduations, afin que des nombres "ronds" soient affichés.
 * Fonction reprise à partir du code de la version Flash et servant pour les
 * repères et quadrillages
 * @param {Float} mini
 * @param {Float} maxi
 * @param {Float} unite
 * @returns {Float}
 */
iep.determiner_graduations = function (mini, maxi, unite) {
  var debut_trace;
  //lorsque 0 est entre les bornes, on part de 0 et on cherche le début du tracé
  if (maxi * mini < 0) {
    debut_trace = -Math.floor(-mini / unite) * unite;
  }
  //en cas de puissance de 10 entre les bornes, on prend
  else if (Math.ceil(Math.log(Math.abs(mini)) / Math.LN10) !== Math.ceil(Math.log(Math.abs(maxi)) / Math.LN10)) {
    if (mini < 0) {
      debut_trace = -Math.pow(10, Math.floor(Math.log(Math.abs(mini)) / Math.LN10));
    } else {
      debut_trace = Math.pow(10, Math.ceil(Math.log(Math.abs(mini)) / Math.LN10));
    }
    debut_trace = debut_trace - Math.floor((debut_trace - mini) / unite) * unite;
  }
  // dans tous les autres cas, on fait ce qu'on peut
  else {
    debut_trace = iep.valeur_approchee(mini, Math.floor(Math.log(unite) / Math.LN10));
    if (debut_trace < mini) {
      debut_trace += unite;
    }
  }
  return debut_trace;
};
/**
 * Fonction reprise à partir du code de la version Flash et servant pour les
 * repères et quadrillages
 * @param {Float} nbr
 * @param {Float} borne1
 * @param {Float} borne2
 * @param {Float} borne_pix1
 * @param {Float} borne_pix2
 * @returns {Float}
 */
iep.mettre_en_pixels = function (nbr, borne1, borne2, borne_pix1, borne_pix2) {
  var coeff_dir = (borne_pix1 - borne_pix2) / (borne1 - borne2);
  var ordo_orig = borne_pix1 - coeff_dir * borne1;
  return coeff_dir * nbr + ordo_orig;
};
/**Fonctions éléminant dans le code XML contenu dans la chaîne ch les attributs
 * en doublon en ne gardant que le premier
 * @param {string} ch
 * @returns {string} / La chaîne avec les doublons d'attribut supprimés.
 */
iep.elimineDoublonsXML = function (ch) {
  var i,j,li,a,b;
  var res = "";
  var liste = ["objet","mouvement","vitesse","tempo","abscisse","ordonnee","id","couleur","pointille","echelle",
    "angle","cible","texte","ecrire","rotation","translation","epaisseur","opacite","masquer","montrer",
    "crayon","equerre","regle","compas","reglequerre","sens","hauteur","largeur","haut","gauche",
    "Xmin","Ymin","Xmax","yMax","Xgrad","Ygrad","image","police","taille",
    "ordonnee_bas_droite","abscisse_bas_droite","ordonnee_haut_gauche","abscisse_haut_gauche"];
  var s = ch.split(/<action/gi);
  for (i = 0; i < s.length; i++) {
    li = s[i];
    for (j = 0; j < liste.length; j++) {
      var r = new RegExp(" " + liste[j]+'[^\\w ]*= *"[\\w]*"',"i");
      a = li.search(r);
      if (a !== -1) {
        b = li.substring(a+1).search(r);
        if (b !== -1)
          li = li.replace(r,"");
      } 
    }
    res += (i === 0) ? li : "<action" + li;
  }
  return res;
};
/**
 * Fonction utilisée dans l'objet iep.ActionEcrireTexte et contenant les informations
 * sur un texte contenant des balises <b> ou <i> ou <u> ou <font> (ou un mélange)
 * @param {type} bold
 * @param {type} italic
 * @param {type} underline
 * @param {type} couleur
 * @param {type} fontface
 * @param {type} taille
 */
iep.infoBalise = function(bold,italic,underline,couleur,fontface,taille) {
  this.bold = bold;
  this.italic = italic;
  this.underline = underline;
  this.couleur = couleur;
  this.fontface = fontface;
  this.taille = taille;
};
/**
 * Fonction remplaçant dans la chaîne ch les accents écrits en code html par leur caractère utf8
 * @param {string} ch : La chaîne à traiter
 * @returns {string} : La chaîne avec les caractères remplacès
 */
iep.remplaceAccentsHtml = function(ch) {
  var t1 = ["&agrave;","&acirc;","&aelig;","&egrave;","&eacute;","&ecirc;","&icirc;","&ocirc;",
    "&ouml;","&oslash;","&Oslash;","&ugrave;","&ucirc;","&apos;"];
  var t2 = ["à","â","æ","è","é","ê","î","ô",
    "ö","ø","Ø","ù","û","'"];
  var i;
  for (i = 0;i < t1.length;i++) {
    ch = ch.replace(new RegExp(t1[i],"g"), t2[i]);
  }
  return ch;
};
/**
 * Fonction remplaçant dans la chaîne ch toutes les balises écrites avec des caractères spéciaux £
 * par des balises normales du type <...>
 * @param {string} ch : La chaîne à traiter contenant les balises spéciales
 * @returns {string} : La chaîne avec les balises normales
 */
iep.remplaceBalises = function(ch) {
  // Certains auteurs ont utilisé les codes £lt; et £gt;
  // Inutile car quand on parse avec getAttribute pour avoir le texte le balises sont déjà remplacées
  /*
  ch = ch.replace(/£lt;/gi,"£lt£");
  ch = ch.replace(/£gt;/gi,"£gt£");
  */
  // Par contre si elles ont déjà été remplacées et utilisaient des majuscules il faut mettre des minuscules
  ch = ch.replace(/<B>/g,"<b>");
  ch = ch.replace(/<\/B>/g,"</b>");
  ch = ch.replace(/<I>/g,"<i>");
  ch = ch.replace(/<\/I>/g,"</i>");
  ch = ch.replace(/<U>/g,"<u>");
  ch = ch.replace(/<\/U>/g,"</u>");
  
  ch = ch.replace(/£lt£i£gt£/gi,"<i>");
  ch = ch.replace(/£i£/gi,"<i>");
  ch = ch.replace(new RegExp("£lt£/i£gt£","gi"),"</i>");
  ch = ch.replace(new RegExp("£/i£","gi"),"</i>");
  ch = ch.replace(/£lt£b£gt£/gi,"<b>");
  ch = ch.replace(/£b£/gi,"<b>");
  ch = ch.replace(new RegExp("£lt£/b£gt£","gi"),"</b>");
  ch = ch.replace(new RegExp("£/b£","gi"),"</b>");
  ch = ch.replace(/£lt£u£gt£/gi,"<u>");
  ch = ch.replace(/£u£/gi,"<u>");
  ch = ch.replace(new RegExp("£lt£/u£gt£","gi"),"</u>");
  ch = ch.replace(new RegExp("£/u£","gi"),"</u>");
  ch = ch.replace(/£lt£br£gt£/gi,"<br>");
  ch = ch.replace(new RegExp("£lt£br/£gt£","gi"),"<br>");
  ch = ch.replace(/£br£/gi,"<br>");
  ch = ch.replace(new RegExp("£br/£","gi"),"<br>");
  ch = ch.replace(/£lt£font/gi,"<font");
  ch = ch.replace(new RegExp("£lt£/font£gt£","gi"),"</font>");  
  // Remplacement de balises exotiques utilisées par certains
  ch = ch.replace(/£lt£bold£gt£/gi,"<b>");
  ch = ch.replace(new RegExp("£lt£/bold£gt£","gi"),"</b>");
  // remplacement des £lt£ et £gt£ utiliséscomme caractères
  ch = ch.replace(/£lt£/gi,"<");
  ch = ch.replace(/£gt£/gi,">");
  ch = ch.replace(/£inferieurstrict£/gi,"<");
  ch = ch.replace(/£superieurstrict£/gi,">");
  // Remplacement des guillemets
  ch = ch.replace(/£guillemet£/gi,'"');
  return ch;
};
/**
 * Fonction renvoyant une chaîne où les caractères spéciaux de Flash de ch sont remplacés par
 * le caractère UTF8 correspondant
 * @param {string} ch : La chaîne à traiter
 */
iep.remplaceCarSpe = function(ch) {
  var i,s,cha,chb;
  s = ch;
  if (ch.indexOf("£") !== -1) {
    var a = ["alpha2","plus","moins","fois","divise","petitf","petitebarre","grandebarre","prime","seconde",
      "puceronde","grandC","euler","petitg","petith","Ironde","Lronde","lronde","grandN",
      "Pronde","grandQ","Rronde","grandR","grandZ","Eronde","Fronde","Nronde","flecheG",
      "flecheH","flecheD","flecheB","flecheDG","flecheGD","flecheHB","flecheBH","croissant","decroissant",
      "alaligneadroite","alaligneagauche","doubleflecheG","doubleflecheD","doubleflecheDG","doubleflecheGD",
      "flecheGbarre","flecheDbarre","flecheGcreuse","flecheHcreuse","flecheDcreuse","flecheBcreuse",
      "qqsoit","pourtout","quelquesoit","complement","differentielpartiel","ilexiste",
      "ilnexistepas","vide","nabla","appartienta","nappartientpasa","contient",
      "petitcontient","grandproduit","grandcoproduit","grandesomme","petitebarrefine",
      "moinsouplus","antislash","asterisque","racine","proportionnela",
      "infini","angle","anglespherique","divise","nedivisepas",
      "parallelea","nestpasparallelea","etlogique","oulogique","inter",
      "intersection","union","integrale","doubleintegrale","tripleintegrale",
      "integralecurviligne","integralesurfacique","integralevolumique","egaleasymptotiquea","environdroit",
      "environegala","environ","egalpardefinition","differentde","identiquea",
      "inferieura","superieura","inclusdans","contient","nestpasinclusdans",
      "necontientpas","sommedirecte","differencedirecte","produittensoriel","divisiondirecte",
      "produitdirect","top","perpendiculairea","antecedentde","imagede",
      "angledroitarc","point","pv"];
    var b = ["α","+","-","×","÷","f","–","—","'",'"',
      "•","ℂ","ℂ","ℊ","ℏ","ℑ","ℒ","ℓ","ℕ",
      "℘","ℚ","ℜ","ℝ","ℤ","ℰ","ℱ","ℵ","←",
      "↑","→","↓","↔","↔","↕","↕","↗","↘",
      "↳","↵","⇐","⇒","⇔","⇔",
      "⇤","⇥","⇦","⇧","⇨","⇩",
      "∀","∀","∀","∁","∂","∃",
      "∄","∅","∇","∈","∉","∋",
      "∍","∏","∐","∑","−",
      "∓","∖","∗","√","∝",
      "∞","∡","∢","∣","∤",
      "//","∦","∧","∨","∩",
      "∩","∪","∫","∬","∭",
      "∮","∯","∰","≃","≃",
      "≅","≈","≝","≠","≡",
      "≤","≥","⊂","⊃","⊄",
      "⊅","⊕","⊖","⊗","⊘",
      "⊙","⊤","⊥","⊶","⊷",
      "⊾","⋅",";"];
    for (i=0; i<a.length; i++) {
      cha = "£"+a[i]+"£";
      chb = b[i];
      if (s.indexOf(cha) !== -1) s = s.replace(new RegExp(cha,"gi"),chb); 
    }
  }
  return s;
};
/**
 * Fonction récursive appelée lorsque l'affichage de texte n'utilise pas le LaTeX
 * Elle traite les balises u, i, b et font pour ajouter à txt (qui est élément
 * graphique svg text des tspans correspondants.
 * @param {String} : La chaîne contenant le texte avec balises éventuelles
 * @param {iep.infoBalise} infoBalise : un objet qui contient les caractéristiques du texte, à savoir
 * bold ou non, italique ou non, souligné ou non, couleur et fonte utilisée
 * @param {svg.text} txt : L'élément text de svg auquel seront rajoutés les tspan créés
 * @param {Boolean} debutLigne : true si l'affichage du prochain tspan doit se faire en début de ligne
 * @param {Integer} y : le paramètre y du prochain tspan à créer
 */
iep.traiteBalise = function(ch,infoBalise,txt,debutLigne,y) {
  var stylespan,tspan,indfb,ind1,ind2,fonte,couleur,taille,inddeb;
  if (ch === "") return;
  var info;
  var ind = ch.search(/<b>|<i>|<u>|<font/);
  tspan = document.createElementNS(iep.svgsn,"tspan");
  tspan.setAttribute("pointer-events", "none");
  if ((ind === -1) || (ind > 0)) { // Il y a du texte
    stylespan = "";
    // if (infoBalise.couleur !== "") stylespan += "fill:" + infoBalise.couleur+";";
    if (infoBalise.couleur) stylespan += "fill:" + infoBalise.couleur+";";
    if (infoBalise.bold) stylespan += "font-weight:bold;";
    if (infoBalise.italic) stylespan += "font-style:italic;";
    if (infoBalise.underline) stylespan += "text-decoration:underline;";
    if (infoBalise.fontface !== "") stylespan += "fontfamily:"+infoBalise.fontface+";";
    stylespan += "font-size:" + infoBalise.taille + "px;";
    tspan.setAttribute("style", stylespan);
    if(debutLigne) {
      tspan.setAttribute("x",0);
      debutLigne = false;
    }
    tspan.setAttribute("y",y);
    var ch2 = (ind === -1) ? ch : ch.substring(0,ind);
    tspan.appendChild(document.createTextNode(ch2));
    txt.appendChild(tspan);
    if (ind > 0) iep.traiteBalise(ch.substring(ind),infoBalise,txt,debutLigne,y);
    // else ch = "";
  }
  else {
    if (ch.indexOf("<b>") === 0) { // Balise bold
      info = new iep.infoBalise(true,infoBalise.italic,infoBalise.underline,infoBalise.couleur,infoBalise.fontface);
      indfb = iep.indiceFinBalise(ch);
      if (indfb === -1) iep.traiteBalise(ch.substring(3),info,txt,debutLigne,y);
      else iep.traiteBalise(ch.substring(3,indfb-4),info,txt,debutLigne,y);
    }
    else {
      if (ch.indexOf("<i>") === 0) { // Balise italique
        info = new iep.infoBalise(infoBalise.bold,true,infoBalise.underline,infoBalise.couleur,infoBalise.fontface);
        indfb = iep.indiceFinBalise(ch);
        if (indfb === -1) iep.traiteBalise(ch.substring(3),info,txt,debutLigne,y);
        else iep.traiteBalise(ch.substring(3,indfb-4),info,txt,debutLigne,y);
      }
      else {
        if (ch.indexOf("<u>") === 0) { // Balise souigné
          info = new iep.infoBalise(infoBalise.bold,infoBalise.italic,true,infoBalise.couleur,infoBalise.fontface);
          indfb = iep.indiceFinBalise(ch);
          if (indfb === -1) iep.traiteBalise(ch.substring(3),info,txt,debutLigne,y);    
          else iep.traiteBalise(ch.substring(3,indfb-4),info,txt,debutLigne,y);        
        }
        else {
          if (ch.indexOf("<font") === 0) {
            indfb = iep.indiceFinBalise(ch);
            if (indfb === -1)indfb = ch.length;
            ind1 = ch.indexOf('face="');
            if (ind1 !== -1) {
              ind2 = ch.indexOf('"',ind1+6);
              if (ind2 !== -1)
                fonte = ch.substring(ind1+6,ind2);
              else fonte = "";
            }
            else fonte = "";
            couleur = "";
            ind1 = ch.indexOf('couleur="');
            if (ind1 !== -1) {
              ind2 = ch.indexOf('"',ind1+9);
              if (ind2 !== -1) {
                couleur = iep.couleur(ch.substring(ind1+9,ind2));
              }
            }
            else {
              ind1 = ch.indexOf('color="');
              if (ind1 !== -1) {
                ind2 = ch.indexOf('"',ind1+7);
                if (ind2 !== -1) {
                  couleur = iep.couleur(ch.substring(ind1+7,ind2));
                }
              }      
            }
            ind1 = ch.indexOf('size="');
            if (ind1 !== -1) {
              ind2 = ch.indexOf('"',ind1+6);
              if (ind2 !== -1)
                taille = ch.substring(ind1+6,ind2);
                else taille = infoBalise.taille;
            }

            info = new iep.infoBalise(infoBalise.bold,infoBalise.italic,infoBalise.underline,couleur,fonte,taille);
            inddeb = ch.indexOf(">"); // Recherche du > de la balise <font>
            if (ch.search(new RegExp("</font>","i")) !== -1)
              iep.traiteBalise(ch.substring(inddeb+1,indfb-7),info,txt,debutLigne,y);
            else iep.traiteBalise(ch.substring(inddeb+1),info,txt,debutLigne,y);
          }      
        }
      }
    }
  }
};
/**
 * Fonction renvoyant true si la chaîne ch contient des codes spéciaux de maths en flash
 * sauf les £e() et £i() simples.
 * Si les arguments de £e() ou £i() contiennent des appels à des balises ou d'autres
 * appels à £e() et £i() renvoie true car alors il faut utiliser le LaTeX pour représenter la formule.
 * Si expind est true on cherche aussi les puissances et indices
 * @param {string} ch
 * @returns {Boolean}
 */
iep.necessiteLatex = function(ch) {
  var i,ind,indparf,s,ch1;
  var codes ="acdfgnprsuv"; // Les codes e (exposant) et i (idice) ne nécessitent pas l'utilisation de MathJax
  for (i = 0; i < codes.length; i++) {
    if (ch.indexOf("£"+codes.charAt(i)+"(") !== -1) return true;
  }
  // Si la formule contient des puissances ou des indices utilisant elle-mêmes des balises
  // puissances, indices ou font il faut passer par le LaTeX donc on renvoie true
  ch1 = ch;
  while ((ind = ch1.search(/£e\(|£i\(/g)) !== -1) {
    indparf = iep.indiceParentheseFermante(ch1, ind + 3);
    if (indparf === -1) indparf = ch1.length;
    s = ch1.substring(ind, indparf);
    if (s.search(/£e\(|£i\(\£lt\£font/g) !== -1) return true;
    if (indparf === ch1.length) return false;
    else ch1 = s.substring(indparf + 1);
  };
  ch1 = ch;
  // Si la formule contient des balise font contenant des indices ou exposants
  // il faut aussi passer par le LaTeX donc on renvoie true
  while ((ind = ch1.search(/\£lt\£font/g)) !== -1) {
    indparf = iep.indiceFinBalise(ch1.substring(ind));
    if (indparf === -1) indparf = ch1.length;
    else indparf += ind;
    s = ch1.substring(ind, indparf);
    if (s.search(/£e\(|£i\(/g) !== -1) return true;
    if (indparf === ch1.length) return false;
    else ch1 = s.substring(indparf + 1);
  };
  
  return false;
};

/**
 * Fonction récursive renvoyant une chaîne LaTeX représentant la chaîne contenue dans s
 * @param {string} s : la chaîne à traiter (qui représente une ligne dans le cas de plusieurs lignes)
 * @param {Boolena} btexte : Si true, le contenut renvoyé est encadré par une balise \text
 */
iep.getMaths = function(s,btexte) {
  var ind,indpf,indv,ind1,ind2,inddeb,ch2,couleur,ret,indv1,indv2,indv3,car;
  var ch = s;
  if (ch === "") return "";
  // Traitement des balises Font
  // On ne traite que les changements de couleur
   while ((ind = ch.indexOf("<font")) !== -1) {
    couleur = null;
    ch2 = ch.substring(ind);
    indpf = iep.indiceFinBalise(ch2);
    ind1 = ch2.indexOf('couleur="');
    if (ind1 !== -1) {
      ind2 = ch2.indexOf('"',ind1+9);
      if (ind2 !== -1) couleur = iep.couleur(ch2.substring(ind1+9, ind2));
    }
    else {
      ind1 = ch2.indexOf('color="');
      if (ind1 !== -1) {
        ind2 = ch2.indexOf('"',ind1+7);
        if (ind2 !== -1) couleur = iep.couleur(ch2.substring(ind1+7,ind2));
      }      
    }
    if (couleur != null) { // On ne traite que les changements de couleur
      inddeb = ind + ch2.indexOf(">") + 1; // Pointe sur le début concerné par la balise <font
      if (indpf === -1)
        return iep.getMaths(ch.substring(0,ind) + "\\textcolor{" + couleur + "}{" + 
            ch.substring(inddeb) + "}",btexte);
        else {
          indpf += ind;
          return iep.getMaths(ch.substring(0,ind) + "\\textcolor{" + couleur + "}{" + 
            ch.substring(inddeb,indpf-7) + "}" + ch.substring(indpf),btexte);
        }
    }
    else {
      inddeb = ind + ch2.indexOf(">") + 1;
      if (indpf === -1) return iep.getMaths(ch.substring(0,ind) + ch.substring(inddeb),btexte);
      else {
        indpf += ind;
        return iep.getMaths(ch.substring(0,ind) + ch.substring(inddeb,indpf-7) +
          ch.substring(indpf),btexte);
      }
    };

  }
  // Traitement des caractères par code hexadécimal
  while ((ind = ch.indexOf("£u(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(ch.substring(0,ind),btexte) + "\\unicode{x" + ch.substring(ind+3,indpf) 
      + "}"+ iep.getMaths(ch.substring(indpf+1),btexte);
  }
  // Traitement des fractions
  while ((ind = ch.indexOf("£f(")) !== -1) {
    indv = iep.indiceVirgule(ch,ind+3,1); // Recherche de la première virgule
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if ((indv === -1) || (indpf === -1) || (indv > indpf)) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\frac{" +
       iep.getMaths(ch.substring(ind+3,indv),false) + "}{" +  
       iep.getMaths(ch.substring(indv+1,indpf),false) + "}" + iep.getMaths(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des racines carrées
  while ((ind = ch.indexOf("£r(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\sqrt{" +
      iep.getMaths(ch.substring(ind+3,indpf),false)
      + "}" + iep.getMaths(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des codes d'angle
  while ((ind = ch.indexOf("£a(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) +
      "\\widehat{" + iep.getMaths(ch.substring(ind+3,indpf),true)+ "}" 
      + iep.getMaths(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des intégrales
   while ((ind = ch.indexOf("£g(")) !== -1) {
    indv1 = iep.indiceVirgule(ch,ind+3,1); // Recherche de la première virgule
    indv2 = iep.indiceVirgule(ch,ind+3,2); // Recherche de la première virgule
    indv3 = iep.indiceVirgule(ch,ind+3,3); // Recherche de la première virgule
    indpf = iep.indiceParentheseFermante(ch,ind+2);     
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\int_{" +
      iep.getMaths(ch.substring(indv2+1,indv3),false) + "}^{" +
      iep.getMaths(ch.substring(indv3+1,indpf),false) +
      "}" + iep.getMaths(ch.substring(ind+3,indv1),false) +
      " d" + iep.getMaths(ch.substring(indv1+1,indv2),false) +
      iep.getMaths(ch.substring(indpf+1),btexte),btexte);
   }
  // Traitement des crochets
  while ((ind = ch.indexOf("£c(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(ch.substring(0,ind),btexte) + "\\left[" + iep.getMaths(ch.substring(ind+3,indpf),btexte)
      + "\\right]" + iep.getMaths(ch.substring(indpf+1),btexte);
  }
  // Traitement des parenthèses
  while ((ind = ch.indexOf("£p(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\left(" + iep.getMaths(ch.substring(ind+3,indpf),btexte)
      + "\\right)" + iep.getMaths(ch.substring(indpf+1),btexte));
  }
  // Traitement des valeurs absolues
  while ((ind = ch.indexOf("£d(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\left|" + iep.getMaths(ch.substring(ind+3,indpf),false)
      + "\\right|" + iep.getMaths(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des normes
  while ((ind = ch.indexOf("£n(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\left\\|" + iep.getMaths(ch.substring(ind+3,indpf),false)
      + "\\right\\|" + iep.getMaths(ch.substring(indpf+1),btexte));
  }
  // Traitement des indices
  while ((ind = ch.indexOf("£i(")) !== -1) {
    indv = iep.indiceVirgule(ch,ind+3,1); // Recherche de la première virgule
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if ((indv === -1) || (indpf === -1) || (indv > indpf)) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "{" + iep.getMaths(ch.substring(ind+3,indv),false) + "}_{" +
       iep.getMaths(ch.substring(indv+1,indpf),false) + "}" + iep.getMaths(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des exposants
  while ((ind = ch.indexOf("£e(")) !== -1) {
    indv = iep.indiceVirgule(ch,ind+3,1); // Recherche de la première virgule
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if ((indv === -1) || (indpf === -1) || (indv > indpf)) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "{" + iep.getMaths(ch.substring(ind+3,indv),false) + "}^{" +
       iep.getMaths(ch.substring(indv+1,indpf),false) + "}" + iep.getMaths(ch.substring(indpf+1),btexte));
  }
  // Traitement des vecteurs
  while ((ind = ch.indexOf("£v(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMaths(iep.getMaths(ch.substring(0,ind),btexte) + "\\overrightarrow{" + iep.getMaths(ch.substring(ind+3,indpf),false)
      + "}" + iep.getMaths(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des balises <u>
  while ((ind = ch.indexOf("<u>")) !== -1) {
    indpf = iep.indiceFinBalise(ch.substring(ind));
    if (indpf === -1) return ch;
    else indpf += ind;
    return iep.getMaths(ch.substring(0,ind),btexte) + "\\underline{" + iep.getMaths(ch.substring(ind+3,indpf-4),true) 
      + "}"+ iep.getMaths(ch.substring(indpf),btexte);
  }
  // Traitement des balises <i>
  while ((ind = ch.indexOf("<i>")) !== -1) {
    indpf = iep.indiceFinBalise(ch.substring(ind));
    if (indpf === -1) return iep.getMaths(ch.substring(0,ind),btexte) +
            "\\textit{" + iep.getMaths(ch.substring(ind+3),false) + "}";
    else {
      indpf += ind;
      return iep.getMaths(ch.substring(0,ind),btexte) + "\\textit{" +
        iep.getMaths(ch.substring(ind+3,indpf-4),false) + "}" +
        iep.getMaths(ch.substring(indpf),btexte);
    }
  }
    // Traitement des balises <b>
  while ((ind = ch.indexOf("<b>")) !== -1) {
    indpf = iep.indiceFinBalise(ch.substring(ind));
    if (indpf === -1) return iep.getMaths(ch.substring(0,ind),btexte) +
            "\\textbf{" + iep.getMaths(ch.substring(ind+3),false) + "}";
    else {
      indpf += ind;
      return iep.getMaths(ch.substring(0,ind),btexte) + "\\textbf{" +
        iep.getMaths(ch.substring(ind+3,indpf-4),false) + "}" +
        iep.getMaths(ch.substring(indpf),btexte);
    }
  }
  if (btexte) {
    // Même si on et en mode texte, on peut avoir au début de ch une balise \textcolor incomplète
    if (iep.contientBaliseLaTeX(ch)) return ch;
    else {
      // Il faut sauter les balises \\textcolor en tenant compte que certaines ne sont pas fermées.
      ind = ch.indexOf("\\textcolor{");
      if (ind !== -1) {
        ret = "";
        ch2 = ch;
        while ((ind = ch2.indexOf("\\textcolor{")) != -1) {
          ind1 = ch2.indexOf("}{");
          indpf = iep.accoladeFermante(ch2,ind1+1);
          if (indpf === -1) {
            if (ind == 0) {
              if (ind1+2 != ch2.length) ret += ch2.substring(0,ind1+2) + "\\text{" + ch2.substring(ind1+2) + "}";
              else ret += ch2;
              ch2 = "";
            }
            else {
              if (ind1+2 != ch2.length) ret += "\\text{" + ch2.substring(0, ind) + "}" + ch2.substring(ind,ind1+2) +
                    "\\text{" + ch2.substring(ind1+2) + "}";
                  else ret += "\\text{" + ch2.substring(0, ind) + "}" + ch2.substring(ind,ind1+2);
              ch2 = "";
            }
          }
          else {
            if (ind == 0) {
              ret += ch2.substring(0,indpf+1);
              if (indpf < ch2.length-1) ch2 = ch2.substring(indpf+1);
              else ch2 = "";
            }
            else {
              ret +=  "\\text{" + ch2.substring(0,ind) + "}" + ch2.substring(ind,ind1+2) +
                "\\text{" + ch2.substring(ind1+2,indpf+1) + "}";
              if (indpf < ch2.length-1) ch2 = ch2.substring(indpf+1);
              else ch2 = "";
            }
          }
        }
        if (ch2 === "") return ret;
        else return ret + "\\text{" + ch2 + "}";
      }
      else {
        // Si la chaîne commence par une parenthèse fermante, une accolade fermante ou une virgule
        //  il s'agit d'une formule en cours et pas de mode texte
        car = ch.charAt(0);
        if ((car === ")") || (car === ",") || (car === "}")) return ch;
        else return "\\text{" + ch + "}";
      }
    }
  }
  else return ch;
};

/**
 * Fonction récursive renvoyant une chaîne LaTeX représentant la chaîne contenue dans s
 * Utilisée pour les noms devant utiliser le LaTeX pour être affichés
 * @param {string} s : la chaîne à traiter (qui représente une ligne dans le cas de plusieurs lignes)
 * @param {Boolena} btexte : Si true, le contenut renvoyé est encadré par une balise \text
 */
iep.getMathsForName = function(s,btexte) {
  var ind,indpf,indv,ind1,ind2,inddeb,ch2,couleur,ret,indv1,indv2,indv3,car;
  var ch = s;
  if (ch === "") return "";
  // Traitement des balises Font
  // On ne traite que les changements de couleur
   while ((ind = ch.indexOf("<font")) !== -1) {
    couleur = null;
    ch2 = ch.substring(ind);
    indpf = iep.indiceFinBalise(ch2);
    ind1 = ch2.indexOf('couleur="');
    if (ind1 !== -1) {
      ind2 = ch2.indexOf('"',ind1+9);
      if (ind2 !== -1) couleur = iep.couleur(ch2.substring(ind1+9, ind2));
    }
    else {
      ind1 = ch2.indexOf('color="');
      if (ind1 !== -1) {
        ind2 = ch2.indexOf('"',ind1+7);
        if (ind2 !== -1) couleur = iep.couleur(ch2.substring(ind1+7,ind2));
      }      
    }
    if (couleur !== null) { // On ne traite que les changements de couleur
      inddeb = ind + ch2.indexOf(">") + 1; // Pointe sur le début concerné par la balise <font
      if (indpf === -1)
        return iep.getMathsForName(ch.substring(0,ind) + "\\textcolor{" + couleur + "}{" + 
            ch.substring(inddeb) + "}",btexte);
        else {
          indpf += ind;
          return iep.getMathsForName(ch.substring(0,ind) + "\\textcolor{" + couleur + "}{" + 
            ch.substring(inddeb,indpf-7) + "}" + ch.substring(indpf),btexte);
        }
    }
    else {
      inddeb = ind + ch2.indexOf(">") + 1;
      if (indpf === -1) return iep.getMathsForName(ch.substring(0,ind) + ch.substring(inddeb),btexte);
      else {
        indpf += ind;
        return iep.getMathsForName(ch.substring(0,ind) + ch.substring(inddeb,indpf-7) +
          ch.substring(indpf),btexte);
      }
    };

  }
  // Traitement des caractères par code hexadécimal
  while ((ind = ch.indexOf("£u(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMathsForName(ch.substring(0,ind),btexte) + "\\unicode{x" + ch.substring(ind+3,indpf) 
      + "}"+ iep.getMathsForName(ch.substring(indpf+1),btexte);
  }
  // Traitement des racines carrées
  while ((ind = ch.indexOf("£r(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMathsForName(iep.getMathsForName(ch.substring(0,ind),btexte) + "\\sqrt{" +
      iep.getMathsForName(ch.substring(ind+3,indpf),false)
      + "}" + iep.getMathsForName(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des valeurs absolues
  while ((ind = ch.indexOf("£d(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMathsForName(iep.getMathsForName(ch.substring(0,ind),btexte) + "\\left|" + iep.getMathsForName(ch.substring(ind+3,indpf),false)
      + "\\right|" + iep.getMathsForName(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des normes
  while ((ind = ch.indexOf("£n(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMathsForName(iep.getMathsForName(ch.substring(0,ind),btexte) + "\\left\\|" + iep.getMathsForName(ch.substring(ind+3,indpf),false)
      + "\\right\\|" + iep.getMathsForName(ch.substring(indpf+1),btexte));
  }
  // Traitement des indices
  while ((ind = ch.indexOf("£i(")) !== -1) {
    indv = iep.indiceVirgule(ch,ind+3,1); // Recherche de la première virgule
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if ((indv === -1) || (indpf === -1) || (indv > indpf)) return ch;
    // rrue car il s'agit d'un nom : iep.getMathsForName(ch.substring(ind+3,indv),true)
    return iep.getMathsForName(iep.getMathsForName(ch.substring(0,ind),btexte) + "{" + iep.getMathsForName(ch.substring(ind+3,indv),true) + "}_{" +
       iep.getMathsForName(ch.substring(indv+1,indpf),false) + "}" + iep.getMathsForName(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des exposants
  while ((ind = ch.indexOf("£e(")) !== -1) {
    indv = iep.indiceVirgule(ch,ind+3,1); // Recherche de la première virgule
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if ((indv === -1) || (indpf === -1) || (indv > indpf)) return ch;
    // true car il s'agit d'un affichage de nom : iep.getMathsForName(ch.substring(ind+3,indv),true)
    return iep.getMathsForName(iep.getMathsForName(ch.substring(0,ind),btexte) + "{" + iep.getMathsForName(ch.substring(ind+3,indv),true) + "}^{" +
       iep.getMathsForName(ch.substring(indv+1,indpf),false) + "}" + iep.getMathsForName(ch.substring(indpf+1),btexte));
  }
  // Traitement des vecteurs
  while ((ind = ch.indexOf("£v(")) !== -1) {
    indpf = iep.indiceParentheseFermante(ch,ind+2);
    if (indpf === -1) return ch;
    return iep.getMathsForName(iep.getMathsForName(ch.substring(0,ind),btexte) + "\\overrightarrow{" + iep.getMathsForName(ch.substring(ind+3,indpf),false)
      + "}" + iep.getMathsForName(ch.substring(indpf+1),btexte),btexte);
  }
  // Traitement des balises <u>
  while ((ind = ch.indexOf("<u>")) !== -1) {
    indpf = iep.indiceFinBalise(ch.substring(ind));
    if (indpf === -1) return ch;
    else indpf += ind;
    return iep.getMathsForName(ch.substring(0,ind),btexte) + "\\underline{" + iep.getMathsForName(ch.substring(ind+3,indpf-4),true) 
      + "}"+ iep.getMathsForName(ch.substring(indpf),btexte);
  }
  // Traitement des balises <i>
  while ((ind = ch.indexOf("<i>")) !== -1) {
    indpf = iep.indiceFinBalise(ch.substring(ind));
    if (indpf === -1) return iep.getMathsForName(ch.substring(0,ind),btexte) +
            "\\textit{" + iep.getMathsForName(ch.substring(ind+3),false) + "}";
    else {
      indpf += ind;
      return iep.getMathsForName(ch.substring(0,ind),btexte) + "\\textit{" +
        iep.getMathsForName(ch.substring(ind+3,indpf-4),false) + "}" +
        iep.getMathsForName(ch.substring(indpf),btexte);
    }
  }
    // Traitement des balises <b>
  while ((ind = ch.indexOf("<b>")) !== -1) {
    indpf = iep.indiceFinBalise(ch.substring(ind));
    if (indpf === -1) return iep.getMathsForName(ch.substring(0,ind),btexte) +
            "\\textbf{" + iep.getMathsForName(ch.substring(ind+3),false) + "}";
    else {
      indpf += ind;
      return iep.getMathsForName(ch.substring(0,ind),btexte) + "\\textbf{" +
        iep.getMathsForName(ch.substring(ind+3,indpf-4),false) + "}" +
        iep.getMathsForName(ch.substring(indpf),btexte);
    }
  }
  if (btexte) {
    // Même si on et en mode texte, on peut avoir au début de ch une balise \textcolor incomplète
    if (iep.contientBaliseLaTeX(ch)) return ch;
    else {
      // Il faut sauter les balises \\textcolor en tenant compte que certaines ne sont pas fermées.
      ind = ch.indexOf("\\textcolor{");
      if (ind !== -1) {
        ret = "";
        ch2 = ch;
        while ((ind = ch2.indexOf("\\textcolor{")) != -1) {
          ind1 = ch2.indexOf("}{");
          indpf = iep.accoladeFermante(ch2,ind1+1);
          if (indpf === -1) {
            if (ind == 0) {
              if (ind1+2 != ch2.length) ret += ch2.substring(0,ind1+2) + "\\text{" + ch2.substring(ind1+2) + "}";
              else ret += ch2;
              ch2 = "";
            }
            else {
              if (ind1+2 != ch2.length) ret += "\\text{" + ch2.substring(0, ind) + "}" + ch2.substring(ind,ind1+2) +
                    "\\text{" + ch2.substring(ind1+2) + "}";
                  else ret += "\\text{" + ch2.substring(0, ind) + "}" + ch2.substring(ind,ind1+2);
              ch2 = "";
            }
          }
          else {
            if (ind == 0) {
              ret += ch2.substring(0,indpf+1);
              if (indpf < ch2.length-1) ch2 = ch2.substring(indpf+1);
              else ch2 = "";
            }
            else {
              ret +=  "\\text{" + ch2.substring(0,ind) + "}" + ch2.substring(ind,ind1+2) +
                "\\text{" + ch2.substring(ind1+2,indpf+1) + "}";
              if (indpf < ch2.length-1) ch2 = ch2.substring(indpf+1);
              else ch2 = "";
            }
          }
        }
        if (ch2 === "") return ret;
        else return ret + "\\text{" + ch2 + "}";
      }
      else {
        // Si la chaîne commence par une parenthèse fermante, une accolade fermante ou une virgule
        //  il s'agit d'une formule en cours et pas de mode texte
        car = ch.charAt(0);
        if ((car === ")") || (car === ",") || (car === "}")) return ch;
        else return "\\text{" + ch + "}";
      }
    }
  }
  else return ch;
};
/*
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Fonction créant un objet permettant de gérer plusieurs figures instrumenPoche
 * dans des svg différents
 * @constructor
 */
iep.iepApp = function () {
    this.docs = [];
    var t = this;
    window.addEventListener("unload",function(){t.closeAllXMLWindows()});
};
/**
 * Fonction ajoutant un document à un iepApp
 * @param {string|Element} svg : Le svg (l'élément du dom ou son id) dans lequel vont se faire les affichages.
 * @param {string} chDoc : La chaîne XML décrivant le document.
 * @param {boolean} autoStart : Si true l'animation démarre dès la fin du chargement
 */
iep.iepApp.prototype.addDoc = function(svg, chDoc, autoStart) {
  var doc;
  if (typeof svg === "string") svg = document.getElementById(svg);
  if (!svg) return new Error("svg manquant");
  if (svg.localName != "svg") return new Error("svg incorrect");
  doc = new iep.iepDoc(svg, chDoc, (arguments.length <= 2) ? true : autoStart);
  this.docs.push(doc);
  svg.iepDoc = doc; // Pour que les boutons accèdent au document.
  return doc;
};
/**
 * Fonction utilisée lors de la fermeture d'une fenêtre contenant des figures
 * InstrumenPoche dans des SVG.
 * Elle provoque la fermeture des éventuelles fenêtres popup contenant le code
 * XML d'une figure (obtenues en cliquant sur une icône XML)
 */
iep.iepApp.prototype.closeAllXMLWindows = function() {
  var i;
  for (i = 0; i < this.docs.length; i++) {
    var doc = this.docs[i];
    if (doc.windowxml != null) doc.windowxml.close();
  }
}
/*
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
var iep;
if (!iep) iep = {};
/**
 * Document représentant une figure affichée dans le SVG d'id idDoc
 * @constructor
 * @param {Element} svg  Le SVG contenant la figure
 * @param {string} chdoc La chaîne contenant le code XML décrivant la figure.
 * param {boolean} autoStart : Si true l'animation démarre automtiquement
 */
iep.iepDoc = function (svg, chdoc, autoStart) {
  /**
   * L'élément html du svg
   * @type Element
   */
  this.svg = svg;
  this.autoStart = autoStart;
  /**
   * Pointera sur la fenêtre contenant le code xml
   * @type Window
   */
  this.windowxml = null;
  var ch = chdoc;
  // On ajoute l'en-tête xml si elle a été ommise.
  if(ch.search(/<?xml/gi) == -1) ch = '<?xml version="1.0" encoding="UTF-8"?>' + ch;
  // On élimine les <action_ suivis de chiffres
  ch = ch.replace(/<action[_]\d*/gi,"<action");
  // On élimine les remarques html
  ch = ch.replace(/<!--.*-->/g,"");
  // On corrige les oublis d'espaces et les -- dans les attributs
  ch = ch.replace(/(="[^"]*")([^ ])/g, "$1 $2").replace(/--/g,"");
  // On corrige les éventuels << ou >>
  ch = ch.replace(/<</g,"<").replace(/>>/g,">");
  // Certains auteurs ont mis des <actiontempo= sans espace ou autre
  ch = ch.replace(/<action(\w)/gi,"<action $1");
  // Les commentaires peuvent un attribut texte ce qui pose des problèmes dans elimineDoublonsXML
  // On les remplace par un attribut texteCommentaire
  ch = ch.replace(/<commentaire\s*texte\s*=/gi,"<commentaire texteCommentaire =");
  // On corrige les attributs en doublon
  ch = iep.elimineDoublonsXML(ch);
  // On élimine l'en-tête qui contient parfois des caractères bizarres
  /**
   * La string xml du doc
   * @type string
   */
  this.codexml = ch; // Mémorisé pour pouvoir être affiche quand on clique sur le bouton XML

  var ind1 = ch.search(/<\s*INSTRUMENPOCHE/gi);
  var ind2 = ch.indexOf(">",ind1);
  var ind3 = ch.search(new RegExp("<\s*/INSTRUMENPOCHE","gi")); // Pas de > dans la recherche car certains auteurs se sont trompés sur la balise de fin
  this.chdoc = "<INSTRUMENPOCHE>" + ch.substring(ind2+1,ind3) + "</INSTRUMENPOCHE>";


  this.actions = []; // Un tableau qui contiendra toutes les actions de la figure.
  // Une liste des éléments graphiques de type objet graphique traités par la figure.
  // Chaque élément sera identifié par un n° d'id et par son type
  // Lorsqu'un élément de même id et de même nature qu'un élément déjà existant est créé, lors de l'exécution
  // de l'action de création, son élément graphique remplacera celui de l'élement déjà existant
  // dans le DOM du svg de la figure
  this.elements = [];
  this.compasRetourne = false;
  // On crée les instruments dès le début car àa chaque fois qu'un instruent est translaté, tourné, zoomé,
  // certains objet créés ont besoin de connaître l'état des instruments pour leur création
  this.compasLeve = null;
  this.compas = new iep.compas(this);
  this.equerre = new iep.equerre(this);
  this.rapporteur = new iep.rapporteur(this);
  this.regle = new iep.regle(this);
  this.requerre = new iep.requerre(this);
  this.crayon = new iep.crayon(this);
  // On parse le xml
  this.xmldoc = iep.parseXMLDoc(this.chdoc);
  this.tabact = this.xmldoc.getElementsByTagName("action");
  this.started = false;
  this.animationEnCours = true; // Sera mis à false pour le pas à pas
  // indiceActionEnCours est l'indice de la première future action qui devra être exécutée
  this.indiceActionEnCours = 0;
  this.creeActions();
  var t = this;
  // Les événements souris pour pouvoir gérer les déplacements de la barre d'icônes
  svg.addEventListener("mousemove",function(ev) {t.onmousemove(ev,ev.clientX,ev.clientY)},false);
  svg.addEventListener("mouseup",function() {t.onmouseup()},false);
  svg.addEventListener("mouseleave",function() {t.onmouseleave()},false);
  svg.addEventListener("touchmove",function(ev) {t.onmousemove(ev,ev.touches[0].pageX,ev.touches[0].pageY)},false);
  svg.addEventListener("touchend",function() {t.onmouseup()},false);
  svg.addEventListener("touchcancel",function() {t.onmouseup()},false);
  svg.addEventListener("touchleave",function() {t.onmouseleave()},false);
  iep.log("iepDoc instancié : ", this);
};

/**
 * Fontion apppelée à la fin du chargement de certains onjets dont le chargement est asynchrone : les images
 * et les textes dont l'écriture nécessite l'utilisation du LaTeX.
 * Renvoie true si tous les objets de la figure sont bien chargés.
 * Au bout de 10 secondes écoulées affiche un message d'erreur
 * @returns {boolean}.
 */
iep.iepDoc.prototype.waitForReadyState = function() {
  if (this.started) return;
  var ready = this.verifyReadyState();
  if (ready) this.start();
  else {
     var t = this;
      setTimeout(function() {
        // t.waitForReadyState();
        if (!t.started) {
          alert("Le chargement de la figure IEP a échoué");
          t.started = true;
        }
      },10000); // On laisse maxi 10 secondes à la figure pour se charger
  }
};

/**
 * Fonction renvoyant true seulement si tous les objets de la figure sont bien chargés
 * @returns {boolean}
 */
iep.iepDoc.prototype.verifyReadyState = function() {
  var ready = true;
  var i;
  for (i = 0; i < this.actions.length;i++) {
    ready = ready && this.actions[i].isReady;
  }
  return ready;
};

/**
 * Fonction créant tous les éléments graphiques des aactions de création de la figure.
 * Seules les actions de créations d'objets ont une fonction creegElement faisant quelquechose
 */
iep.iepDoc.prototype.creeElementsGraphiques = function() {
  var i;
  for (i=0;i<this.actions.length;i++) {
    this.actions[i].creegElement();
  }
};

/**
 * Fonction réinitilisant la position initiale de tous les instruments et les cachant.
 */
iep.iepDoc.prototype.initialiseOutils = function() {
  this.compas.initialisePosition();
  this.equerre.initialisePosition();
  this.rapporteur.initialisePosition();
  this.regle.initialisePosition();
  this.requerre.initialisePosition();
  this.crayon.initialisePosition();
  this.compas.montre(false);
  this.compas.leve = false;
  this.equerre.montre(false);
  this.rapporteur.montre(false);
  this.regle.montre(false);
  this.requerre.montre(false);
  this.crayon.montre(false);
};

/**
 * Fonction retirnat du DOM du SVG les éléments graphiques des outils et les recréant
 * de façon à ce que les outils soient créées dans le DOM après les éléments graphiques
 * des objets de la figure.
 */
iep.iepDoc.prototype.recreeOutils = function() {
  this.compas.updateg();
  // this.compasLeve.updateg();
  this.equerre.updateg();
  this.rapporteur.updateg();
  this.regle.updateg();
  this.requerre.updateg();
  this.crayon.updateg();
  this.compas.montre(false);
  this.compas.leve = false;
  this.equerre.montre(false);
  this.rapporteur.montre(false);
  this.regle.montre(false);
  this.requerre.montre(false);
  this.crayon.montre(false);
};

/**
 * Fonction lancée après le chargement complet de la figure.
 * Toutes les actions de création d'objet de la figure créent leur propre élément graphique dans le DOM du svg
 * qu'elles stockent dans une variable this.g. Ces éléments sont initialeùent tous cachés.
 * Ensuite on recrée dans le DOM les éléments graphiques des outils pour qu'ils soient situés après les objets créés.
 * Puis on crée les icônes et on les initialise à leur état initial  pour le démarrage de la figure.
 */
iep.iepDoc.prototype.start = function() {
  this.started = true;
  this.creeElementsGraphiques();
  this.recreeOutils();
  this.creeIcones();
  this.InitialiseIcones();
  if (this.autoStart) this.onPlay();
  else {
    this.montreIcone("Pause",false);
    this.montreIcone("Play",true);
  }
};

/**
 * Fonction créant les icônes de la figure.
 */
iep.iepDoc.prototype.creeIcones = function() {
  // Toutes les images seront contenues dans un g element appelé barreIcones
  var nbIcones = 8;
  var t = this;
  this.barreIcones = document.createElementNS(iep.svgsn,"g");
  this.creeElementsPourIcones();
  this.iconeGoBegin = new iep.iconeGoBegin(0,0,"fonce");
  this.iconeGoBeginGris = new iep.iconeGoBegin(0,0,"clair");
  this.iconeGoBegin.onclick = function() {t.onGoBegin()};
  this.barreIcones.appendChild(this.iconeGoBegin);
  this.barreIcones.appendChild(this.iconeGoBeginGris);
  this.iconeStepPrev = new iep.iconeStepPrev(32,0,"fonce");
  this.iconeStepPrevGris = new iep.iconeStepPrev(32,0,"clair");
  this.iconeStepPrev.onclick = function() {t.onStepPrev()};
  this.barreIcones.appendChild(this.iconeStepPrev);
  this.barreIcones.appendChild(this.iconeStepPrevGris);
  this.iconePause = iep.iconePause(64,0,"fonce");
  this.iconePauseGris = iep.iconePause(64,0,"clair");
  this.iconePause.onclick = function() {t.onPause()};
  this.barreIcones.appendChild(this.iconePause);
  this.barreIcones.appendChild(this.iconePauseGris);
  this.iconePlay = new iep.iconePlay(96,0,"fonce");
  this.iconePlayGris = new iep.iconePlay(96,0,"clair");
  this.iconePlay.onclick = function() {t.onPlay()};
  this.barreIcones.appendChild(this.iconePlay);
  // Icône rouge à activer quand on rencontre une pause
  // Cette icône ne sera visible et active que quand on rencontre une action de pause
  this.iconePlay2 = new iep.iconePlay(96,0,"rouge");
  this.iconePlay2.setAttribute("visibility","hidden");
  this.iconePlay2.onclick = function() {t.onPlayContinuer()};
  this.barreIcones.appendChild(this.iconePlay2);
  this.barreIcones.appendChild(this.iconePlayGris);
  this.iconeRestart = new iep.iconeRestart(128,0,"fonce");
  this.iconeRestartGris = new iep.iconeRestart(128,0,"clair");
  this.iconeRestart.onclick = function() {t.onRestart()};
  this.barreIcones.appendChild(this.iconeRestart);
  this.barreIcones.appendChild(this.iconeRestartGris);
  this.iconeStepNext = new iep.iconeStepNext(160,0,"fonce");
  this.iconeStepNextGris = new iep.iconeStepNext(160,0,"clair");;
  this.iconeStepNext.onclick = function() {t.onStepNext()};
  this.barreIcones.appendChild(this.iconeStepNext);
  this.barreIcones.appendChild(this.iconeStepNextGris);
  this.iconeGoEnd = new iep.iconeGoEnd(192,0,"fonce");
  this.iconeGoEndGris = new iep.iconeGoEnd(192,0,"clair");
  this.iconeGoEnd.onclick = function() {t.onGoEnd()};
  this.barreIcones.appendChild(this.iconeGoEnd);
  this.barreIcones.appendChild(this.iconeGoEndGris);
  this.iconeXML = new iep.iconeXML(224,0,"fonce");
  this.iconeXMLGris = new iep.iconeXML(224,0,"clair");
  this.iconeXML.onclick = function() {t.onXML()};
  this.barreIcones.appendChild(this.iconeXML);
  this.barreIcones.appendChild(this.iconeXMLGris);

  this.hauteurBarre = 32;
  this.demiLargeurBarre = nbIcones*16;
  // this.xbarre et this.ybarre contiennent les coordonnées initiales de la barre d'outils dans le SVG
  this.xbarre = 50; // Barre placée à gauche de bord
  this.ybarre = 4;
  this.barreIcones.setAttribute("transform","translate(" + this.xbarre + ",4)");
  this.svg.appendChild(this.barreIcones);
  // Ajout ge gestionnaires de souris sur la barre d'icônes
  this.isDraggingBarre = false;
  var t = this;
  this.barreIcones.addEventListener("mousedown",function(ev) {t.onmousedownbarre(ev.clientX,ev.clientY)},false);
  this.barreIcones.addEventListener("mousemove",function(ev) {t.onmousemovebarre(ev.clientX,ev.clientY)},false);
  // C'est le SVG qui gère les événements mouseup
  this.barreIcones.addEventListener("touchstart",function(ev)
    {t.onmousedownbarre(ev.touches[0].pageX,ev.touches[0].pageY)},false);
  this.barreIcones.addEventListener("touchmove",function() {t.onmousemovebarre()},false);
  // C'est le svg qui gère les événements touhcance et touchend
  // this.barreIcones.addEventListener("touchcancel",function() {t.onmouseupbarre()},false);
  // Initialisation de la position relative de la barre d'outils
  // this.deltax et this.deltay contiendront de décalage relatif de la barre d'outils par rapport
  // à sa position initiale lorsqu'on la fait glisser.
  this.deltax = 0;
  this.deltay = 0;
};

/**
 * Fonction appelée quand on clique ou fait un appui tactile sur la barre d'icônes de la figure
 * @param {Integer} xmouse : l'abscisse du point cliqué par la souris
 * @param {Integer} ymouse : l'ordonnée du point cliqué par la souris
 */
iep.iepDoc.prototype.onmousedownbarre = function(xmouse,ymouse) {
  if (this.animationEnCours) return;
  this.isDraggingBarre = true;
  this.xcapturebarre = xmouse;
  this.ycapturebarre = ymouse;
};

/**
 * Fonction appelée au survol de la barre d'icônes par la souris ou du pointeur tactive
 */
iep.iepDoc.prototype.onmousemovebarre = function() {
  if (this.animationEnCours) {
    this.barreIcones.style.cursor = "default";
    this.isDraggingBarre =false;
    return;
  }
  this.barreIcones.style.cursor = "pointer";
};

/**
 * Fonction appelée au ssurvol du svg par la souris ou le pointeur tactile
 * @param {event} ev
 * @param {Integer} xmouse : l'abscisse pointée par la souris
 * @param {Integer} ymouse : l'ordonnée pointée par la souris
 */
iep.iepDoc.prototype.onmousemove = function(ev,xmouse,ymouse) {
  if (this.isDraggingBarre) {
    var w = parseInt(this.svg.getAttribute("width"));
    var h = parseInt(this.svg.getAttribute("height"));
    var dx = xmouse - this.xcapturebarre;
    var dy = ymouse - this.ycapturebarre;
    var delta_x = this.deltax + dx;
    var delta_y = this.deltay + dy;
    var xpos = this.xbarre + delta_x;
    var ypos = this.ybarre + delta_y;
    if ((xpos <= w - 2*this.demiLargeurBarre) && (xpos >= 0)
            && (ypos >= 0) && (ypos <= h - this.hauteurBarre)) {
      this.barreIcones.setAttribute("transform","translate(" + xpos + "," + ypos + ")");
      this.deltax = delta_x;
      this.deltay = delta_y;
      this.xcapturebarre = xmouse;
      this.ycapturebarre = ymouse;
    }
    ev.preventDefault(); // Pour éviter que des éléments texte soient sélectionnés en bougeant la souris
  }
  // ev.preventDefault(); // Pour éviter que des éléments texte soient sélectionnés en bougeant la souris
  // Abandonné car alors on e peut pls zoomer ou dézoomer sur périphérique mobile
};
/**
 * Fonction appelée lorsque le bouton de la souris est relâché sur le svg
 */
iep.iepDoc.prototype.onmouseup = function() {
  this.isDraggingBarre = false;
};
/**
 * Fonction appelée quand on relâche le bouton de la souris sur la figure
 */
iep.iepDoc.prototype.onmouseleave = function() {
  this.isDraggingBarre = false;
};
/**
 * Fonction appelée quand on clique sur le bouton XML de la barre d'outils
 * Ouvre une fenêtre affichant le code XML de la figure
 */
iep.iepDoc.prototype.onXML = function() {
  if (this.windowxml == null) this.windowxml = this.popup();
  else this.windowxml.focus();
};

/**
 * Fonction activant ou désactivant l'icône de nom nomIcone
 * Si bvisible est true, l'icône normale est montrée et l'icône grisée est cachée
 * Si bvisible est false,l'icône normale est masquée et l'icône grisée est montrée
 * @param {string} nomIcone
 * @param {boolean} bvisible
 */
iep.iepDoc.prototype.montreIcone = function(nomIcone,bvisible) {
  var nom = "icone" + nomIcone;
  this[nom].setAttribute("visibility", bvisible ? "visible" : "hidden");
  this[nom + "Gris"].setAttribute("visibility", bvisible ? "hidden" : "visible");
};
/**
 * Fonction activant l'icône rouge Continuer et désactivant les autres icônes
 */
iep.iepDoc.prototype.activeIconeContinuer = function() {
  this.montreIcone("GoBegin",false);
  this.montreIcone("StepPrev",false);
  this.montreIcone("Pause",false);
  this.montreIcone("Play",false);
  this.montreIcone("Restart",false);
  this.montreIcone("StepNext",false);
  this.montreIcone("GoEnd",false);
  this.iconePlayGris.setAttribute("visibility","hidden");
  this.iconePlay2.setAttribute("visibility","visible");
  this.montreIcone("XML",true);
};
/**
 * Fonction désactivant toutes les icônes suf l'icône de pause
 */
iep.iepDoc.prototype.InitialiseIcones = function() {
  this.montreIcone("GoBegin",false);
  this.montreIcone("StepPrev",false);
  this.montreIcone("Pause",true);
  this.montreIcone("Play",false);
  this.montreIcone("Restart",false);
  this.montreIcone("StepNext",false);
  this.montreIcone("GoEnd",false);
  this.montreIcone("XML",false);
};
/**
 * Fonction appelée lors d'un clic sur l'icône de retour de la figure au début
 */
iep.iepDoc.prototype.onGoBegin = function() {
  this.animationEnCours = false;
  this.initialise();
  this.montreIcone("GoBegin",false);
  this.montreIcone("StepPrev",false);
  this.montreIcone("Pause",false);
  this.montreIcone("Play",true);
  this.montreIcone("Restart",false);
  this.montreIcone("StepNext",true);
  this.montreIcone("GoEnd",true);
  this.montreIcone("XML",true);
  this.indiceActionEnCours = 0;
};
/**
 * Fonction appelée lors d'un clic sur l'icône de saut à la fin de la figure
 */
iep.iepDoc.prototype.onGoEnd = function() {
  this.animationEnCours = false;
  this.initialise();
  this.montreIcone("GoBegin",true);
  this.montreIcone("StepPrev",true);
  this.montreIcone("Pause",false);
  this.montreIcone("Play",false);
  this.montreIcone("Restart",true);
  this.montreIcone("StepNext",false);
  this.montreIcone("GoEnd",false);
  this.montreIcone("XML",true);
  this.indiceActionEnCours =this.actions.length;
  this.executeJusque(this.actions.length-1);
};
/**
 * Fonction appelée quand on clique sur l'icône play de la figure
 */
iep.iepDoc.prototype.onPlay = function() {
  this.animationEnCours = true;
  this.montreIcone("GoBegin",false);
  this.montreIcone("StepPrev",false);
  this.montreIcone("Pause",true);
  this.montreIcone("Play",false);
  this.montreIcone("Restart",false);
  this.montreIcone("StepNext",false);
  this.montreIcone("GoEnd",false);
  this.montreIcone("XML",false);
  this.actions[this.indiceActionEnCours].execute(false);
};
/**
 * Fonction appelée quand on clique sur l'icône continuer de la figure
 */

iep.iepDoc.prototype.onPlayContinuer = function() {
  if (this.indiceActionEnCours === this.actions.length) {
    this.onGoEnd();
    return;
  }
  this.iconePlay2.setAttribute("visibility","hidden");
  this.montreIcone("GoBegin",false);
  this.montreIcone("StepPrev",false);
  this.montreIcone("Pause",true);
  this.montreIcone("Play",false);
  this.montreIcone("Restart",false);
  this.montreIcone("StepNext",false);
  this.montreIcone("GoEnd",false);
  this.montreIcone("XML",false);
  this.animationEnCours = true;
  this.indiceActionEnCours++;
  this.actions[this.indiceActionEnCours].execute(false);
};
/**
 * Fonction appelée quand on clique sur l'icône de retour au début et jouer
 */

iep.iepDoc.prototype.onRestart = function() {
  var t = this;
  if (this.animationEnCours) {
    this.animationEnCours = false;
    setTimeout(function() {
      t.onRestart();
    }, 100);
    return;
  }
  if (this.animationEnCours) return;
  this.animationEnCours = false;
  this.initialise();
  this.indiceActionEnCours = 0;
  this.onPlay();
};
/**
 * Fonction appelée quand on clique sur l'icône pause de la figure
 */

iep.iepDoc.prototype.onPause = function() {
  var t = this;
  t.animationEnCours = false;
  // On laisse 5 dixièmes de seconde pour finir l'animation en cours.
  setTimeout(function() {
    t.montreIcone("GoBegin",true);
    t.montreIcone("StepPrev",true);
    t.montreIcone("Pause",false);
    t.montreIcone("Play",true);
    t.montreIcone("Restart",true);
    t.montreIcone("StepNext",true);
    t.montreIcone("GoEnd",true);
    t.montreIcone("XML",true);
  },500);
};
/**
 * Fonction appelée quand on clique sur l'icône suivant de la figure (pas à pas)
 */
iep.iepDoc.prototype.onStepNext = function() {
  this.animationEnCours = false;
  // this.indiceActionEnCours++;
  if (this.actions[this.indiceActionEnCours].actionVisible()) this.indiceActionEnCours++;
  else this.indiceActionEnCours = this.indiceProchaineActionVisible(this.indiceActionEnCours)+1;
  this.executeJusque(this.indiceActionEnCours-1);
  var derniereAction = this.indiceActionEnCours === this.actions.length;
  this.montreIcone("GoBegin",true);
  this.montreIcone("StepPrev",true);
  this.montreIcone("Pause",false);
  this.montreIcone("Play",derniereAction ? false : true);
  this.montreIcone("Restart",true);
  this.montreIcone("StepNext",derniereAction ? false : true);
  this.montreIcone("GoEnd",derniereAction ? false : true);
  this.montreIcone("XML",true);
};
/**
 * Fonction appelée quand on clique sur l'icône précédent de la figure (pas à pas)
 */
iep.iepDoc.prototype.onStepPrev = function() {
  this.animationEnCours = false;
  // this.indiceActionEnCours--;
  this.indiceActionEnCours = this.indicePrecedenteActionVisible(this.indiceActionEnCours-1);
  if (this.indiceActionEnCours > 0) this.executeJusque(this.indiceActionEnCours-1);
  var premiereAction = this.indiceActionEnCours === 0;
  this.montreIcone("GoBegin",premiereAction ? false : true);
  this.montreIcone("StepPrev",premiereAction ? false : true);
  this.montreIcone("Pause",false);
  this.montreIcone("Play",true);
  this.montreIcone("Restart",false);
  this.montreIcone("StepNext",true);
  this.montreIcone("GoEnd",true);
  this.montreIcone("XML",true);
};

/**
 * Fonction cachant tous les éléments graphiques de la figure, réinitialisant tous les outils
 * et réinitialisant la position de tous les objets ou instruments qui peuvent être translatés, tournés ou zoomés
 * à savoir les points et images
 */
iep.iepDoc.prototype.initialise = function() {
  var i;
  var svg = this.svg;
  for (i = 0; i < svg.childNodes.length; i++) {
    var el = svg.childNodes[i];
    if (el !== this.barreIcones)
      try{ // Pour compatibilité avec explorer
        svg.childNodes[i].setAttribute("visibility","hidden");
      }
      catch(e) {}
  }
  for (i = 0; i < this.actions.length; i++) {
    var action = this.actions[i];
    if (action instanceof iep.actionCreation) action.objet.initialisePosition();
  }
  this.initialiseOutils();
};
/**
 * Fonction donnant l'état de visibilité de l'instrument instrument après l'exécution
 * de l'action d'indice indaction
 * @param {iep.instrumentAncetre} instrument
 * @param {Integer} indaction
 * @returns {boolean}
 */
iep.iepDoc.prototype.getInstrumentVisibility = function(instrument,indaction) {
  if (indaction === -1) return false;
  var action;
  var visible = false;
  for (var i = 0; i <= indaction; i++) {
    action = this.actions[i];
    if (action instanceof iep.actionMontrerInstrument) {
      if (action.instrument === instrument) visible = true;
    }
    else if (action instanceof iep.actionMasquerInstrument) {
      if (action.instrument === instrument) visible = false;
    }
  }
  return visible;
};
/**
 * Fonction donnant l'état de visibilité de l'objet objet après l'exécution
 * de l'action d'indice indaction
 * @param {iep.ObjetBase} objet
 * @param {Integer} indaction
 * @returns {boolean}
 */
iep.iepDoc.prototype.getObjectVisibility = function(object,indaction) {
  if (indaction === -1) return false;
  var action;
  var visible = false;
  for (var i = 0; i <= indaction; i++) {
    action = this.actions[i];
    if (action instanceof iep.actionMontrer) {
      if (action.objet === object) visible = true;
    }
    else if (action instanceof iep.actionMasquer) {
      if (action.objet === object) visible = false;
    }
  }
  return visible;
};
/**
 * Fonction donnant l'état du compas (levé ou couché) après l'exécution
 * de l'action d'indice indaction.
 * Si le compas est couché, renvoie "couche", s'il est leve, renvoie "leve"
 * @param {Integer} indaction
 * @returns {string}
 */
iep.iepDoc.prototype.getCompasStatus = function(indaction) {
  if (indaction === -1) return "couche";
  var couche = true;
  var action;
  for (var i = 0; i <= indaction; i++) {
    action = this.actions[i];
    if (action instanceof iep.actionCoucherCompas) {
      couche = true;
    }
    else if (action instanceof iep.actionLeverCompas) {
      couche = false;
    }
  }
  return couche ? "couche" : "leve";
};
/**
 * Fonction renvoyant l'indice de la prochaine action qui aura une action visible sur la figure
 * à partir de l'indice ind
 * Si aucune action visible ne figure après ind, renvoie this.actions.length
 * @param {type} ind
 * @returns {iep.iepDoc.prototype.indiceProchaineActionVisible.i|iep.iepDoc.actions.length}
 */
iep.iepDoc.prototype.indiceProchaineActionVisible = function(ind) {
  var i;
  if (ind >= this.actions.lentgh) return this.actions.length;
  for (i = ind; i< this.actions.length; i++) {
    if (this.actions[i].actionVisible()) return i;
  }
  return this.actions.length;
};
/**
 * Fonction renvoyant l'indice de la précédene action qui aura une action visible sur la figure
 * à partir de l'indice ind
 * Si aucune action visible ne figure avant ind, renvoie 0
 * @param {type} ind
 * @returns {iep.iepDoc.prototype.indiceProchaineActionVisible.i|iep.iepDoc.actions.length}
 */

iep.iepDoc.prototype.indicePrecedenteActionVisible = function(ind) {
  var i;
  if (ind < 0) return 0;
  for (i = ind; i >= 0; i--) {
    if (this.actions[i].actionVisible()) return i;
  }
  return 0;
};
/**
 * Fonction montrant les instruments à leur étape de visibilité à l'étape n
 * @param {Integer} n : l'indice de la dernière action exécutée
 */
iep.iepDoc.prototype.montreInstrumentsEtape = function(n) {
  var visible;
  visible = this.getInstrumentVisibility(this.compas,n);
  if (this.compas.leve) this.compasLeve.montre(visible);
  else this.compas.montre(visible);
  visible = this.getInstrumentVisibility(this.crayon,n);
  this.crayon.montre(visible);
  visible = this.getInstrumentVisibility(this.equerre,n);
  this.equerre.montre(visible);
  visible = this.getInstrumentVisibility(this.rapporteur,n);
  this.rapporteur.montre(visible);
  visible = this.getInstrumentVisibility(this.regle,n);
  this.regle.montre(visible);
  visible = this.getInstrumentVisibility(this.requerre,n);
  this.requerre.montre(visible);
};
/**
 * Fonction excécutant sans animation toutes les actions de la figure jusqu'à l'indice indfin compris
 * @param {Integer} indfin
 */
iep.iepDoc.prototype.executeJusque = function(indfin) {
  if (indfin >= this.actions.length) return;
  this.initialise();
  this.initialiseOutils();
  this.animationEnCours = false;
  for (var i = 0; i <= indfin; i++)
    this.actions[i].execute(true); // true pour que l'exécution soit immédiate
  this.montreInstrumentsEtape(indfin);
};
/**
 * Fonction recherchant s'il y a déjà dans la liste doc.elements un élément de même id et de même
 * nature.
 * Si oui on remplace l'ancien. Sinon on rajoute element à la liste.
 * @param {iep.doc.elements[]} element
 */
iep.iepDoc.prototype.addElement = function(element) {
  var i,el;
  for (i=0; i<this.elements.length;i++) {
    el = this.elements[i];
    // S'il existe un élément de même type et de même id, on le remplace par le nouveau
    if ((el.id === element.id) && (el.objet === element.objet)) {
      this.elements[i] = element;
      return;
    }
  }
  // S'il n'y a pas d'élément du même type et de même id, on ajoute le nouvel élément
  this.elements.push(element);
};
/**
 * Fonction appelée dans le cas où on est sur que la liste doc.elements ne contient pas
 * déjà un élément de même id et de même nature que element
 * @param {type} element
 */
iep.iepDoc.prototype.pushElement = function(element) {
  this.elements.push(element);
};
/**
 * Fonction renvoyant l'objet de elements d'id égale à id s'il y en a une et sinon null
 * @param {type} id
 * @returns {Boolean|iep.iepDoc.elements|iep.iepDoc.prototype.element.el}
 */
iep.iepDoc.prototype.getElement = function(id,nature) {
  var i,el;
  for (i=0; i<this.elements.length;i++) {
    el = this.elements[i];
    if ((el.id === id) && (el.objet === nature)) return el;
  }
  return null;
};
/**
 * Fonction remplaçant l'élément de elements did id par e s'il y en a un
 * S'il n'y a pas délément d'id id renvoie false
 * @param {type} id
 * @param {type} e
 * @returns {boolean}
 */
iep.iepDoc.prototype.setElement = function(id,e) {
  var i;
  for (i=0; i<this.elements.length;i++) {
    if (this.elements[i].id === id) {
      this.elements[i] = e;
      return true;
    }
  }
  return false;
};
/**
 * Fonction ajoutant l'action action à la liste d'actions listeActions
 * @param {type} action
 */
iep.iepDoc.prototype.ajouteAction = function(action) {
  // On donne à action un indice por qu'lle sache quelle est son rand dans la liste
  // Ainsi elle pourra ensuite indiquer si son action sur la figure est visible ou non
  action.indice = this.actions.length;
  this.actions.push(action);
};
/**
 * Fonction créant toutes les actions de la figure
 * @param {type} ind
 */
iep.iepDoc.prototype.creeActions = function() {
  var ind,el,at,ato,objet,action,forme,xcrayon,ycrayon,id,
    angle,sens,vitesse,abs,ord,cibles,coord,tempo,len,ecart,vect,echelle,point;
  for (ind = 0; ind < this.tabact.length;ind++) {
    el = this.tabact[ind];
    id = iep.getId(el);
    at = el.getAttribute("mouvement");
    tempo = el.getAttribute("tempo");
    try {
      switch (at) {
        case "creer":
          ato = el.getAttribute("objet");
          if (ato != null) {
            switch (ato) {
              case "point":
                objet = new iep.point(this,id,parseFloat(iep.getAbs(el)),parseFloat(iep.getOrd(el)),
                  iep.getCouleur(el),iep.getEpaisseur(el));
                action = new iep.actionCreation(this,id,objet,tempo);
                break;
              case "angle":
                objet = new iep.angle(this,id,iep.getAbs(el),iep.getOrd(el),el.getAttribute("rayon"),
                  el.getAttribute("angle1"),el.getAttribute("angle2"),iep.getCouleur(el),iep.getEpaisseur(el),
                  iep.getOpaciteFond(el),el.getAttribute("forme"));
                action = new iep.actionCreation(this,id,objet,tempo);
                break;
              case "angle_droit":
                objet = new iep.angleDroit(this,id,el.getAttribute("abscisse_sommet"),el.getAttribute("ordonnee_sommet"),
                  el.getAttribute("abscisse_inter"),el.getAttribute("ordonnee_inter"),iep.getCouleur(el),iep.getEpaisseur(el));
                action = new iep.actionCreation(this,id,objet,tempo);
                break;
              case "longueur":
                objet = new iep.marqueSegment(this,id,iep.getAbs(el),iep.getOrd(el),
                  iep.getCouleur(el),iep.getEpaisseur(el),
                  el.getAttribute("forme"));
                action = new iep.actionCreation(this,id,objet,tempo);
                break;
              case "texte":
                objet = new iep.texte(this,id,iep.getAbs(el),iep.getOrd(el),iep.getCouleur(el),el.getAttribute("taille"));
                action = new iep.actionCreation(this,id,objet,tempo);
                break;
              case "repere":
                objet = new iep.repere(this,el.getAttribute("hauteur"),el.getAttribute("largeur"),
                el.getAttribute("haut"),el.getAttribute("gauche"),el.getAttribute("Xgrad"),
                el.getAttribute("Ygrad"),el.getAttribute("Xmin"),el.getAttribute("Xmax"),
                el.getAttribute("Ymin"),el.getAttribute("Ymax"),iep.getCouleur(el),
                el.getAttribute("grille"),el.getAttribute("axes"),el.getAttribute("etiquettes"));
                action = new iep.actionCreation(this,id,objet,tempo);
                break;
              case "quadrillage":
                var abscisse_bas_droite = el.getAttribute("abscisse_bas_droite");
                if (abscisse_bas_droite != null) { // Syntaxe non documentée dans la doc
                  abscisse_bas_droite = parseFloat(abscisse_bas_droite)/30;
                  var ordonnee_bas_droite = parseFloat(el.getAttribute("ordonnee_bas_droite"))/30;
                  var abscisse_haut_gauche = parseFloat(el.getAttribute("abscisse_haut_gauche"))/30;
                  var ordonnee_haut_gauche = parseFloat(el.getAttribute("ordonnee_haut_gauche"))/30;
                  objet = new iep.quadrillage(this,el.getAttribute("quadrillage"),String(ordonnee_bas_droite-ordonnee_haut_gauche),
                  String(abscisse_bas_droite-abscisse_haut_gauche),ordonnee_haut_gauche,abscisse_haut_gauche,iep.getCouleur(el))
                  action = new iep.actionCreation(this,"quadrillageIEP",objet,tempo);
                }
                else
                  objet = new iep.quadrillage(this,el.getAttribute("quadrillage"),el.getAttribute("hauteur"),
                  el.getAttribute("largeur"),el.getAttribute("haut"),el.getAttribute("gauche"),iep.getCouleur(el))
                action = new iep.actionCreation(this,"quadrillageIEP",objet,tempo);
                break;
              case "axe":
                objet =new iep.axe(this, el.getAttribute("pente"), el.getAttribute("largeur"),
                  el.getAttribute("haut"),el.getAttribute("gauche"),el.getAttribute("distanceBord"),
                  el.getAttribute("Xgrad"),el.getAttribute("Xmin"),el.getAttribute("Xmax"),iep.getCouleur(el));
               action = new iep.actionCreation(this,id,objet,tempo);
               break;
             default:
               action = null; // Pour ce que j'aurais oublié d'implémenter
            }
            if (action != null) this.ajouteAction(action);
            action.setReady();
          }
          break;
        case "montrer":
          ato = el.getAttribute("objet");
          if (iep.estInstrument(ato)) {
            abs = el.getAttribute("abscisse");
            ord = el.getAttribute("ordonnee");
            action = new iep.actionMontrerInstrument(this, this[ato],abs,ord,tempo);
            if (((ato === "crayon") || (ato === "compas")) && (abs != null) && (ord != null)) this[ato].translate(parseFloat(abs),parseFloat(ord));
          }
          else action = new iep.actionMontrer(this,id,ato,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "masquer":
          ato = el.getAttribute("objet");
          if (iep.estInstrument(ato)) action = new iep.actionMasquerInstrument(this, this[ato],tempo);
          else action = new iep.actionMasquer(this,id,ato,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "tracer":
          ato = el.getAttribute("objet");
          var cible = el.getAttribute("cible");
          if (cible != null) {
            objet = this.getElement(cible,"point"); // Le point vers lequel on veut translater l'objet
            abs = objet.xcons;
            ord = objet.ycons;
          }
          else {
            abs = parseFloat(iep.getAbs(el));
            ord = parseFloat(iep.getOrd(el));
          }
          switch(ato) {
            case "crayon":
              forme = el.getAttribute("forme");
              xcrayon = this.crayon.x;
              ycrayon = this.crayon.y;
              id = iep.getId(el);
              switch(forme) {
                case null: // Cas d'un segment
                  if (isNaN(abs) || isNaN(ord)) break;
                  objet = new iep.segment(this,id,xcrayon,ycrayon,abs,ord,iep.getCouleur(el),
                    iep.getEpaisseur(el),iep.getOpaciteTrait(el),iep.getStyleTrait(el),iep.getStyle(el));
                  this.crayon.translate(abs,ord);
                  break;
                case "droite": // Cas d'une droite
                  if (isNaN(abs) || isNaN(ord)) break;
                  objet = new iep.droite(this,id,xcrayon,ycrayon,abs,ord,iep.getCouleur(el),
                    iep.getEpaisseur(el),iep.getOpaciteTrait(el),iep.getStyleTrait(el),iep.getStyle(el));
                  this.crayon.translate(abs,ord);
                  break;
                case "demidroite": // Cas d'une demi-droite
                  if (isNaN(abs) || isNaN(ord)) break;
                  objet = new iep.demiDroite(this,id,xcrayon,ycrayon,abs,ord,iep.getCouleur(el),
                    iep.getEpaisseur(el),iep.getOpaciteTrait(el),iep.getStyleTrait(el),iep.getStyle(el));
                  this.crayon.translate(abs,ord);
                  break;
                case  "libre":
                  objet=new iep.ligneContinue(this,id,this.crayon.x, this.crayon.y,el.getAttribute("abscisses"),
                    el.getAttribute("ordonnees"),iep.getCouleur(el),iep.getEpaisseur(el),iep.getOpaciteTrait(el),
                    iep.getStyleTrait(el),iep.getStyle(el));
                  len = objet.abs.length;
                  this.crayon.translate(objet.abs[len-1],objet.ord[len-1]);
                  break;
                case  "polygone":
                  cibles = el.getAttribute("cibles");
                  if (cibles === null) {
                    objet= new iep.polygone(this,id,el.getAttribute("abscisses"),
                      el.getAttribute("ordonnees"),iep.getCouleur(el),iep.getEpaisseur(el),
                      iep.getCouleurFond(el),el.getAttribute("opacite"));
                  }
                  else {
                    coord = this.getCoord(cibles);
                    objet= new iep.polygone(this,id,coord.abs,coord.ord,
                      iep.getCouleur(el),iep.getEpaisseur(el),
                      iep.getCouleurFond(el),el.getAttribute("opacite"));
                  }
                  len = objet.abs.length;
                  this.crayon.translate(objet.abs[len-1],objet.ord[len-1]);
                  break;
              }
              vitesse = el.getAttribute("vitesse");
              if (vitesse == null) vitesse = "4";
              action = new iep.actionCreation(this,iep.getId(el),objet,tempo,vitesse);
              this.ajouteAction(action);
              action.setReady();
              break;
            case "trait":
              vitesse = el.getAttribute("vitesse");
              if (vitesse === null) vitesse = 10000;  // Instantané si vitesse pas précisé
              objet = new iep.segment(this,id,el.getAttribute("abscisse1"),el.getAttribute("ordonnee1"),
                el.getAttribute("abscisse2"),el.getAttribute("ordonnee2"),iep.getCouleur(el),
                iep.getEpaisseur(el),iep.getOpaciteTrait(el),iep.getStyleTrait(el),iep.getStyle(el));
              action = new iep.actionCreation(this,iep.getId(el),objet,tempo,vitesse); // Pas d'animation pour les traits
              this.ajouteAction(action);
              action.setReady();
              break;
            case "compas":
              objet = new iep.arc(this,id,this.compas.x,this.compas.y,this.compas.ecart,
                this.getAngle(el,"debut"),this.getAngle(el,"fin"),iep.getCouleur(el),iep.getEpaisseur(el),iep.getOpaciteTrait(el),
                iep.getStyleTrait(el));
              vitesse = el.getAttribute("sens");
              if (vitesse == null) vitesse = "8";
              action = new iep.actionCreation(this,iep.getId(el),objet,tempo,vitesse);
              this.ajouteAction(action);
              action.setReady();
              break;
            case "angle_droit": // Certains scripts utilisent tracer au lieu de créer pour les angles droits ...
              objet = new iep.angleDroit(this,id,el.getAttribute("abscisse_sommet"),el.getAttribute("ordonnee_sommet"),
                    el.getAttribute("abscisse_inter"),el.getAttribute("ordonnee_inter"),iep.getCouleur(el),iep.getEpaisseur(el));
              action = new iep.actionCreation(this,iep.getId(el),objet,tempo,vitesse);
              this.ajouteAction(action);
              action.setReady();
              break;
          }
          break;
        case "rotation":
          ato = el.getAttribute("objet");
          sens = el.getAttribute("sens");
          if (iep.estInstrument(ato)) {
            var cible = el.getAttribute("cible");
            // Au cas ou un auteur a mis à la fois un attribut cible et un attribut angle,
            // C'est l'attribut angle qui semble l'emporter (constaté par des tests).
            if ((cible != null) && (el.getAttribute("angle") === null)) {
              objet = this.getElement(cible,"point"); // Le point vers lequel on veut translater l'objet
              abs = objet.xcons;
              ord = objet.ycons;
              var vect = new iep.vect(this[ato].x,this[ato].y,abs,ord);
              if (vect.presqueNul()) angle = 0;
              else angle = -vect.angle();
            }
            else {
              if (ato === "compas") angle = this.getAngle(el,"angle");
              else angle = el.getAttribute("angle");
            }
            action = new iep.actionRotationInstrument(this,this[ato],angle,tempo,sens);
          }
          else {
            angle = el.getAttribute("angle");
            action = new iep.actionRotationObjet(this,id,angle,ato,tempo,sens);
          }
          this.ajouteAction(action);
          action.setReady();
          break;
        case "translation":
          ato = el.getAttribute("objet");
          var cible = el.getAttribute("cible");
          // Au cas où on a spécifié abscisse et ordonnée et cible, cible aa priorité
          if (cible != null) {
            objet = this.getElement(cible,"point"); // Le point vers lequel on veut translater l'objet
            abs = objet.xcons;
            ord = objet.ycons;
          }
          else {
            abs = parseFloat(iep.getAbs(el));
            ord = parseFloat(iep.getOrd(el));
          }
          vitesse = el.getAttribute("vitesse");
          if (iep.estInstrument(ato)) {
            this[ato].x = abs;
            this[ato].y = ord;
            action = new iep.actionTranslationInstrument(this,this[ato],abs,ord,tempo,vitesse);
          }
          else {
            // Dans le cas où on translate un point il faut modifier ses champs xcons et ycons
            // pour que tout objet ultérieur l'utilisant comme cible soit correctement initialisé
            if (ato === "point") {
              point = this.getElement(id,"point");
              point.xcons = abs;
              point.ycons = ord;
            }
            action = new iep.actionTranslationObjet(this,id,abs,ord,ato,tempo,vitesse);
          }
          this.ajouteAction(action);
          if ((ato === "crayon") || (ato === "compas"))
              this[ato].translate(abs,ord);
          action.setReady();
          break;
        case "zoom":
          ato = el.getAttribute("objet");
          vitesse = el.getAttribute("vitesse");
          if (iep.estInstrument(ato)) {
            echelle = el.getAttribute("echelle");
            this[ato].zoomfactor = parseFloat(echelle)/100;
            action = new iep.actionZoomInstrument(this,this[ato],echelle,tempo,vitesse);
          }
          else action = new iep.actionZoomObjet(this,id,el.getAttribute("echelle"),ato,tempo,vitesse);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "ecarter":
          var cible = el.getAttribute("cible");
          if (cible != null) {
            objet = this.getElement(cible,"point"); // Le point vers lequel on veut translater l'objet
            vect = new iep.vect(this.compas.x, this.compas.y,objet.xcons,objet.ycons);
            ecart = vect.norme();
          }
          else ecart = parseFloat(el.getAttribute("ecart"))*this.compas.zoomfactor;
          this.compas.ecart = ecart;
          action = new iep.actionEcarterCompas(this,ecart,tempo,el.getAttribute("vitesse"));
          this.ajouteAction(action);
          action.setReady();
          break;
        case "lever":
          action = new iep.actionLeverCompas(this,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "coucher":
          action = new iep.actionCoucherCompas(this,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;

        case "masquer_nombres":
          action = new iep.actionMontrerNombres(this,false,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "montrer_nombres":
          action = new iep.actionMontrerNombres(this,true,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "graduations":
          ato = el.getAttribute("objet");
          action = new iep.actionMontrerGraduations(this,this[ato],true,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "vide":
          ato = el.getAttribute("objet");
          action = new iep.actionMontrerGraduations(this,this[ato],false,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "retourner":
          this.compasRetourne = !this.compasRetourne;
          action = new iep.actionRetourner(this,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "glisser":
          action = new iep.actionGlisser(this,el.getAttribute("abscisse"),tempo,el.getAttribute("vitesse"));
          this.ajouteAction(action);
          action.setReady();
          break;
        case "chargement":
          // Attention, pour l'image, pas d'appel de creeAction(ind+1)
          // car c'est une fonction de callBack qui s'en charge une fois l'image chargée
          objet = new iep.image(this,id,el.getAttribute("url"));
          objet.prepareAction();
          // Pas de action.setReady car il faut attendre que la figure soit chargée
          break;
        case "ecrire": // Texte
          action = new iep.actionEcrireTexte(this,id,iep.getCouleur(el),el.getAttribute("taille"),
            el.getAttribute("texte"),el.getAttribute("style"),iep.getCouleurFondTexte(el),
            el.getAttribute("opacite_fond"),
            iep.getCouleurCadre(el),el.getAttribute("epaisseur_cadre"),
            el.getAttribute("marge"),el.getAttribute("marge_gauche"),
            el.getAttribute("marge_droite"),el.getAttribute("marge_haut"),
            el.getAttribute("marge_bas"),tempo);
          this.ajouteAction(action);
          action.prepare();
          break;
        case "nommer":
          objet = new iep.nomPoint(this,id,iep.getAbs(el),iep.getOrd(el),
            el.getAttribute("nom"),iep.getCouleur(el));
          action = new iep.actionNommerPoint(this,objet,tempo);
          this.ajouteAction(action);
          // action.setReady();
          action.prepare();
          break;
        case "pause":
          action = new iep.actionPause(this,tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
        case "modifier_longueur":
          ato = el.getAttribute("objet");
          action = new iep.actionModifierLongueur(this,this[ato],el.getAttribute("longueur"),tempo);
          this.ajouteAction(action);
          action.setReady();
          break;
      }
    }
    catch (e) {
      // On ne fait rien si xml incorrect
    }
  }
  // Une fois les actions créées on attend que les éléments asynchrones soient prêts pour continuer
  this.waitForReadyState();
}
/**
 * Si immediat est true, les actions sont exécutées immédiatement, sans animation et sans temporisation
 * @param {type} immediat
 */
iep.iepDoc.prototype.execute = function(immediat) {
  this.indiceActionEnCours = 0;
  this.actions[0].execute(immediat);
};
/**
 * Fonction appelée lorsqu'une action a été exécutée pour passer à l'exécution suivante.
 * Si immediat est true, on retourne sans rien faire
 * Si animationECours en false, on revient sans rien faire (pour arrêter une animation en cours)
 * Si l'action.tempo n'est pas nul, on attend action.tempo millièmes de seconde avant de passer à l'action suivante
 * indiceActionEnCours est l'indice de la prochaine action à exécuter
 * @param {boolean} immediat
 */
iep.iepDoc.prototype.actionSuivante = function(immediat) {
  var imm = (arguments.length === 0) ? false : immediat;
  if (!this.animationEnCours && !imm) {
    // if (this.indiceActionEnCours != this.actions.length-1) this.indiceActionEnCours++;
    return;
  } // Pas de passage à l'action suivante pour le mode pas à pas.
  var action = this.actions[this.indiceActionEnCours];
  if ((action.tempo === null) || imm) this.passageActionSuivante(imm);
  else {
    var t = this;
    setTimeout(function() {t.passageActionSuivante(imm);},action.tempo);
  }
};
/**
 * Fonction appelée par actionSuivante pour passer à l'action suivante
 * indiceActionEnCours est l'indice de la prochaine action à exécuter
 * @param {boolean} immediat
 */
iep.iepDoc.prototype.passageActionSuivante = function(immediat) {
  if (this.indiceActionEnCours !== this.actions.length-1) {
    this.indiceActionEnCours++;
    this.actions[this.indiceActionEnCours].execute(immediat);
  }
  else {
    this.indiceActionEnCours++;
    this.montreIcone("GoBegin",true);
    this.montreIcone("StepPrev",true);
    this.montreIcone("Pause",false);
    this.montreIcone("Play",false);
    this.montreIcone("StepNext",false);
    this.montreIcone("GoEnd",false);
    this.montreIcone("Restart",true);
    this.montreIcone("XML",true);
    this.animationEnCours = false;
  }
};
// Attention : Pour les fonctions suivantes ne pas utiliser de test d'identité ===
/**
 * Fonction renvoyant l'attribut couleur d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getCouleur = function(el) {
  var coul = el.getAttribute("couleur");
  if (coul == null) return "black";
  else return iep.couleur(coul);
};
iep.getCouleurCadre = function(el) {
  var coul = el.getAttribute("couleur_cadre");
  if (coul == null) return null;
  else return iep.couleur(coul);  
}
/**
 * Fonction renvoyant l'attribut couleur de fond d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */

iep.getCouleurFond = function(el) {
  var coul = el.getAttribute("couleur_fond");
  if (coul == null) return null;
  else return iep.couleur(coul);
};
/**
 * Fonction renvoyant l'attribut couleur de fond d'une action d'écriture de texte
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getCouleurFondTexte = function(el) {
  var coul = el.getAttribute("couleur_fond");
  if (coul == null) return null;
  else return iep.couleur(coul);
};
/**
 * Fonction renvoyant l'attribut épaisseur d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */

iep.getEpaisseur = function(el) {
  var ep = el.getAttribute("epaisseur");
  if ((ep == null) || (ep == 0)) return "1";
  else return ep;
};
/**
 * Fonction renvoyant l'attribut opacité de trait d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getOpaciteTrait = function(el) {
  var op = el.getAttribute("opacite");
  if (op == null) return "100";
  else return op;
};
/**
 * Fonction renvoyant l'attribut opacité de fond d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getOpaciteFond = function(el) {
  var op = el.getAttribute("opacite");
  if (op == null) return "50";
  else return op;
};
/**
 * Fonction renvoyant l'attribut le style de trait d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getStyleTrait = function(el) {
  var st = el.getAttribute("pointille");
  if (st == null) return "continu"; else return st;
};
/**
 * Fonction renvoyant l'attribut style de flèce d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */

iep.getStyle =function(el) {
  var st = el.getAttribute("style");
  if (st == null) return "normal"; else return st;
};
/**
 * Fonction renvoyant l'attribut id d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */

iep.getId = function(el) {
  return el.getAttribute("id");
};
/**
 * Fonction renvoyant l'attribut abscisse de trait d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getAbs = function(el) {
  return el.getAttribute("abscisse");
};
/**
 * Fonction renvoyant l'attribut ordonnée de trait d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */
iep.getOrd = function(el) {
  return el.getAttribute("ordonnee");
};
/**
 * Fonction renvoyant l'attribut point d'une action de création de polygône
 * @param {type} cible
 * @returns {iep.iepDoc.prototype.getCoord.res|Object}
 */
iep.iepDoc.prototype.getCoord = function(cible) {
  var a,i,abs,ord,res,len,ch;
  abs = "";
  ord = "";
  a = cible.split(',');
  len = a.length;
  for (i=0;i<len;i++) {
    ch = (i === 0) ? "" : ",";
    var obj = this.getElement(a[i],"point");
    abs += ch + obj.xcons;
    ord += ch + obj.ycons;
  }
  res = new Object();
  res.abs = abs;
  res.ord = ord;
  return res;
};
/**
 * Fonction renvoyant l'attribut angle d'une action
 * @param {Element} el element retourné par this.xmldoc.getElementsByTagName("action")[]
 * @returns {string|Couleur}
 */

iep.iepDoc.prototype.getAngle = function(el,attribute) {
  var a = el.getAttribute(attribute);
  if (a == null) return null;
  if (this.compasRetourne) return String(parseFloat(a)+180);
  else return a;
};
/**
 * Fonction créant l'icône play de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconePlay.svg}
 */
iep.iconePlay = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","-5,8 8,0 -5,-8");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  // svg.setAttribute("transform","translate(16,16)");
  return svg;
};
/**
 * Fonction créant l'icône pause de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 */
iep.iconePause = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var rect = document.createElementNS(iep.svgsn,"rect");
  rect.setAttribute("x",-6);
  rect.setAttribute("y",-7);
  rect.setAttribute("width",3);
  rect.setAttribute("height",14);
  rect.setAttribute("style","stroke:white;fill:white;");
  rect.setAttribute("transform","translate(16,16)");
  svg.appendChild(rect);
  rect = document.createElementNS(iep.svgsn,"rect");
  rect.setAttribute("x",2);
  rect.setAttribute("y",-7);
  rect.setAttribute("width",3);
  rect.setAttribute("height",14);
  rect.setAttribute("style","stroke:white;fill:white;");
  rect.setAttribute("transform","translate(16,16)");
  svg.appendChild(rect);
  // svg.setAttribute("transform","translate(16,16)");
  return svg;
};
/**
 * Fonction créant l'icône stepNext de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconeStepNext.svg}
 */
iep.iconeStepNext = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var rect = document.createElementNS(iep.svgsn,"rect");
  rect.setAttribute("x",5);
  rect.setAttribute("y",-6);
  rect.setAttribute("width",3);
  rect.setAttribute("height",12);
  rect.setAttribute("style","stroke:white;fill:white;");
  rect.setAttribute("transform","translate(16,16)");
  svg.appendChild(rect);
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","-7,6 2,0 -7,-6");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  return svg;
};
/**
 * Fonction créant l'icône stepPrev de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconeStepPrev.svg}
 */
iep.iconeStepPrev = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var rect = document.createElementNS(iep.svgsn,"rect");
  rect.setAttribute("x",-7);
  rect.setAttribute("y",-6);
  rect.setAttribute("width",3);
  rect.setAttribute("height",12);
  rect.setAttribute("style","stroke:white;fill:white;");
  rect.setAttribute("transform","translate(16,16)");
  svg.appendChild(rect);
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","7,6 -2,0 7,-6");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  // svg.setAttribute("transform","translate(16,16)");
  return svg;
};
/**
 * Fonction créant l'icône goEnd de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconeGoEnd.svg}
 */
iep.iconeGoEnd = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","-6,7 1,0 -6,-7");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","3,7 10,0 3,-7");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  // svg.setAttribute("transform","translate(16,16)");
  return svg;
};
/**
 * Fonction créant l'icône goBegin de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconeGoBegin.svg}
 */
iep.iconeGoBegin = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","6,7 -1,0 6,-7");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  var pol = document.createElementNS(iep.svgsn,"polygon");
  pol.setAttribute("points","-3,7 -10,0 -3,-7");
  pol.setAttribute("style","stroke:white;fill:white;");
  pol.setAttribute("transform","translate(16,16)");
  svg.appendChild(pol);
  return svg;
};
/**
 * Fonction créant l'icône restart de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconeRestart.svg}
 */
iep.iconeRestart = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var path = document.createElementNS(iep.svgsn,"path");
  path.setAttribute("d","M 0 -8 A 8 8 -90 1 1 -8 0");
  path.setAttribute("style","stroke:white;stroke-width:3;fill:none;");
  path.setAttribute("marker-end","url(#markerarrow");
  path.setAttribute("transform","translate(16,16)");
  svg.appendChild(path);
  return svg;
};
/**
 * Fonction créant l'icône XML de la figure
 * Si teinte == "fonce", la couleur bleu foncée est employée
 * Si "teinte == "clair", la couleur gris clair est employée
 * @param {Integer} x : l'abscisse de l'icône relative à la barre d'icônes
 * @param {Integer} y : l'ordonnée de l'icône relative à la barre d'icônes
 * @param {string} teinte : "fonce" ou "clair"
 * @returns {iep.iconeXML.svg}
 */
iep.iconeXML = function(x,y,teinte) {
  var svg = document.createElementNS(iep.svgsn,"svg");
  svg.setAttribute("x",x);
  svg.setAttribute("y",y);
  svg.setAttribute("width",32);
  svg.setAttribute("height",32);
  var circ = document.createElementNS(iep.svgsn,"circle");
  // le cercle entourant l'icône
  svg.appendChild(circ);
  circ.setAttribute("cx",16);
  circ.setAttribute("cy",16);
  circ.setAttribute("r",15);
  var filtre = (teinte === "fonce") ? "filtrebleu" : "filtregris";
  circ.setAttribute("style","stroke:none;" + "fill:url(#radial"+teinte+"); filter:url(#" + filtre + ");");
  var text = document.createElementNS(iep.svgsn,"text");
  text.setAttribute("pointer-events", "none");
  text.appendChild(document.createTextNode("XML"));
  text.setAttribute("x", 16);
  text.setAttribute("y", 21);
  text.setAttribute("style","font-family:Arial;font-size:9pt;fill:white;text-anchor:middle");
  svg.appendChild(text);
  return svg;
};

/**
 * Fonction rajoutant dans le svg parent des définitions defs qui serot utilisées
 * plusieurs fois dans les icônes (radialGradiant et marker pour la flèche de l'icône restart.
 * On les mets dans le svg barreIcones pour qu'il ne soient pas effacés lors de initialise()
 */
iep.iepDoc.prototype.creeElementsPourIcones = function() {
  var defs = document.createElementNS (iep.svgsn,"defs");
  defs.appendChild(iep.radialGradiant("#9999FF","#000099","radialfonce"));
  this.svg.appendChild(defs);
  defs = document.createElementNS (iep.svgsn,"defs");
  defs.appendChild(iep.radialGradiant("#CCCCCC","#666666","radialclair"));
  this.svg.appendChild(defs);
  defs = document.createElementNS (iep.svgsn,"defs");
  defs.appendChild(iep.radialGradiant("#FF9999","#FF0000","radialrouge"));
  this.svg.appendChild(defs);
  defs = document.createElementNS (iep.svgsn,"defs");
  defs.appendChild(iep.markerArrow("markerarrow"));
  this.barreIcones.appendChild(defs);
  defs = document.createElementNS (iep.svgsn,"defs");
  defs.appendChild(iep.filtreEclairageIcone("filtrebleu"));
  this.barreIcones.appendChild(defs);
  defs = document.createElementNS (iep.svgsn,"defs");
  defs.appendChild(iep.filtreEclairageIcone("filtregris"));
  this.barreIcones.appendChild(defs);
}
/**
 * Fonction renvoyant un radialGradiant utilisé pour le dessin des icônes
 * @param {string} clair : la couleur claire de gradiant
 * @param {string} fonce :  la couleur focée du gradiant
 * @param {Sring} id : l'id du gradiant
 * @returns {iep.radialGradiant.rg}
 */
iep.radialGradiant = function(clair, fonce, id) {
  var rg = document.createElementNS(iep.svgsn, "radialGradient");
  rg.setAttribute("id",id);
  var stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","0%");
  stop.setAttribute("style","stop-color:" + fonce + ";");
  rg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","80%");
  stop.setAttribute("style","stop-color:" + clair + ";");
  rg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","100%");
  stop.setAttribute("style","stop-color:" + fonce + ";");
  rg.appendChild(stop);
  return rg;
};
/**
 * Fonction renvoyant un marker qui sera utilisé pour la flèce de l'icône restart
 * @param {string} id : L'id du marker
 * @returns {iep.markerArrow.marker}
 */
iep.markerArrow = function(id) {
  var marker = document.createElementNS(iep.svgsn,"marker");
  marker.setAttribute("id",id);
  marker.setAttribute("markerWidth","4");
  marker.setAttribute("markerHeight","4");
  marker.setAttribute("refX","0");
  marker.setAttribute("refY","2");
  marker.setAttribute("orient","auto");
  var pathm = document.createElementNS(iep.svgsn,"path");
  pathm.setAttribute("d","M 0 0 2 2 0 4 Z");
  pathm.setAttribute("style","fill:white;");
  marker.appendChild(pathm);
  return marker;
};
/**
 * Foncion renvoyant un filtre qui sera utilisé pour donner un aspect de relief aux icônes
 * @param {type} id : L'id du filtre
 * @returns {iep.filtreEclairageIcone.filter}
 */
iep.filtreEclairageIcone = function(id) {
  var filter = document.createElementNS(iep.svgsn,"filter");
  filter.setAttribute("x","0");
  filter.setAttribute("y","0");
  filter.setAttribute("width","100%");
  filter.setAttribute("height","100%");
  filter.setAttribute("color-interpolation-filters","sRGB");
  var dl = document.createElementNS(iep.svgsn,"feDiffuseLighting");
  dl.setAttribute("lighting-color","white");
  dl.setAttribute("surfaceScale","1");
  dl.setAttribute("diffuseConstant","1.2");
  dl.setAttribute("in","SourceGraphic");
  dl.setAttribute("result","diffOut");
  var pointLight = document.createElementNS(iep.svgsn,"fePointLight");
  pointLight.setAttribute("x","5");
  pointLight.setAttribute("y","5");
  pointLight.setAttribute("z","15");
  dl.appendChild(pointLight);
  filter.appendChild(dl);
  var fc = document.createElementNS(iep.svgsn,"feComposite");
  fc.setAttribute("in","SourceGraphic");
  fc.setAttribute("in2","diffOut");
  fc.setAttribute("operator","arithmetic");
  fc.setAttribute("k1","1");
  fc.setAttribute("k2","0");
  fc.setAttribute("k3","0");
  fc.setAttribute("k4","0");
  filter.appendChild(fc);
  filter.setAttribute("id",id);
  return filter;
};
/**
 * Fonction renvoyant une fenêtre contenant le code XML de la figure
 */
iep.iepDoc.prototype.popup = function() {
  // ouvre une fenetre sans barre d'etat, ni d'ascenceur
  var w = window.open("",'popup','width=700,height=450,toolbar=no,scrollbars=no,resizable=yes');
  w.iepDoc = this;
  var doc = w.document;
  doc.title = "Code Xml de la figure";
  var div = document.createElement("div");
  var form = document.createElement("form");
  div.appendChild(form);
  div.setAttribute("align","center");
  doc.body.appendChild(div);
  var ta = document.createElement("textarea");
  form.appendChild(ta);
  ta.setAttribute("cols",80);
  ta.setAttribute("rows",25);
  ta.setAttribute("readonly",true);
  ta.style.fontSize = "13px";
  ta.scrollIntoView();
  // Si le script ne comporte pas de retours à la ligne (une seule ligne), on en rajoute
  var ch = this.codexml;
  ch = ch.replace(/>\s*</g,">\n<");
  ta.appendChild(document.createTextNode(ch));
  ta.select();
  // w.onbeforeunload = function(){this.iepDoc.windowxml = null;};
  w.addEventListener("unload",function(){this.iepDoc.windowxml = null;});
  return w;
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

iep.cc1 = ["noir","rouge","vert","bleu","blanc","gris"];
iep.cc2 = ["black","red","green","blue","white","grey"];
/**
 * Fonction renvoyant une chaîne de carcatère représentant en svg la couleur
 * correspondant au contenu de ch (couleur en Flash)
 * @param {string} ch
 * @returns {Couleur}
 */
iep.couleur = function(ch) {
  var i,s,t;
  if (ch === "0") return "black";
  for (i = 0; i < iep.cc1.length; i++) {
    if (iep.cc1[i] === ch) return iep.cc2[i];
  }
  if (ch.substring(0,2)==="0x") {
    s = "000000"+ch.substring(2);
    t  = s.substring(s.length-6);
    ch = "#" + t;
  }
  else {
    if (iep.chiffre(ch.charAt(0))) {
      ch = parseFloat(ch).toString(16);
      s = "000000"+ch;
      t  = s.substring(s.length-6);
      ch = "#" + t;
    }
    else {
      if (ch.charAt(0) === "#") {
        s = "000000"+ch.substring(1);
        t  = s.substring(s.length-6);
        ch = "#" + t;
      }
    }
  }
  return ch;
};

/**
 * Une chaine de la forme #xxyyzz ou xx yy et zz sont les composantes RVB en hexa
 * @typedef Couleur
 * @type {string}
 */
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe vecteur
 * Peut être initialisé par les coordonnées de l'origine et de l'extrémité ou
 * par deux coordonnées
 * @constructor
 * @param {type} x1 : Abscisse de l'origine ou première coordonnée si deux paramètres
 * @param {type} y1 : Ordonnée de l'origine ou première coordonnée si deux paramètres
 * @param {type} x2 : Abscisse de l'extrémité si quatre paramètres
 * @param {type} y2 : Ordonnée de l'extrémité si quatre paramètres
 */
iep.vect = function(x1,y1,x2,y2) {
  if (arguments.length > 2) {
    this.x = x2-x1;
    this.y = y2-y1;
  }
  else {
    this.x = x1;
    this.y = y1;
  }
};
/**
 * Fonction renvoyant true si la norme du vecteur est inférieur oue égale à 10^-9
 * @returns {boolean}
 */
iep.vect.prototype.presqueNul = function() {
  return iep.zero(this.x) && iep.zero(this.y);
}
/**
 * Fonction renvoyant la norme du vecteur
 * @returns {Number}
 */
iep.vect.prototype.norme = function() {
  return Math.sqrt(this.x*this.x + this.y*this.y);
};
/**
 * Renvoie un vecteur de norme norme, colinéaire à v et de même sens
 * @param {Float} normeSouhaitee :  la norme du vecteur renvoyé
 * @returns {iep.vect}
 */
iep.vect.prototype.vecteurColineaire = function(normeSouhaitee) {
  var norm = this.norme();
  return new iep.vect(this.x/norm*normeSouhaitee,this.y/norm*normeSouhaitee);
};
/**
 * Fonction faisiant tourner un vecteur de l'angle ang et renvoyant un nouveua vecteur
 * correspondant à cette transformation
 * @param {type} angle : L'ange de rotation en degrés
 * @param {type} v
 */
iep.vect.prototype.tourne = function(angle) {
  var kc,ks;
  kc = Math.cos(angle*iep.convDegRad);
  ks = Math.sin(angle*iep.convDegRad);
  return new iep.vect(kc*this.x - ks*this.y,kc*this.y + ks*this.x);
};
/**
 * Renvoie la mesure en degrés de l'angle polaire d'un vecteur non nul
 * Le nombre renvoyé est entre 0 et 380 (exclu)
 * @returns {Number}
 */
iep.vect.prototype.angle = function() {
  var angle;
  if ((this.x === 0) && (this.y === 0)) return 0;
  else {
    if (this.x === 0) 
    {
      if (this.y > 0) 
      angle = 3*Math.PI/2;
      else angle = Math.PI/2;
    }
    else {
     if (this.x >= 0)
        angle = Math.atan(-this.y/this.x);
     else
     {
        angle = Math.PI-Math.atan(this.y/this.x);
     };
     if (angle < 0) angle = angle + 2*Math.PI;
    }
  }
  return angle*iep.convRadDeg;
}

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe ancêtre de tous les instruments.
 * Contient deux membres this.x et this.y contenant les coordonnées de l'instrument
 * un membre this.angle contenant l'angle de l'instrument avec l'ohorizontale
 * et un membre this.zoomfactor contenant le facteur d'agrandissement de l'instrument.
 * @constructor
 * @param {iep.iepDoc} doc : le document contenant la figure et les instruments
 * @returns {undefined}
 */
iep.instrumentAncetre = function(doc) {
  this.doc = doc;
  this.x = 200;
  this.y = 400;
  this.angle = 0; //Sera modifié au départ seulement pour le crayon
  this.zoomfactor = 1;
  this.visible = false;
};
/**
 * Fonction mettant l'instrument en position visible et montrant l'élement
 * graphique le représentant dans le DOM du svg de la figure
 * @param {boolean} bvisible
 */
iep.instrumentAncetre.prototype.montre = function(bvisible) {
  this.visible = bvisible;
  this.g.setAttribute("visibility", bvisible ? "visible" : "hidden");
};
/**
 * Fonction retirant du DOM du svg de la figure l'élément graphique représentant
 * l'instrument, le recréant et le rajoutant ensuite de façon que l'instrument
 * soit après les éléments graphiques représentant les objets de la figure et ne
 * soit pas recouvert par eux.
 * @returns {undefined}
 */
iep.instrumentAncetre.prototype.updateg = function() {
  this.initialisePosition();
  this.doc.svg.removeChild(this.g);
  this.doc.svg.appendChild(this.g);
};
/**
 * Fonction amenant l'instruemnt aux coordonnées (x,y) sans changer l'angle
 * ni le rapport de zoom.
 * @param {type} x : La nouvelle abscisse
 * @param {type} y : La nouvelle ordonnée
 * @returns {undefined}
 */
iep.instrumentAncetre.prototype.translate = function(x,y) {
  this.setPosition(x,y,this.angle,this.zoomfactor);
};
/**
 * Fonction envoayan l'angle initial de l'instruement.
 * Sera redéfini pour le crayon à -40°
 * @returns {Number}
 */
iep.instrumentAncetre.prototype.angleInit = function() {
  return 0;
}
/**
 * Fonction modifiant la position relative du g element représentant l'instrument
 * dans le svg de la figure pour qu'i soit à la bonne position, bon angle et bon zoom
 * Sera redéfini pour le compas et le compas levé.
 * @param {type} x
 * @param {type} y
 * @param {type} angle
 * @param {type} zoomfactor
 */
iep.instrumentAncetre.prototype.setPosition = function(x,y,angle,zoomfactor) {
  var zoom;
  this.x = x;
  this.y = y;
  this.angle = angle;
  if (arguments.length >= 4) {
    zoom = zoomfactor;
    this.zoomfactor = zoom;
  }
  else zoom = this.zoomfactor;
  this.g.setAttribute("transform","scale(" + zoom + ") translate(" +
    String(x/zoom) + "," + String(y/zoom) + ") rotate(" + angle + ")");
};
/**
 * Fonction initialisant la position de l'instrument
 */
iep.instrumentAncetre.prototype.initialisePosition = function() {
  this.setPosition(200,400,this.angleInit(),1);
};
/**
 * Fonction lançant une animation de l'instrument par translation
 * @param {Float} xfin : l'abscisse de l'instrument à la fin de la translation
 * @param {Float} yfin : l'ordonnée de l'instrument à la fin de la translation
 * @param {Float} pix : Le nombre de pixels de la translation à chaque dixième de seconde
 */
iep.instrumentAncetre.prototype.lanceAnimationTranslation = function(xfin,yfin,pix10) {
  this.xfin = xfin;
  this.yfin = yfin;
  this.pix = Math.abs(pix10/2); //Dans la version JavaScript on quadruple la fréquence
                                // en divisant le pas par 4 
                                // (en fait on corrige par car la rapidité semble double de celle annoncée dans le mode d'emploi)
  var v = new iep.vect(this.x,this.y,xfin,yfin);
  this.dist = v.norme(); // La distance entre la position actuelle et la position finale
  if (this.dist == 0) {
    this.doc.actionSuivante();
    return;
  }
  this.vect = v.vecteurColineaire(this.pix);
  var t = this;
  this.timer = setInterval(function(){iep.instrumentAncetre.actionPourTranslation.call(t)},25);
};
// On poursuit l'animation tant que la distance entre la position actuelle et la psoition de fin
// est inférieure à la distance précédente
/**
 * Fonction appelée par un timer lors de l'animation de translation de l'instrument
 */
iep.instrumentAncetre.actionPourTranslation = function() {
  var x = this.x + this.vect.x;
  var y = this.y + this.vect.y;
  var u = new iep.vect(x,y,this.xfin,this.yfin);
  var d = u.norme();
  if ((d > this.dist) || !this.doc.animationEnCours) {
    this.setPosition(this.xfin,this.yfin,this.angle);
    if (this == this.doc.compas) { 
      if ((this.doc.compasLeve != null) && (this.doc.compasLeve.visible)) 
        this.doc.compasLeve.setPosition(this.xfin,this.yfin,this.angle);;
    }
    clearInterval(this.timer);
    this.doc.actionSuivante();
    return;
  }
  else {
    this.dist = d;
    this.setPosition(x,y,this.angle);
    if (this == this.doc.compas) { 
      if ((this.doc.compasLeve != null) && (this.doc.compasLeve.visible)) 
        this.doc.compasLeve.setPosition(x,y,this.angle);;
    }
  }
};
/**
 * Fonction lançant une animation de l'instrument par rotation
 * @param {Float} l'angle de fin de l'objet avec l'horizontale après la rotation
 * @param {Integer} deg10 : le nombre de degrés dont on tounre à chaque
 * dixième de seconde
 */
iep.instrumentAncetre.prototype.lanceAnimationRotation = function(angfin,deg10) {
  // Il faut choisir le déplacement "le plus court"
  var ang1 = iep.mesurePrincDeg(this.angle);
  var ang2 = iep.mesurePrincDeg(angfin);
  if (Math.abs(ang2-ang1)>180) {
    if (ang2>ang1) ang2 = ang2-360;
    else ang2 = ang2+360;
  }
  this.anglefin = ang2;
  this.angle = ang1;
  // Contraiement à ce qui est dit dans la doc d'instrumenpoche il semble que le pas par défaut soit de 16 °
  // et même un peu plus
  this.pasdeg = deg10/3; // Dans la version JavaScript on quadruple la fréquence
                         // En divisant le pas par 4
                         // Mais en fait par deux pour compatibilité observée
  var sens = (ang2 >= ang1) ? 1 : -1; // 1 pour le sens direct svg
  this.pasdeg *= sens;
  this.distang = Math.abs(ang2-this.angle);
  var t = this;
  this.timer = setInterval(function(){iep.instrumentAncetre.actionPourRotation.call(t)},25);
};
/**
 * Fonction appelée par un timer lors de l'animation de rotation de l'instrument
 */
iep.instrumentAncetre.actionPourRotation = function() {
  var ang = parseFloat(this.angle) + parseFloat(this.pasdeg);
  var dis = Math.abs(ang-this.anglefin);
  if ((dis > this.distang) || !this.doc.animationEnCours) {
    this.setPosition(this.x,this.y,this.anglefin);
    clearInterval(this.timer);
    this.doc.actionSuivante();    
    return;
  }
  else {
    this.distang = dis;
    this.setPosition(this.x,this.y,ang);
  }
};
/**
 * Fonction lançant une animation de l'instrument par zoom
 * @param {Integer} zoomfin : le zoom final quand l'animation est finie
 * @param {Float} vitesse : paramètre représentant la vitesse d'animation
 */
iep.instrumentAncetre.prototype.lanceAnimationZoom = function(zoomfin,vitesse) {
  this.zoomfin = zoomfin;
  this.vitesse = parseInt(vitesse);
  this.pas = this.vitesse*0.05*(this.zoomfin - this.zoomfactor); // On augmente
  // par défaut de 5% de vitesse tous les dixièmes de seconde
  this.senspos = (this.pas >=0);
  var t = this;
  this.timer = setInterval(function(){iep.instrumentAncetre.actionPourZoom.call(t)},100);
}
/**
 * Fonction appelée par un timer lors de l'animation de zoom de l'instrument
 */
iep.instrumentAncetre.actionPourZoom = function() {
  var z = this.zoomfactor + this.pas;
  if (this.doc.animationEnCours && ((this.senspos && (z < this.zoomfin)) || (!this.senspos && (z>this.zoomfin)))) 
    this.setPosition(this.x,this.y,this.angle,z);
  else {
    this.zoomfactor = this.zoomfin;
    this.positionne();
    clearInterval(this.timer);
    this.doc.actionSuivante();    
    return;
  }
};
/**
 * Fonction recalculant l'élément graphique représentant l'instrument et
 * remettant son élément grapique en accord avec cette position
 */
iep.instrumentAncetre.prototype.positionne = function() {
  this.setPosition(this.x,this.y,this.angle,this.zoomfactor);
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * @constructor
 * @extends iep.instrumentAncetre
 * Classe représentant la règle de la figure
 * @param {iep.iepDoc} doc : le document propriétaire de la figure
 */
iep.regle = function(doc) {
  iep.instrumentAncetre.call(this,doc);
  this.x = 100;
  this.y = 400;
  this.angle = 0;
  this.longueur =  15;  // Longueur en cm. Peut être modifier par actionModifierLongueur
                        // 30 pixels par cm
  this.graduationVisible = true;
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(this.x,this.y,this.angle);
  doc.svg.appendChild(this.g);
};
iep.regle.prototype = new iep.instrumentAncetre();
/**
 * Fonction mettant dans this.g l'élément graphique représentant la règle
 * dans le DOM du svg de la figure
 */
iep.regle.prototype.creeg = function () {
  var li, text;
  var hauteurPolice = 9;
  var longint = this.longueur*30 + 15;
  var larg = 57;
  var largeur = String(larg);
  var ray = 6; // Rayon de courbure des coins
  var rayon = String(ray);
  var g = document.createElementNS(iep.svgsn, "g");
  var rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("x", -rayon - 1);
  rect.setAttribute("y", 0);
  rect.setAttribute("width", String(longint+ray+1));
  rect.setAttribute("height", largeur);
  rect.setAttribute("rx", rayon);
  rect.setAttribute("ry", rayon);
  rect.setAttribute("style", "stroke:#999999;stroke-width:2; fill: #c6cbe8; fill-opacity: 0.5;");
  g.appendChild(rect);
  var line = document.createElementNS(iep.svgsn, "line");
  line.setAttribute("x1",-rayon);
  line.setAttribute("y1",27);
  line.setAttribute("x2",longint);
  line.setAttribute("y2",27);
  line.setAttribute("style", "stroke:#999999;stroke-width:2;");
  g.appendChild(line);
  // On crée les graduations
  this.graduations = document.createElementNS(iep.svgsn, "g");
  for (var i = 0; i <= this.longueur*10; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    var x = 3*i;
    li.setAttribute("x1", x);
    li.setAttribute("y1", 0);
    li.setAttribute("x2", x);
    li.setAttribute("y2", i%10 === 0 ? 12 : (i%5 === 0 ? 9 : 6));
    if (i%10 === 0) {
      text = document.createElementNS(iep.svgsn,"text");
      text.setAttribute("pointer-events", "none");
      text.appendChild(document.createTextNode(String(i/10)));
      text.setAttribute("x", x);
      text.setAttribute("y", 12 + hauteurPolice);
      text.setAttribute("style","font-family: monospace;font-size: "+ hauteurPolice + "pt;text-anchor:middle");
      this.graduations.appendChild(text);
    }
    li.setAttribute("style", "stroke:black;stroke-width:0.7;");
    this.graduations.appendChild(li);
  }
  // On rajoute le texte Sesamath
  text = document.createElementNS(iep.svgsn,"text");
  text.setAttribute("pointer-events", "none");
  text.appendChild(document.createTextNode("Sésamath"));
  text.setAttribute("x", longint/2 - ray -3);
  text.setAttribute("y", largeur - 5);
  text.setAttribute("style","font-family: Arial;font-size: 8pt;font-weight:bold;fill: maroon;text-anchor:middle;");
  g.appendChild(text);
  g.appendChild(this.graduations);
  this.g = g;
};
/**
 * Fonction mettant les graduations de la règle en mode visible
 * et les endnt visibles dans le svg si l'instrument y est visible
 * @param {boolean} bvisible
 */
iep.regle.prototype.montreGraduations = function(bvisible) {
  this.graduationVisible = bvisible;
  this.graduations.setAttribute("visibility", this.visible ? (bvisible ? "visible" : "hidden") : "hidden");
}
/**
 * Fonction montrant la régle ou la cachant suivant la valeur de bvisible
 * @param {type} bvisible
 */
iep.regle.prototype.montre = function(bvisible) {
  iep.instrumentAncetre.prototype.montre.call(this,bvisible);
  if (bvisible) 
   this.graduations.setAttribute("visibility", this.graduationVisible ? "visble" : "hidden");
  else {
    this.graduations.setAttribute("visibility","hidden");
  }
};


/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * @constructor
 * @extends iep.instrumentAncetre
 * Classe représentant le rapporteur de la figure.
 * @param {type} doc
 */
iep.rapporteur = function(doc) {
  iep.instrumentAncetre.call(this,doc);
  this.graduationExterneVisible = true;
  this.graduationInterneVisible = true;
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(this.x,this.y,this.angle);
  doc.svg.appendChild(this.g);
};
iep.rapporteur.prototype = new iep.instrumentAncetre();
/**
 * Fonction mettant dans this.g l'élément graphique représentant le rapporteur dans
 * le DOM du svg de la figure
 */
iep.rapporteur.prototype.creeg = function() {
  var path,p,g,li,i,text,circ;
  var hauteurPolice = 10;
  var hauteurPoliceInterne = 9;
  var largeurBarre = "13";
  var ray = 156;
  var rayon = String(ray); // Le rayon extérieur
  var rayInt = 89;
  var rayonInt = String(rayInt); // Le rayon intérieur
  var raytraitint = "116"; // Le rayon du trait interne
  var rayonMire = "7";
  g = document.createElementNS(iep.svgsn, "g");
  p = document.createElementNS(iep.svgsn, "path");
  // Le pourtour
  path = "M " + rayon + " 0 A " + rayon + " " + rayon + " 0 0 0 -" + rayon + " 0 L " +
          "-" + rayon + " " + largeurBarre + " L " + rayon + " " + largeurBarre +
          " L " + rayon + " 0";
  // L'intérieur qui doit être tacé dans l'autre sens
  path += "M 0 0 L -" + rayonInt + " 0 A " + rayonInt + " " + rayonInt + " 0 0 1 " +
          rayonInt + " 0 Z";
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:#999999;stroke-width:1; fill: #c6cbe8; fill-opacity: 0.5");
  g.appendChild(p);
  // La mire
  p = document.createElementNS(iep.svgsn, "path");
  path = "M " + rayonMire + " 0 A " + rayonMire + " " + rayonMire + " 0 0 0 " +
          "-" + rayonMire + " 0 M 0 0 L 0 -" + rayonMire;
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke: #666666;stroke-width:1; fill: none");
  g.appendChild(p);
  // Les graduations externes
  for (i = 0; i <= 180; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("x1", rayon);
    li.setAttribute("y1", 0);
    li.setAttribute("x2", i%10 === 0 ? ray-20 :(i%5 === 0 ? ray - 10 : ray - 5));
    li.setAttribute("y2", 0);
    li.setAttribute("transform", "rotate(-" + i + ")");
    li.setAttribute("style", "stroke: #333333;stroke-width:0.7;");
    g.appendChild(li);
  }
  // Les nombres de la graduation externe
  this.gradext = document.createElementNS(iep.svgsn, "g");
  for (i = 0; i <= 180; i++) {
    if (i%10 === 0) {
      text = document.createElementNS(iep.svgsn,"text");
      text.setAttribute("pointer-events", "none");
      text.appendChild(document.createTextNode(i));
      text.setAttribute("x", 0);
      text.setAttribute("y", 0);
      text.setAttribute("style","font-family: arial;font-size: "+ hauteurPolice +
              "px;text-anchor: middle; fill: black");
      text.setAttribute("transform", " scale(-1) rotate(" + String(-i-90) + ") translate(0," +
              String(-ray + 22 + hauteurPolice) + ")");
      this.gradext.appendChild(text);
    } 
  }
  g.appendChild(this.gradext);
  // On rajoute le trait interne séparat les graduatiosn externes et internes
  var dep = ray - 26 - hauteurPolice;
  p = document.createElementNS(iep.svgsn, "path");
  path = "M " + String(dep) + " 0 L " + raytraitint + " 0 A " + 
          raytraitint + " " + raytraitint + " 0 0 0 -" + raytraitint + " 0 L " +
          String(-dep) + " 0";
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke: #666666;stroke-width:1; fill: none");
  g.appendChild(p); 
  // On rajoute les graduatiions internes
  for (i = 0; i <= 18; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("x1", rayInt);
    li.setAttribute("y1", 0);
    li.setAttribute("x2", rayInt + 10);
    li.setAttribute("y2", 0);
    li.setAttribute("transform", "rotate(-" + String(10*i) + ")");
    li.setAttribute("style", "stroke: #333333;stroke-width:0.7;");
    g.appendChild(li);    
  }
  // Les nombres de la graduation interne
  this.gradint = document.createElementNS(iep.svgsn, "g");
  for (i = 0; i <= 18; i++) {
      text = document.createElementNS(iep.svgsn,"text");
      text.setAttribute("pointer-events", "none");
      text.appendChild(document.createTextNode(180 - 10*i));
      text.setAttribute("x", 0);
      text.setAttribute("y", 0);
      text.setAttribute("style","font-family: arial;font-size: "+ hauteurPoliceInterne +
              "px;text-anchor: middle; fill: black");
      text.setAttribute("transform", "scale(-1) rotate(" + String(-10*i-90) + ") translate(0," +
              String(-rayInt - 14) + ")");
      // text.setAttribute("transform", "rotate(" + String(-10*i+1) + ")");
      this.gradint.appendChild(text);    
  }
  g.appendChild(this.gradint);
  // On matérialise le centre par un petit rond (n'existait pas).
  circ = document.createElementNS(iep.svgsn, "circle");
  circ.setAttribute("cx", 0);
  circ.setAttribute("cy", 0);
  circ.setAttribute("r", 2);
  circ.setAttribute("style","stroke: black; stroke-width: 1;fill: none");
  g.appendChild(circ);
  // On rajoute le texte Sesamath dans la barre du bas
  text = document.createElementNS(iep.svgsn,"text");
  text.setAttribute("pointer-events", "none");
  text.appendChild(document.createTextNode("Sésamath"));
  text.setAttribute("x", 0);
  text.setAttribute("y", largeurBarre-2);
  text.setAttribute("style","font-family: sans-serif;font-size: 7pt;font-weight:bold;text-anchor: middle; fill: maroon");
  g.appendChild(text);
  this.g = g;
};
/**
 * Fonction montrant ou affichant les nombres de la graduation externe
 * @param {type} bvisible : Nombres affichés si true
 */
iep.rapporteur.prototype.montreGraduations = function(bvisible) {
  this.graduationInterneVisible = true;
  if (this.visible) this.gradint.setAttribute("visibility", bvisible ? "visible" : "hidden");
};
/**
 * Fonction montrant ou affichant les nombres de la graduation interne du rapporteur
 * @param {type} bvisible : : Nombres affichés si true
 */
iep.rapporteur.prototype.montreGraduationsExternes = function(bvisible) {
  this.graduationExterneVisible = true;
  if (this.visible) this.gradext.setAttribute("visibility", bvisible ? "visible" : "hidden");
};
/**
 * Fonction mettant le rapporteur en position visible ou caché suivant la
 * valeur de bvisible
 * @param {boolean} bvisible
 */
iep.rapporteur.prototype.montre = function(bvisible) {
  iep.instrumentAncetre.prototype.montre.call(this,bvisible);
  if (bvisible) {
   this.gradint.setAttribute("visibility", this.graduationInterneVisible ? "visible" : "hidden");
   this.gradext.setAttribute("visibility",this.graduationExterneVisible ? "visible" : "hidden");
 }
  else {
    this.gradint.setAttribute("visibility","hidden");
    this.gradext.setAttribute("visibility","hidden");
  }
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Objet représentant l'équerre de la figure.
 * @constructor
 * @extends iep.instrumentAncetre
 * (this.x,this.y)  sont les coordonnées du sommet de l'équerre
 * this.angle est l'angle que fait l'équerre avec l'horizontale.
 * @param {iep.iepDoc} doc : le document propriétaire de la figure
 */
iep.equerre = function(doc) {
  iep.instrumentAncetre.call(this,doc);
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(this.x,this.y,this.angle);
  doc.svg.appendChild(this.g);
};
iep.equerre.prototype = new iep.instrumentAncetre();
/**
 * Fonction mettannt dans this.g l'élément graphique représentant l'équerre
 * dans le DOM du svg qui contient la figure
 * @returns {g}
 */
iep.equerre.prototype.creeg = function() {
  var g,p,path,path2,text;
  var largeur = 131;
  var hauteur = 223;
  var largeurbas = 31;
  var largeurgauche = 24;
  var largeurint = 61;
  var hauteurint = largeurint*hauteur/largeur;
  var largeurbande = 16; // pour la partie plus claire en bas et à gauche.
  var h = hauteur/largeur*(largeur - largeurbande); // Hauteur basse de la partie plus claire
  var l = largeur/hauteur*(hauteur - largeurbande);
  
  g = document.createElementNS(iep.svgsn, "g");
  // D'abord le pourtour
  p = document.createElementNS(iep.svgsn, "path");
  // Pourtour exterieur
  path = "M 0 0 L " + largeur + " 0 L 0 " + String(-hauteur) + " Z";
  // Pourtout intérieur qui doit être parcouru dans l'autre sens pour le remplissage
  path2 = "M "+ largeurgauche + " " + String(-largeurbas) + " L " + largeurgauche + " " +
          String(-largeurbas - hauteurint) +  " L " + String(largeurgauche + largeurint) +
          " " + String(-largeurbas) + " Z";
  path += path2;
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black;stroke-width:0.75; fill: #c6cbe8; fill-opacity: 0.5");
  g.appendChild(p);
  // On repasse l'intérieur pour laisser une bande plus claire sur les côtés
  p = document.createElementNS(iep.svgsn, "path");
  path = "M " + largeurbande + " " + String(-largeurbande) + " L " + l + " " +
          String(-largeurbande) + " L " + largeurbande + " " + (-h) + " Z";
  path += path2;
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke-width:0; fill: #c6cbe8; fill-opacity: 0.5");
  g.appendChild(p);
  // On rajoute le texte Sesamath dans la barre du bas
  text = document.createElementNS(iep.svgsn,"text");
  text.setAttribute("pointer-events", "none");
  text.appendChild(document.createTextNode("Sésamath"));
  text.setAttribute("x", largeurgauche);
  text.setAttribute("y", -largeurbande/2 - 5);
  text.setAttribute("style","font-family: Arial;font-size: 8pt;font-weight:bold;fill: maroon");
  g.appendChild(text);
  this.g = g;
  return g; // Sert pour la règle équerre
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant le crayon de la figure.
 * (this.x,this.y) sont les coordonnées de la pointe de la mine
 * this.angle est l'angle du corps du crayon par rapport à l'horizontale.
 * @constructor
 * @extends iep.instrumentAncetre
 * @param {iep.iepDoc} doc : le document propriétaire de la figure
 */
iep.crayon = function(doc) {
  iep.instrumentAncetre.call(this,doc);
  this.angle = this.angleInit();
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(this.x,this.y,this.angle);
  doc.svg.appendChild(this.g);
};
iep.crayon.prototype = new iep.instrumentAncetre();

/**
 * extends iep.instruementAncetre
 */
iep.crayon.prototype.angleInit = function() {
  return -40;
}
/**
 * Fonction mettant dans this.g l'élément graphqie représentant le crayon
 * dans le DOM du svg de la figure
 */
iep.crayon.prototype.creeg = function() {
  var g,defs,p,path,path1,lg,stop,circ;
  var lon = 97; // La longueur totale (sans le demi-cercle final)
  var longpointe = 15; // La longueur de la mine
  var demlarg = 4.5; // La demi-largeur du crayon
  var longmine = 3;
  var demlargmine = longmine/longpointe*demlarg;
  
  g = document.createElementNS(iep.svgsn, "g");
  // On crée un gradiant linéaire pour la mine
  // Il est nécessaire d'englober le gradient dans un defs pour pouvoir l'utiliser via url(#grad) plus tard
  defs = document.createElementNS (iep.svgsn, "defs");
  lg = document.createElementNS(iep.svgsn, "linearGradient");
  lg.setAttribute("id","gradpointe");
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","0%");
  stop.setAttribute("style","stop-color: #f00000;");
  lg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","50%");
  stop.setAttribute("style","stop-color: #eeb444;");
  lg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","100%");
  stop.setAttribute("style","stop-color: #f00000");
  lg.appendChild(stop);
  lg.setAttribute("x1","0%");
  lg.setAttribute("y1","0%");
  lg.setAttribute("x2","0%");
  lg.setAttribute("y2","100%");
  defs.appendChild(lg);
  g.appendChild(defs);
  // Un autre gradiant pour le corps du crayon
  defs = document.createElementNS (iep.svgsn, "defs");
  lg = document.createElementNS(iep.svgsn, "linearGradient");
  lg.setAttribute("id","gradcorps");
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","0%");
  stop.setAttribute("style","stop-color: #810216;");
  lg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","25%");
  stop.setAttribute("style","stop-color: #c64f00;");
  lg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","50%");
  stop.setAttribute("style","stop-color: #ab4a36");
  lg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","75%");
  stop.setAttribute("style","stop-color: #c64f00;");
  lg.appendChild(stop);
  stop = document.createElementNS(iep.svgsn, "stop");
  stop.setAttribute("offset","100%");
  stop.setAttribute("style","stop-colr: #810216;");
  lg.appendChild(stop);
  lg.setAttribute("x1","0%");
  lg.setAttribute("y1","0%");
  lg.setAttribute("x2","0%");
  lg.setAttribute("y2","100%");
  defs.appendChild(lg);
  g.appendChild(defs);

  // D'abord la mine
  p = document.createElementNS(iep.svgsn, "path");
  var rx = demlarg/3;
  var ry = rx*3/4;
  path1 = "A" + rx + " " + ry + " -90 0 0 " +
          longpointe + " " + String(rx) + "A" +  rx + " " + ry + " -90 0 1 " +
          + longpointe + " " + String(-rx) + "A" + rx + " " + ry + " -90 0 0 " +
          + longpointe + " " + String(-demlarg);
  path = "M 0 0 L" + longpointe + " " + demlarg + path1 + "Z";
  
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black; stroke-width:0.5; fill: url(#gradpointe); fill-opacity: 1;");
  g.appendChild(p);
  // La pointe noire
  p = document.createElementNS(iep.svgsn, "path");
  path = "M 0 0 L " + longmine + " " + demlargmine + " " + longmine + " " +
          String(-demlargmine) +"Z";
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black; stroke-width:0.5; fill: black; fill-opacity: 1;");  
  g.appendChild(p);
  // Le corps du crayon
  p = document.createElementNS(iep.svgsn, "path");
  path = "M " + longpointe + " " + demlarg + path1 + " L " + lon + " " + String(-demlarg) +
          " L " + lon + " " + String(demlarg) + "Z";
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black; stroke-width:0.5; fill: url(#gradcorps); fill-opacity: 1;");  
  g.appendChild(p);
  // Le cercle bleu du bout
  circ = document.createElementNS(iep.svgsn, "circle");
  circ.setAttribute("r",demlarg);
  circ.setAttribute("cx",lon);
  circ.setAttribute("cy", 0);
  circ.setAttribute("style", "stroke:blue; stroke-width:0.5; fill: blue; fill-opacity: 1;");
  g.appendChild(circ);
  this.g = g;
};

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * @constructor
 * @extends iep.instrumentAncetre
 * Classe représentant la régle-equerre dans la figure InstrumenPoche
 * @param {iep.iepDo} doc : le document propriétaire de la figure
 */
iep.requerre = function(doc) {
  iep.instrumentAncetre.call(this,doc);
  this.x = 200;
  this.y = 400;
  this.angle = 0;
  this.zoomfactor = 1;
  this.abscisse = 0; // L'abscisse au coin de l'équerre sur la règle
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(this.x,this.y,this.angle);
  doc.svg.appendChild(this.g);
};
iep.requerre.prototype = new iep.instrumentAncetre();
/**
 * Fonction mettant dans this.g l'élément graphique représentant la règle-équerre
 * dans le DOM du svg de la figure
 */
iep.requerre.prototype.creeg = function() {
  var g,p,path,pathtrou,line;
  var longd = 172; // Longueur de la partie droite à partir de l'origine
  var longg = 256; // Longueur de la partie gauche à partir de l'origine
  var larg = 57; // La largeur
  var ray = 5; // Rayon de courbure des coins
  var largeurbande = 16; // Largeur des bandes plus claires
  var dectroud = 20;
  var dectroub = 15;
  var raytrou = 6;
  var style = "stroke:#999999;stroke-width:2; fill: #c6cbe8; fill-opacity: 0.5";

  // Ici on ne peut pas faire un rectangle aux bords arrondis si on veut le petit cercle
  // ajouré en bas et à droite
  
  g = document.createElementNS(iep.svgsn, "g");
  p = document.createElementNS(iep.svgsn, "path");
  path = "M 0 0 L " + String(longd - ray) + " 0 A " +
          ray + " " + ray + " 0 0 1 " + String(longd) + " " + ray +
          " L " + String(longd) + " " + String(larg- ray) + " A " +
          ray + " " + ray + " 90 0 1 " + String(longd - ray) + " " + String(larg) +
          " L " + String(-longg+ray) + " " + larg + " A " + ray + " " + ray + " 180 0 1 " + "-" + longg + " " +
          String(larg - ray) + " L " + String(-longg) + " " + ray + " A " + ray + " " + ray +
          " -90 0 1 " + String(-longg+ray) + " 0Z";
  // On rajoute le trou parcouru dans le sens inverse pour qu'il ne soit pas rempli
  // Forcément deux demi-cercles. Un cercle entier ne semble pas possible.
  var ytrou = larg-dectroub; 
  pathtrou = "M" + String(longd-dectroud+raytrou) + " " + ytrou + "A " +
          raytrou + " " + raytrou + " 180 0 0 " + String(longd-dectroud-raytrou) + " " + String(ytrou) +
          "A " + raytrou + " " + raytrou + " 180 0 0 " + String(longd-dectroud+raytrou) + " " + String(ytrou);
  
  path += pathtrou;
  p.setAttribute("d", path);
  p.setAttribute("style", style);
  g.appendChild(p);
  // On rajoute l'intérieur pour les bords soient plus clairs
  p = document.createElementNS(iep.svgsn, "path");
  path = "M" + String(longd) + " " + String(largeurbande) + "L" +
          String(longd) + " " + String(larg- ray) + " A " +
          ray + " " + ray + " 90 0 1 " + String(longd - ray) + " " + String(larg) +
          " L " + String(-longg+ray) + " " + larg + " A " + ray + " " + ray + " 180 0 1 " + "-" + longg + " " +
          String(larg - ray) + " L " + String(-longg) + " " + String(largeurbande) + "Z" + pathtrou;
  p.setAttribute("d", path);
  p.setAttribute("style", style);
  g.appendChild(p);
  // Un trait de rappel pour l'origine
  line = document.createElementNS(iep.svgsn, "line");
  line.setAttribute("x1",0);
  line.setAttribute("y1",8);
  line.setAttribute("x2",0);
  line.setAttribute("y2",0);
  line.setAttribute("style","stroke: black");
  g.appendChild(line);
  this.gequerre = new iep.equerre.prototype.creeg();
  this.gequerre.setAttribute("transform","scale(0.7)");
  g.appendChild(this.gequerre);
  this.setAbs(this.abscisse);
  this.g = g;
};
/**
 * Fonction faisant glisser l'équerre sur la règle jusqu'à l'abscisse abs
 * L'abscisse 0 est représentée par le petit trait sur la règle
 * @param {type} abs
 */
iep.requerre.prototype.setAbs = function(abs) {
  this.abscisse = abs;
  this.gequerre.setAttribute("transform","translate("+abs+",0)");
};
/**
 * Fonction lançant une animation de glissement de l'équerre sur la règle
 * La règle équerre est le seul instrument à voir un mouvement autre que translation et rotation : Le glissement.
 * @param {Float} absfin : l'abscisse de l'équerre à la fin du glissement
 * @param {Float} pix10 : le nombre de pixels dont gilsse l'équerre
 * à chaque dixième de seconde.
 */
iep.requerre.prototype.lanceAnimationGlissement = function(absfin,pix10) {
  this.absfin = absfin;
  this.pix = (absfin >= this.abscisse) ? pix10/4 : -pix10/4; // Dans la version JavaScript on quadruple la fréquence
  this.dist = Math.abs(absfin-this.abscisse);
  var t = this;
  this.timer = setInterval(function(){iep.requerre.actionPourGlissement.call(t)},25);
};
/**
 * Fonction appelée par un timer lors de l'aniamtion de glissement de l'équerre
 * sur la règle
 */
iep.requerre.actionPourGlissement = function() {
  var abs = this.abscisse + this.pix;
  var dis = Math.abs(this.absfin-abs);
  if ((dis > this.dist) || !this.doc.animationEnCours) {
    this.setAbs(this.absfin);
    clearInterval(this.timer);
    this.doc.actionSuivante();
    return;
  }
  else {
    this.dist = dis;
    this.setAbs(abs);
  }
};


/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * @constructor
 * @extends iep.instrumentAncetre
 * Objet compas contenu dans une animation InstrumenPche
 * @param {iep.iepDoc} doc : le document propriétaire
 * la variable this.leve contient true si une action ActionLeverCompas a été
 * précédemment exécutée. Dans ce as c'est un autre objet iep.compasLeve qui sera créé
 * La variable this.bretourne contient true si une action ActionRetourner a été
 * exécutée précdemment.
 * et utilisé dans la figure.
 */
iep.compas = function(doc) {
  iep.instrumentAncetre.call(this,doc);
  this.ecart = 0;
  this.bretourne = false;
  this.leve = false;
  this.lon = 185; // Longueur intérieure des deux branches des compas
  this.longpointe = 18;
  var d = this.lon + this.longpointe;
  this.alp = Math.asin(this.ecart/(2*d))/Math.PI*180; // Le demi-angle que font les deux branches du compas
  this.decbh = 30; // Décalage vers le bas de la partie haute
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(this.x,this.y,this.angle,1);
  // Ajout de d'élément graphique représentnant le compas dans le SVG contenant la figure
  doc.svg.appendChild(this.g);
};
iep.compas.prototype = new iep.instrumentAncetre();
/**
 * Fonction initialisant la position du compas
 */
iep.compas.prototype.initialisePosition = function() {
  iep.instrumentAncetre.prototype.initialisePosition.call(this);
  this.ecart = 0;
  this.positionne();
};
/**
 * Fonction créant l'élément graphique représentant le compas
 * @returns {svgElement}
 */
iep.compas.prototype.creeg = function() {
  var g,li,p,path,circ,text,rect;
  var lon = this.lon; // Longueur intérieure des deux branches des compas
  var longpointe = this.longpointe; // Longueur de la pointe et de la mine
  var ep = 7; // Epaisseur des branches
  var retb = 20; // Retrait du bouton de gauche sur la branche;
  var rayb = 6; // Le rayon des boutons
  var epm = 3.5; // Epaisseur de la mine
  var dlb = 3; // la demi-largeur basse de la partie fixe
  var dlm = 12; // La demi-largeur moyenne de la partie fixe
  var ylm = 24; // L'ordonnée correspondante
  var ylm2 = 35; // Ordonnée de la partie moyenne haute
  var dlh = 6; // La demi-largeur haute de la partie fixe
  var ylh = 50; // L'ordonnée correspondante
  var dltop = 3; // La demi-largeur de la partie supérieure du châpeau
  var ytop = 70; // L'ordonnée correspondante
  g = document.createElementNS(iep.svgsn, "g");
  this.bg = document.createElementNS(iep.svgsn, "g"); // La branche de gauche
  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("x1",0);
  li.setAttribute("y1",0);
  li.setAttribute("x2",0);
  li.setAttribute("y2",-longpointe);
  li.setAttribute("style", "stroke: black; stroke-width:1.5;");
  this.bg.appendChild(li);
  p = document.createElementNS(iep.svgsn, "path");
  path = "M0 " + String(-longpointe) + "L 0 " + String(-longpointe-lon) + " " +
          String(-ep) + " " +  String(-longpointe-lon) + " " + String(-ep) + " " +
          String(-longpointe) + "Z";
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  this.bg.appendChild(p);
  // On rajoute le bouton de gauche
  circ = document.createElementNS(iep.svgsn, "circle");
  circ.setAttribute("cx",-ep/2);
  circ.setAttribute("cy",-longpointe-retb);
  circ.setAttribute("r",rayb);
  circ.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  this.bg.appendChild(circ);
  circ = document.createElementNS(iep.svgsn, "circle");
  circ.setAttribute("cx",-ep/2);
  circ.setAttribute("cy",-longpointe-retb);
  circ.setAttribute("r",2);
  circ.setAttribute("style", "stroke:black;stroke-width:1; fill: silver; fill-opacity: 1");
  this.bg.appendChild(circ);
  g.appendChild(this.bg);
  // La branche de droite
  this.bd = document.createElementNS(iep.svgsn, "g"); // La branche de gauche
  // La mine
  p = document.createElementNS(iep.svgsn, "path");
  path = "M 0 0 L0" + " " + String(-longpointe) + " " + epm + " " + String(-longpointe-epm) + " " +
          epm + " " + String(-epm-3) + "Z";
  p.setAttribute("style", "stroke:black;stroke-width:0.75; fill: blck; fill-opacity: 1");
  p.setAttribute("d", path);
  this.bd.appendChild(p);
  p = document.createElementNS(iep.svgsn, "path");
  path = "M0 " + String(-longpointe) + "L 0 " + String(-longpointe-lon) + " " +
          ep + " " +  String(-longpointe-lon) + " " + ep + " " +
          String(-longpointe) + "Z";
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  this.bd.appendChild(p);
  // Le petit bout de bouton à droite
  p = document.createElementNS(iep.svgsn, "path");
  var rayx = rayb/4*3;
  var rayy = rayb/2;
  path = "M" + ep + " " + String(-longpointe-retb+rayx) + "A " + rayx + " " + rayy + " -90 0 0 " +
       ep + " " + String(-longpointe-retb-rayx) + "Z";
  p.setAttribute("style", "stroke:black;stroke-width:1; fill: silver; fill-opacity: 1");
  p.setAttribute("d", path);
  this.bd.appendChild(p);
  // Ligne en biais sur la branche droite
  var styleli = "stroke: black; stroke-width:1;";
  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("x1",0);
  li.setAttribute("y1",-100);
  li.setAttribute("x2",ep);
  li.setAttribute("y2",-100 + ep);
  li.setAttribute("style", styleli);
  this.bd.appendChild(li);
  // Autres lignes branche de droite
  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("x1",0);
  li.setAttribute("y1",-50);
  li.setAttribute("x2",ep);
  li.setAttribute("y2",-50);
  li.setAttribute("style", styleli);
  this.bd.appendChild(li);
  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("x1",ep/2);
  li.setAttribute("y1",-50);
  li.setAttribute("x2",ep/2);
  li.setAttribute("y2",-73);
  li.setAttribute("style", styleli);
  this.bd.appendChild(li);
  // Le bouton vu de côté
  rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("x",ep);
  rect.setAttribute("y",-68);
  rect.setAttribute("width",4);
  rect.setAttribute("height",12);
  rect.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  this.bd.appendChild(rect);
  g.appendChild(this.bd);
  // La partie fixe du haut
  this.haut = document.createElementNS(iep.svgsn, "g");
  p = document.createElementNS(iep.svgsn, "path");
  path = "M 0 0 L" + dlb + " 0 " + dlm + " " + String(-ylm) + " " + dlm + " " + String(-ylm2) +
          "A" + String(ylh-ylm2) + " " + String(dlm-dlh) + " -90 0 1 " + dlh + " " + String(-ylh) +
          "L" + dltop + " " + String(-ytop) + " " + String(-dltop) + " " + String(-ytop) +
          "L" + String(-dlh) + " " + String(-ylh) + "A" + String(ylh-ylm2) + " " + String(dlm-dlh) +
          " 90 0 1 " + String(-dlm) + " " + String(-ylm2) + " L " +
          String (-dlm) + " " + String(-ylm) + " " + String(-dlb) + " 0Z";
  
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black;stroke-width:0.75; fill: #666666; fill-opacity: 1");
  // this.haut.setAttribute("transform","translate(0,30)");
  this.haut.appendChild(p);
  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("x1",dlh);
  li.setAttribute("y1",-ylh);
  li.setAttribute("x2",-dlh);
  li.setAttribute("y2",-ylh);
  li.setAttribute("style", "stroke: black; stroke-width:1;");
  this.haut.appendChild(li);
  // On ajoute le logo Sesamath
  text = document.createElementNS(iep.svgsn,"text");
  text.setAttribute("pointer-events", "none");
  text.appendChild(document.createTextNode("Sésamath"));
  text.setAttribute("x", 0);
  text.setAttribute("y", 0);
  text.setAttribute("style","font-family: Arial;font-size: 5pt;fill: white");
  text.setAttribute("transform","rotate(-90) translate(4,2.5)");
  this.haut.appendChild(text);

  // On rajoute les deux petits ronds blancs (rivets) à chaque branche
  circ = document.createElementNS(iep.svgsn, "circle");
  circ.setAttribute("cx",-ep+1);
  circ.setAttribute("cy",-this.decbh);
  circ.setAttribute("r",3);
  circ.setAttribute("style", "stroke:black;stroke-width:1; fill: white; fill-opacity: 1");
  this.haut.appendChild(circ);
  circ = document.createElementNS(iep.svgsn, "circle");
  circ.setAttribute("cx",ep-1);
  circ.setAttribute("cy",-this.decbh);
  circ.setAttribute("r",3);
  circ.setAttribute("style", "stroke:black;stroke-width:1; fill: white; fill-opacity: 1");
  this.haut.appendChild(circ);
  
  g.appendChild(this.haut);
  this.g = g;
};
/**
 * Fonction calculant les éléments graphiques du compas et modifiant le svg element
 * en conséquence.
 * @param {Float} x : l'abscisse de la point du compas
 * @param {Float} y : l'ordonnée de la pointe du compas
 * @param {Float} angle : l'angle en degrés que fait le compas avec l'horizontale
 * @param {Float} zoomfactor : le zoom initialisé au daprt à 1 et qui peut être
 * modifié par une action ActionZoomInstrument
 */
iep.compas.prototype.setPosition = function(x,y,angle,zoomfactor) {
  var zoom,scale;
  if (arguments.length >= 4) {
    zoom = zoomfactor;
    this.zoomfactor = zoom;
  }
  else zoom = this.zoomfactor;
  if (this.bretroune) scale = "scale(" + zoom + "," + String(-zoom) + ") ";
  else scale = "scale(" + zoom + ") ";
  this.x = x;
  this.y = y;
  this.angle = angle;
  var d = this.lon + this.longpointe;
  this.alp = Math.asin(this.ecart /(2*d*zoom))/Math.PI*180; // Le demi-angle que font les deux branches du compas
  this.bg.setAttribute("transform", "rotate(" + this.alp + ")");
  this.bd.setAttribute("transform", "translate(" + this.ecart/zoom + ",0) rotate(" + String(-this.alp) + ")");
  var ang = this.alp/180*Math.PI;
  this.haut.setAttribute("transform", "translate(" + String(this.ecart/2/zoom)
    + "," + String((-d)*Math.cos(ang)+this.decbh) + ")");

  this.g. setAttribute("transform",scale + "translate(" +
    String(x/zoom) + "," + String(y/zoom) + ") rotate(" + angle + ")"); 
};
/**
 * Fonction utilisée pour mettre le compas en position retournée par rapport
 * à la position précédente.
 */
iep.compas.prototype.retourne = function() {
  this.bretourne = !this.bretourne;
  this.setPosition(this.x,this.y,this.angle);
};
/**
 * Fonction recalculant les éléments du compas et mettant en conséquence à jour
 * l'élément graphique qui le représente.
 */
iep.compas.prototype.positionne = function() {
  iep.instrumentAncetre.prototype.positionne.call(this);
  if (this.leve && (this.doc.compasLeve != null)) iep.instrumentAncetre.prototype.positionne.call(this.doc.compasLeve);
};
/**
 * Fonction translatant le compas au point de coordonnées (x;y)
 * @param {Float} x : l'abscisse du point de destination
 * @param {Float} y : l'ordonnée du point de destination
 */
iep.compas.prototype.translate = function(x,y) {
  iep.instrumentAncetre.prototype.translate.call(this,x,y);
  if (this.leve && (this.doc.compasLeve != null)) iep.instrumentAncetre.prototype.translate.call(this.doc.compasLeve,x,y);
};
/**
 * Fonction lançant une animation d'écartement de compas pour une action ActionEcaterCompas
 * @param {Float} ecart l la valeur de l'écartement final (en unités de longueur)
 * @param {Float} ec10 : le pas d'incrémentation pour l'écart à chaque dixième de seconde
 */
iep.compas.prototype.lanceAnimationEcartement = function(ecart,ec10) {
  // this.ecart = 0;
  this.ecartfin = parseFloat(ecart);
  // Dans la version JavaScript on pourra rajouter un paramètre donnant le pas
  // d'écartement par dixième de seconde
  this.pasecart = ec10/4;
  var sens = (ecart >= this.ecart) ? 1 : -1; // 1 pour le sens direct svg
  this.pasecart *= sens;
  this.distecart = Math.abs(ecart-this.ecart);
  var t = this;
  this.timer = setInterval(function(){iep.compas.actionPourEcartement.call(t)},25);
}
// On poursuit l'animation tant que la distance entre la position actuelle et la psoition de fin
// est inférieure à la distance précédente
/**
 * Fonction appelée par un timer lors d'une aniamtion d'écartement de compas
 * On poursuit l'animation tant que la distance entre la position actuelle et
 * la position de fin de n réaugmente pas
 */
iep.compas.actionPourEcartement = function() {
  var ec = this.ecart + this.pasecart;
  var dis = Math.abs(ec-this.ecartfin);
  if ((dis > this.distecart) || !this.doc.animationEnCours) {
    this.ecart = this.ecartfin;
    this.setPosition(this.x,this.y,this.angle);
    clearInterval(this.timer);
    this.doc.actionSuivante();    
    return;
  }
  else {
    this.distecart = dis;
    this.ecart = ec;
    this.setPosition(this.x,this.y,this.angle);
  }
};

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * @constructor
 * @extends iep.instrumentAncetre
 * Classe représentant le compas vu du dessus.
 * Attention : Contrairement au compas normal, le compas levé doit être reconstruit à  chaque fois
 * que son écartement est modifié.
 */
iep.compasLeve = function(doc,x,y,angle,ecart) {
  iep.instrumentAncetre.call(this,doc);
  var oldg = (doc.compasLeve == null) ? null : doc.compasLeve.g;
  this.x = x;
  this.y = y;
  this.angle = angle;
  this.ecart = ecart;
  this.creeg();
  this.g.setAttribute("visibility","hidden");
  this.setPosition(x,y,angle);
  if (oldg == null) doc.svg.appendChild(this.g);
  else doc.svg.replaceChild(this.g,oldg);
};
iep.compasLeve.prototype = new iep.instrumentAncetre();
/**
 * Fonction mettant dans this.g l'élément graphique représentant le compas
 * levé dans le DOM du svg contenant la figure.
 */
iep.compasLeve.prototype.creeg = function() {
  var rect,li,p,circ;
  var ep = 7; // Epaisseur des branches. La même que dans compas.
  var longpointe = 8; // Longueur de la pointe et des mines
  var ecart = this.ecart;
  
  var g = document.createElementNS(iep.svgsn, "g");
  if(this.ecart > 2*longpointe) {
    rect = document.createElementNS(iep.svgsn, "rect");
    rect.setAttribute("x", longpointe);
    rect.setAttribute("y", -ep/2);
    rect.setAttribute("width", this.ecart - 2*longpointe);
    rect.setAttribute("height", ep);
    rect.setAttribute("rx", 1);
    rect.setAttribute("ry", 1);
    rect.setAttribute("style", "stroke:black;stroke-width:0.5;fill:silver;fill-opacity:1");
    g.appendChild(rect);
  }
  p = document.createElementNS(iep.svgsn,"path");
  p.setAttribute("d","M0 0 L" + longpointe + " 1 " + longpointe + " -1Z");
  p.setAttribute("style", "stroke:black;stroke-width:0.5;fill:black;fill-opacity:1");
  g.appendChild(p);
  var d = ecart-2*longpointe;
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",longpointe);
  li.setAttribute("y1",0);
  li.setAttribute("x2",longpointe+d/8);
  li.setAttribute("y2",0);
  li.setAttribute("style", "stroke: black; stroke-width:0.5;");
  g.appendChild(li);
  p = document.createElementNS(iep.svgsn,"path");
  var e = String(ecart-longpointe);
  p.setAttribute("d","M" + ecart + " 0 L" + e + " 1.5 " + e + " -1.5Z");
  p.setAttribute("style", "stroke:black;stroke-width:0.5;fill:black;fill-opacity:1");
  g.appendChild(p);
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",e);
  li.setAttribute("y1",0);
  li.setAttribute("x2",String(ecart-longpointe-d/8));
  li.setAttribute("y2",0);
  li.setAttribute("style", "stroke: black; stroke-width:0.5;");
  g.appendChild(li);
  li = document.createElementNS(iep.svgsn,"line");
  var f = String(ecart-longpointe-d/8-2);
  li.setAttribute("x1",f);
  li.setAttribute("y1",-ep/2);
  li.setAttribute("x2",f);
  li.setAttribute("y2",ep/2);
  li.setAttribute("style", "stroke: black; stroke-width:0.5;");
  g.appendChild(li);
  li = document.createElementNS(iep.svgsn,"line");
  f = String(ecart-longpointe-d/3-2);
  li.setAttribute("x1",f);
  li.setAttribute("y1",-ep/2);
  li.setAttribute("x2",f);
  li.setAttribute("y2",ep/2);
  li.setAttribute("style", "stroke: black; stroke-width:0.5;");
  g.appendChild(li);
  // Les boutons vus en perspective
  rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("x", String(longpointe+d/8-5));
  rect.setAttribute("y", String(-ep));
  rect.setAttribute("width", 8);
  rect.setAttribute("height", 3);
  rect.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  g.appendChild(rect);
  rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("x", String(ecart-longpointe-d/8-4));
  rect.setAttribute("y", String(-ep));
  rect.setAttribute("width", 8);
  rect.setAttribute("height", 3);
  rect.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  g.appendChild(rect);

  // Le dessus
  var dl = 12; // Demi-largeur de la partie centrale grise
  var dh = 4; // Demi-hauteur de la partie centrale grise
  p = document.createElementNS(iep.svgsn,"path");
  var path = "M" + String(ecart/2+dl) + " " + String(-dh) + " A " + String(dl) +
          " 2 180 0 0 " + String(ecart/2-dl) + " " + String(-dh) + "A" +
          String(dh) + " 2 90 0 1 " + String(ecart/2-dl) + " " + String(dh) + "A" +
          String(dl) + " 2 0 0 0 " + String(ecart/2+dl) + " " + String(dh) + "A" +
          String(dh) + " 2 -90 0 1 " + String(ecart/2+dl) + " " + String(-dh);
  p.setAttribute("d", path);
  p.setAttribute("style", "stroke:black;stroke-width:0.75; fill: #666666; fill-opacity: 1");
  g.appendChild(p);
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",String(ecart/2+dl));
  li.setAttribute("y1",-ep/2);
  li.setAttribute("x2",String(ecart/2-dl));
  li.setAttribute("y2",ep/2);
  li.setAttribute("style", "stroke: black; stroke-width:0.5;");
  g.appendChild(li);
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",String(ecart/2-dl));
  li.setAttribute("y1",-ep/2);
  li.setAttribute("x2",String(ecart/2+dl));
  li.setAttribute("y2",ep/2);
  li.setAttribute("style", "stroke: black; stroke-width:0.5;");
  g.appendChild(li);
  circ = document.createElementNS(iep.svgsn,"circle");
  circ.setAttribute("cx",String(ecart/2));
  circ.setAttribute("cy",0);
  circ.setAttribute("r",5);
  circ.setAttribute("style", "stroke:black;stroke-width:0.75; fill: #666666; fill-opacity: 1");
  g.appendChild(circ);
  // Les boutons vus en perspective
  rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("x", String(longpointe+d/8-5));
  rect.setAttribute("y", String(-ep));
  rect.setAttribute("width", 7);
  rect.setAttribute("height", 3);
  rect.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  g.appendChild(rect);
  rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("x", String(ecart-longpointe-d/8-4));
  rect.setAttribute("y", String(-ep));
  rect.setAttribute("width", 7);
  rect.setAttribute("height", 3);
  rect.setAttribute("style", "stroke:black;stroke-width:0.75; fill: silver; fill-opacity: 1");
  g.appendChild(rect);
  this.g = g;
};
/**
 * Fonction mettant le svg element représentant le compas levé à la position (x,y)
 * avec un angle avce l'horizontale égal à angle
 * @param {type} x
 * @param {type} y
 * @param {type} angle
 */
iep.compasLeve.prototype.setPosition = function(x,y,angle) {
  this.x = x;
  this.y = y;
  this.angle = angle;
  this.g.setAttribute("transform","translate(" + x + "," + y + ") rotate(" + angle + ")"); 
};


/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Objet ancêtre de tous les objets graphiques
 * Le prototype de chaque descendant de cet objet devra comprendre une fonction creeg qui créera
 * le svg élément associé et l'affectera à this.g
 * @constructor
 * @param {type} doc : Le document propriétaire de l'objet
 * @param {type} id : Le numéro de l'objet dans le document
 */
iep.objetBase = function(doc,id,couleur) {
  this.doc = doc;
  this.id = id;
  this.couleur = couleur;
  this.zoomfactor = 1; // Pour les images
  this.visible = false;
  this.objet = "trait"; // Ne sera redéfini que pour les points, textes, les angles
};
/**
 * Cette fonction est appelée lors de la création d'objets.
 * par défaut elle ne fait rien;
 * Elle sera redéfinie pour les objets qui peuvent être translatés : points, marques de segments, textes, images
 */
iep.objetBase.prototype.positionne = function() {
};
/**
 * Fonction qui réinitialisera la psoition des objets lorsqu'on retrace la figure
 * Ne sera redéfinie que pour les objets dont la position peut être modifiee par translation, rotation ou zoom
 */
iep.objetBase.prototype.initialisePosition = function() {
};
/**
 * Fonction translatant l'objet auux coordonnées (ab)
 * @param {type} a
 * @param {type} b
 */
iep.objetBase.prototype.translate = function(a,b) {
  this.setPosition(a,b,this.angle,this.zoomfactor);
};
/**
 * Fonction donnant au membre angle de l'objet la valeur ang
 * @param {type} ang
 */
iep.objetBase.prototype.tourne = function(ang) {
  this.setPosition(this.x,this.y,ang,this.zoomfactor);
};
/**
 * Fonction donnant au membre zoom de l'objet la valeur rap
 * @param {type} ang
 */
iep.objetBase.prototype.zoom = function(rap) {
  this.zoomfactor = rap;
  this.setPosition(this.x,this.y,this.angle,rap);
}
/**
 * Fonction rendant l'objet visible ou invisible suivant la valeur de bvisible
 * Modifie en consequence la valeur de bvisible
 * @param {type} bvisible
 */
iep.objetBase.prototype.montre = function(bvisible) {
  this.visible = bvisible;
  this.g.setAttribute("visibility", bvisible ? "visible" : "hidden");
};

// Les fonctions suivantes ne serviront que pour les points, marques de segment et affichages de texte
// et d'images
// Ces objets doivent posséder une fonction setPosition(x,y)
/**
 * Fonction ne servant que pour les points, marques de segment, affichages de texte et images
 * Lance une aniamtion de translation de l'objet via un timer et une fonction de callBack
 * Ces objets doivent posséder une fonction setPosition(x,y)
 * @param {Float} xfin : abscisse de la position finale de l'objet
 * @param {type} yfin : ordonnée de la position finale de l'objet
 * @param {type} pix10 : Nombre de pixels pour le déplacement par dixième de seconde
 */
iep.objetBase.prototype.lanceAnimationTranslation = function(xfin,yfin,pix10) {
  // this.g.setAttribute("visibility","visible");
  this.xfin = xfin;
  this.yfin = yfin;
  this.pix = Math.abs(pix10/4); //Dans la version JavaScript on quadruple la fréquence
                                // en divisant le pas par 4 
                                // (en fait par deux car la rapidité semble double de celle annoncée dans le mode d'emploi)
  var v = new iep.vect(this.x,this.y,xfin,yfin);
  this.dist = v.norme(); // La distance entre la position actuelle et la position finale
  // Cas où l'objet est déjà à la bonne position
  if (this.dist === 0) {
    this.translate(this.xfin,this.yfin);
     this.doc.actionSuivante();
    return;    
  }
  this.vect = v.vecteurColineaire(this.pix);
  var t = this;
  this.timer = setInterval(function(){iep.objetBase.actionPourTranslation.call(t)},25);
};
/**
 * Fonction de callBack appelée par un timer lors de l'animation de translation
 * On poursuit l'animation tant que la distance entre la position actuelle et la psoition de fin
 * est inférieure à la distance précédente
 */
iep.objetBase.actionPourTranslation = function() {
  var x = this.x + this.vect.x;
  var y = this.y + this.vect.y;
  var u = new iep.vect(x,y,this.xfin,this.yfin);
  var d = u.norme();
  if ((d > this.dist) || !this.doc.animationEnCours) {
    this.translate(this.xfin,this.yfin);
    clearInterval(this.timer);
    this.doc.actionSuivante();
    return;
  }
  else {
    this.dist = d;
    this.translate(x,y);
  }
};
/**
 * Fonction ne servant que pour les points, marques de segment, affichages de texte et images
 * Lance une aniamtion de rotation de l'objet via un timer et une fonction de callBack
 * Ces objets doivent posséder une fonction setPosition(x,y)
 * @param {Float} angfin : l'angle de fin de l'objet
 * @param {Float} deg10 : le nombre de degrés dont on tourne par dixième de seconde
 */
iep.objetBase.prototype.lanceAnimationRotation = function(angfin,deg10) {
  this.anglefin = angfin;
  this.pasdeg = deg10*2/3; // Dans la version JavaScript on quadruple la fréquence
                                // en divisant le pas par 4 
                                // (en fait on corrige par *2/3 car la rapidité semble double de celle annoncée dans le mode d'emploi)
  var sens = (angfin >= this.angle) ? 1 : -1; // 1 pour le sens direct svg
  this.pasdeg *= sens;
  this.distang = Math.abs(angfin-this.angle);
  var t = this;
  this.timer = setInterval(function(){iep.objetBase.actionPourRotation.call(t)},25);
};
/**
 * Fonction de callBack appelée par un timer lors de l'animation de rotation
 */
iep.objetBase.actionPourRotation = function() {
  var ang = parseFloat(this.angle) + parseFloat(this.pasdeg);
  var dis = Math.abs(ang-this.anglefin);
  if ((dis > this.distang) || !this.doc.animationEnCours) {
    this.tourne(this.anglefin);
    clearInterval(this.timer);
    this.doc.actionSuivante();    
    return;
  }
  else {
    this.distang = dis;
    this.tourne(ang);
  }
};
/**
 * Fonction ne servant que pour les affichages de texte et images
 * Lance une aniamtion de zoom de l'objet via un timer et une fonction de callBack
 * @param {Float} zoomfin : la valeur de fin du zoomfactor de l'objet
 * @param {Float} vitesse : pour la vitesse d'animation
 */
iep.objetBase.prototype.lanceAnimationZoom = function(zoomfin,vitesse) {
  this.zoomfin = zoomfin;
  this.vitesse = parseInt(vitesse);
  this.pas = this.vitesse*0.05*(this.zoomfin - this.zoomfactor); // On augmente
  // par défaut de 5% de vitesse tous les dixièmes de seconde
  this.senspos = (this.pas >=0);
  var t = this;
  this.timer = setInterval(function(){iep.objetBase.actionPourZoom.call(t)},100);
};
/**
 * Fonction de callBack appelée par un timer lors de l'animation de zoom
 */
iep.objetBase.actionPourZoom = function() {
  var z = this.zoomfactor + this.pas;
  if (this.doc.animationEnCours && ((this.senspos && (z < this.zoomfin)) || (!this.senspos && (z>this.zoomfin)))) 
    this.zoom(z);
  else {
    this.zoom(this.zoomfin);
    clearInterval(this.timer);
    this.doc.actionSuivante();    
    return;
  }
};
/**
 * Fonction qui devra être appelée pour remettre à jour le g élément représentant
 * l'objet dans le DOM du svg de la figure
 * Appelé quand on recalcule la figure ou lors des animations
 * Par défaut on reconstruit le svg élément pour mettre à jour l'objet
 * Pour certains objets (point par exemple) ce sera inutile
 * @retursn {undefined}
 */
iep.objetBase.prototype.updateg = function() {
  // var oldg = this.doc.getElement(this.id).g;
  var oldg = this.g;
  this.creeg();
  this.doc.svg.replaceChild(this.g, oldg);
  this.doc.setElement(this.id,this);
  this.g.setAttribute("visibility","visible");
}
/**
 * Fonction qui renverra true lorsque la création de l'objet donne lieu à une animation
 * par exemple pour les segments, droites. Sera donc redéfini dans droiteAncetre
 * Dans ce cas, l'objet devra avoir deux fonctions : lanceAnimation et actionPourAnimation
 * @returns {boolean}
 */
iep.objetBase.prototype.creationAnimee = function() {
  return false;
};
/**
 * Fonction qui devra être redéfinie pour les objets créés avec une animation
 * appelée à la fin d'une animation ou lors de la visualisation immédiate de l'objet
 * lors du click sur une icône stepNext ou stepPrev
 */
iep.objetBase.prototype.finAction = function() {
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe ancêtre de tous les objets de type ligne
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire de l'objet
 * @param {string} id : l'id de l'objet dans le fichier XML de la figure
 * @param {string} couleur : la couleur de l'objet
 * @param {string} epaisseur : l'épaisseur de trait
 * @param {string} opacite : l'opacité du trait
 * @param {string} styleTrait : "tiret" pour avoir des pointillés
 */
iep.objetLigne = function(doc,id,couleur,epaisseur,opacite,styleTrait) {
  iep.objetBase.call(this,doc,id,couleur);
  this.epaisseur = epaisseur;
  this.opacite = (arguments.length >= 5) ? opacite : "100";
  this.styleTrait = (arguments.length >= 6) ? styleTrait : "continu";
  var op = parseFloat(this.opacite/100);
  this.style = "stroke:" + couleur + ";stroke-width:" + epaisseur +
          ";stroke-opacity:" + op + ";stroke-linecap:round;";
  if(this.styleTrait==="tiret") this.style += "stroke-dasharray:3 3;" // Pointillés
};
iep.objetLigne.prototype = new iep.objetBase();/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * La classe représentant l'ancêtre des droites, segments, demi-droites
 * @extends iep.objetBase
 * @constructor
 * @param {type} x1 : Abscisse du premier point
 * @param {type} y1 : Ordonnée du deuième point
 * @param {type} x2 : Abscisse du deuxième point
 * @param {type} y2 : Ordonnée du deuxième point
 * @param {type} couleur : La couleur
 * @param {type} epaisseur : L'épaisseur de trait
 * @param {type} opacite : L'opacité du tracé
 * @param {type} styleTrait : "tiret" pour un trait pointillé
 * @param {boolean} vecteur : Si true une flèche est tracée
 */
iep.droiteAncetre = function(doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait) {
  this.x1 = parseFloat(x1);
  this.y1 = parseFloat(y1);
  this.x2 = parseFloat(x2);
  this.y2 = parseFloat(y2);
  iep.objetLigne.call(this,doc,id,couleur,epaisseur,opacite,styleTrait);
};
iep.droiteAncetre.prototype = new iep.objetLigne();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.droiteAncetre.prototype.creeg = function() {
  var g,li;
  g = document.createElementNS(iep.svgsn,"g");
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",this.x1);
  li.setAttribute("y1",this.y1);
  li.setAttribute("x2",this.x2);
  li.setAttribute("y2",this.y2);
  li.setAttribute("style",this.style);
  g.appendChild(li);
  g.setAttribute("visibility","hidden");
  g.setAttribute("id",this.id);
  this.g = g;
};

iep.droiteAncetre.prototype.creationAnimee = function() {
  return true;
};
iep.droiteAncetre.prototype.lanceAnimation = function(vitesse) {
  this.vitesse = vitesse/4;
  var v = new iep.vect(this.x1,this.y1,this.x2,this.y2);
  this.dist = v.norme(); // La distance entre la position actuelle et la position finale
  if (iep.zero(this.dist)) {
    this.doc.actionSuivante();
    return;
  }
  this.vect = v.vecteurColineaire(this.vitesse*1.8); //*1.8 car dans la réalité c'est plus rapide
  this.xfin = this.x2;
  this.yfin = this.y2;
  this.x2 = this.x1;
  this.y2 = this.y1;
  var cray = this.doc.crayon;
  cray.setPosition(this.x1,this.y1,cray.angle);
  var t = this;
  this.timer = setInterval(function(){iep.droiteAncetre.actionPourAnimation.call(t)},25);
};
iep.droiteAncetre.actionPourAnimation = function(){
  this.x2 +=  this.vect.x;
  this.y2 += this.vect.y;
  var u = new iep.vect(this.x1,this.y1,this.x2,this.y2);
  var d = u.norme();
  var cray = this.doc.crayon;
  if ((d > this.dist) || !this.doc.animationEnCours) {
    this.x2 = this.xfin;
    this.y2 = this.yfin;
    this.finAction();
    this.updateg();
    clearInterval(this.timer);
    this.doc.actionSuivante();
    return;
  }
  else {
    cray.translate(this.x2,this.y2);
    this.updateg();
  }
};
/**
 * extends iep.objetBase.prototype.finAction
 */
iep.droiteAncetre.prototype.finAction = function() {
  this.doc.crayon.translate(this.x2,this.y2);
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant un objet de type point
 * A noter que point possèdera deux champs supplémentaires xcons et ycons
 * qui contiendront les coordonées du point au cours de la construction des autres objets
 * xcons et ycons seront modifiés lors d'une action de translation du point
 * pour que les objets l'utilisant comme cible soient correctement initialisés.
 * @extends iep.objetBase
 * @constructor
 * @param {iepDoc} doc : le document propriétaire
 * @param {string} id : L'id de l'objet
 * @param {string} x : l'abscisse initiale
 * @param {string} y : l'ordonnée initiale
 * @param {string} couleur
 * @param {string} epaisseur
 */
iep.point = function(doc,id,x,y,couleur,epaisseur) {
  this.x = parseFloat(x);
  this.y = parseFloat(y);
  // Les deux variables suivantes servent à rétablir la figure dans son état initial quand on revien au début de la figure
  this.xinit = this.x;
  this.yinit = this.y;
  //
  this.xcons = this.x;
  this.ycons = this.y;
  iep.objetLigne.call(this,doc,id,couleur,epaisseur);
  this.style += "stroke-linecap:round;"; // Des bouts de croix arrondis
  this.nom = null; // Un nom peut être affecté ultérieurement par une action nommer
  this.objet = "point";
};
iep.point.prototype = new iep.objetLigne();
/** @inheritDoc */
iep.point.prototype.initialisePosition = function() {
  this.x = this.xinit;
  this.y = this.yinit;
  this.positionne();
};
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.point.prototype.creeg = function() {
  var g,li;
  var lon = 5; // Longuueur en pixels d'un demi-trait
  g = document.createElementNS(iep.svgsn,"g");
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",-lon);
  li.setAttribute("y1",lon);
  li.setAttribute("x2",lon);
  li.setAttribute("y2",-lon);
  li.setAttribute("style", this.style);
  g.appendChild(li);
  li = document.createElementNS(iep.svgsn,"line");
  li.setAttribute("x1",-lon);
  li.setAttribute("y1",-lon);
  li.setAttribute("x2",lon);
  li.setAttribute("y2",lon);
  li.setAttribute("style", this.style);
  g.appendChild(li);
  g.setAttribute("id",this.id);
  this.g = g;
};
/** @inheritDoc */
iep.point.prototype.positionne = function() {
  this.g.setAttribute("transform","translate(" + this.x + "," + this.y + ")");
};
/** @inheritDoc */
iep.point.prototype.translate = function(x,y) {
  this.x = x;
  this.y = y;
  this.positionne();
  if (this.nom !== null) this.nom.positionne();
};
/** @inheritDoc */
iep.point.prototype.updateg = function() {
  this.setPosition(this.x, this.y);
};
// Nécessaire de redéfinir montre car il peut y avoir un nom attaché au point
iep.point.prototype.montre = function(bvisible) {
  iep.objetBase.prototype.montre.call(this,bvisible);
  if (this.nom !== null) if (this.nom.g) this.nom.montre(bvisible);
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant un segment qui peut être affublé d'une flèche
 * @extends iep.objetBase
 * @constructor
 * @param {Integer} x1 : Abscisse de l'origine
 * @param {integre} y1 : Ordonnée de l'origine
 * @param {Integer} x2 : Abscisse de l'extrémité
 * @param {interger} y2 : Ordonnée de l'extrémité
 * @param {string} couleur : couleur
 * @param {Integer} epaisseur : épaiseur du trait
 * @param {string} opacite : transparence du trait (de 0 à 100)
 * @param {string} styleTrait : Si "tiret" pointillés sinon trait continu
 * @param {boolean} vecteur : Si true on rajoute une flcèce
 */
iep.segment = function(doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait,stylefleche) {
  iep.droiteAncetre.call(this,doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait);
  this.stylefleche = stylefleche;
};
iep.segment.prototype = new iep.droiteAncetre();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 * Si le segment est en fait un vecteur on crée d'abord le svg élément du segment
 * et on lui rajoute une flèche
 */
iep.segment.prototype.creeg = function() {
  iep.droiteAncetre.prototype.creeg.call(this);
  if (this.stylefleche === "vecteur") {
    var g;
    var longFleche = 12;
    var x1 = this.x1;
    var x2 = this.x2;
    var y1 = this.y1;
    var y2 = this.y2;
    var u0 = new iep.vect(x2,y2,x1,y1);
    var u1 = u0.vecteurColineaire(longFleche);
    var u2 = new iep.vect(u1.x*iep.cos30 - u1.y*iep.sin30,
      u1.x*iep.sin30 + u1.y*iep.cos30);
    var u3 = new iep.vect(u1.x*iep.cos30 + u1.y*iep.sin30,
      -u1.x*iep.sin30 + u1.y*iep.cos30);
    var points = String(x2 + u2.x)+ " " + String(y2 + u2.y)+
      "," + x2 + " " + y2 + "," + String(x2 + u3.x)+ " " + String(y2 + u3.y);
    g = document.createElementNS(iep.svgsn,"polyline");
    g.setAttribute("points", points);
    var style = "stroke:" + this.couleur + ";stroke-width:" + this.epaisseur +
          ";stroke-opacity:" + this.opacite + ";fill:none";
    g.setAttribute("style", style);
    this.g.appendChild(g);
  }
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant une droite donnée par les coordonnées de ses deux points
 * @constructor
 * @extends iep.objetBase
 * @param {Float} x1 : Abscisse du premier point
 * @param {Float} y1 : Ordonnée du deuième point
 * @param {Float} x2 : Abscisse du deuxième point
 * @param {Float} y2 : Ordonnée du deuxième point
 * @param {string} couleur : La couleur
 * @param {string} epaisseur : L'épaisseur de trait
 * @param {string} opacite : L'opacité du tracé
 * @param {string} styleTrait : "tiret" pour un trait pointillé
 */
iep.droite = function(doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait) {
  iep.droiteAncetre.call(this,doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait);
};
iep.droite.prototype = new iep.droiteAncetre();

/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.droite.prototype.creeg = function() {
  var g,li;
  this.calcule();
  if (!this.horsFenetre) {
    li = document.createElementNS(iep.svgsn,"line");
    li.setAttribute("x1",this.xext1);
    li.setAttribute("y1",this.yext1);
    li.setAttribute("x2",this.xext2);
    li.setAttribute("y2",this.yext2);
    li.setAttribute("style",this.style);
    this.g = li;
  }
  else this.g = document.createElementNS(iep.svgsn,"g");
  this.g.setAttribute("visibility","hidden");
  this.g.setAttribute("id",this.id);
};
/**
 * Calcul des coordonnées des points d'intersection de la droite avec les bords de la fenêtre
 * Si la droite est hors de la fenêtre, this.horsFenetre est mis à false, sinon true
 * Au retour, (this.xext1, this.yext1) et (this.xext2, this.yext2) sont les coodonnées des points au bord
 * @param {type} svg
 */
iep.droite.prototype.calcule = function() {
  var a,xa,ya,xb,yb,xc,yc,xd,yd,x1,y1,x2,y2;
  var width = parseInt(this.doc.svg.getAttribute("width"));
  var height = parseInt(this.doc.svg.getAttribute("height"));
  x1 = this.x1;
  y1 = this.y1;
  x2 = this.x2;
  y2 = this.y2;
  if ((x1 === x2) && (y1 === y2)) {
    this.horsFenetre = true;
    return;
  }
  this.horsFenetre = false;
  if (x1 === x2) {
    this.xext1 = x1;
    this.yext1 = 0;
    this.xext2 = x1;
    this.yext2 = height;
    if ((x1 < 0) || (x1 > width)) {
      this.horsFenetre = true;
      return;
    }
    this.xext1 = x1;
    this.yext1 = 0;
    this.xext2 = x1;
    this.yext2 = height;
    return;
  }
  if (y1 === y2) {
    this.xext1 = 0;
    this.yext1 = y1;
    this.xext2 = width;
    this.yext2 = y1;
    if ((y1 < 0) || (y1 > height)) {
      this.horsFenetre = true;
      return;
    }
    this.xext1 = 0;
    this.yext1 = y1;
    this.xext2 = width;
    this.yext2 = y1;
    return;
  }
  var indicePoint = 0;
  a = (y2-y1)/(x2-x1);
  xa = 0;
  ya = this.y1 + a*(xa-x1);
  if ((ya >=0) && (ya <= height)) {
    indicePoint++;
    this.xext1 = xa;
    this.yext1 = ya;
  }
  xb = width;
  yb = a*(xb-x1)+y1;
  if ((yb >= 0) && (yb <= height)) {
    indicePoint++;
    if (indicePoint === 1) {
      this.xext1 = xb;
      this.yext1 = yb;
    }
    else {
      this.xext2 = xb;
      this.yext2 = yb;
      return;
    }
  }
  yc = 0;
  xc = (yc-y1)/a+x1;
  if ((xc > 0) && (xc < width)) {
    indicePoint++;
    if (indicePoint === 1) {
      this.xext1 = xc;
      this.yext1 = yc;
    }
    else {
      this.xext2 = xc;
      this.yext2 = yc;
      return;
    }
  }
  yd = height;
  xd = (yd-y1)/a+x1;
  if ((xd > 0) && (xd <= width)) {
    indicePoint++;
    if (indicePoint === 2) {
      this.xext2 = xd;
      this.yext2 = yd;
    }
    else {
      this.horsFenetre = true;
    }  	
  }
  else this.horsFenetre = true;
  if (this.horsFenetre) {
    this.xext1 = 0;
    this.yext1 = ya;
    this.xext2 = xb;
    this.yext2 = yb;
  }
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant une demi-droite dans la figure InstrumenPoche
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc :le document propriétaire de la figure
 * @param {type} id : l'id de l'objet dans le fichier XML
 * @param {Float} x1 : abscisse du premier point
 * @param {Float} y1 : ordonnée du premier point
 * @param {Float} x2 : abscisse du second point
 * @param {Float} y2 : ordonnée du premier point
 * @param {string} couleur : couelur de la demi-droite
 * @param {string} epaisseur : Epaisseur du trait
 * @param {string} opacite : Opacité du trait
 * @param {string} styleTrait : "tiret" pour avoir des pointillés
 */
iep.demiDroite = function(doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait) {
  iep.droite.call(this,doc,id,x1,y1,x2,y2,couleur,epaisseur,opacite,styleTrait);
};
iep.demiDroite.prototype = new iep.droite();
// Inutile de redéfinir creeg()
/**
 * Fonction calculant les coordonnées des points à la limite de la figure
 * (xext1,yext1) et (xext2,yext2) pour le tracé réel
 */
iep.demiDroite.prototype.calcule = function() {
  var u,v;
  iep.droite.prototype.calcule.call(this);
  var vect = new iep.vect(this.x1,this.y1,this.x2,this.y2); // Vceteur directeur de la demi-droite
  // Si la droite est hors-fenêtre, la demi-droite l'est aussi
  if (this.horsFenetre) return;
  // Dans IEP l'origine est toujours dans la fenêtre
  u = new iep.vect(this.x1, this.y1, this.xext1, this.yext1);
  if (u.presqueNul()) {
    v = new iep.vect(this.x1, this.y1, this.xext2, this.yext2);
    if (iep.colineairesMemeSens(v, vect)) {
      this.xext1 = this.x1;
      this.yext1 = this.y1;
    }
    else {
      this.xext2 = this.xext1;
      this.yext2 = this.yext1;
      this.xext1 = this.x1;
      this.yext1 = this.y1;
    }
  }
  else {
    if (iep.colineairesMemeSens(u, vect)) {
      this.xext2 = this.xext1;
      this.yext2 = this.yext1;
      this.xext1 = this.x1;
      this.yext1 = this.y1;
    }
    else {
      this.xext1 = this.x1;
      this.yext1 = this.y1;
    }
  }
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant une ligne continue de la figure
 * @extends iep.objetBase
 * @constructor
 * @param {iepDoc} doc : Le document propriétaire
 * @param {Integer} id : L'id de l'objet dans le fichier XML de la figure
 * @param {string} abs : Une chaîne contenant les abscisses séparées par des virgules
 * @param {string} ord : Une chaîne contenant les ordonnées séparées par des virgules
 * @param{Integer} xdeb : abscisse de début (en fait l'abscisse actuelle du crayon)
 * @param{Integer} ydeb : ordonée de début (en fait l'ordonnée actuelle du crayon)
 * @param {string} couleur : La couleur
 * @param {string} epaisseur : L'épaisseur de trait
 * @param {string} opacite : L'opacité du trait
 * @param {string} styleTrait : tiret pour avoir des pointillés
 */
iep.ligneContinue = function(doc,id,xdeb,ydeb,abs,ord,couleur,epaisseur,opacite,styleTrait) {
  iep.objetLigne.call(this,doc,id,couleur,epaisseur,opacite,styleTrait);
  this.xdeb = xdeb;
  this.ydeb = ydeb;
  this.abs = abs.split(',');
  this.ord = ord.split(',');
  this.objet = "trait";
};
iep.ligneContinue.prototype = new iep.objetLigne();

/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.ligneContinue.prototype.creeg = function() {
  var p,points,i;
  p = document.createElementNS(iep.svgsn,"polyline");
  points = this.xdeb + " " + this.ydeb;
  for (i=0;i<this.abs.length;i++) {
    points += " " + this.abs[i] + " " + this.ord[i];
  }
  p.setAttribute("points",points);
  p.setAttribute("style",this.style+"fill:none;")
  p.setAttribute("visibility","hidden");
  p.setAttribute("id",this.id);
  this.g = p;
};
/** @inheritDoc */
iep.ligneContinue.prototype.creationAnimee = function() {
  return true;
};
/**
 * Fonction lançant l'animation de visualisation de la ligne continue
 * Utilise un timer et une fonction de callBack
 */
iep.ligneContinue.prototype.lanceAnimation = function() {
  this.sauveabs = this.abs;
  this.sauveord = this.ord;
  this.abs = [];
  this.ord = [];
  this.ind = -1; // L'indice dans le tableau des coordonnées
  var t = this;
  this.timer = setInterval(function(){iep.ligneContinue.actionPourAnimation.call(t)},25);
};
/**
 * Fonction de callBack appelée par un timer pour l'animation du tracé
 */
iep.ligneContinue.actionPourAnimation = function(){
  this.ind++;
  var cray = this.doc.crayon;
  if (this.ind >= this.sauveabs.length) {
    this.abs = this.sauveabs;
    this.ord = this.sauveord;
    this.updateg();
    clearInterval(this.timer);
    this.finAction();
    this.doc.actionSuivante();
    return;    
  }
  else {
    var x =this.sauveabs[this.ind];
    var y = this.sauveord[this.ind];
    this.abs.push(x);
    this.ord.push(y);
    this.updateg();    
    cray.setPosition(parseFloat(x),parseFloat(y),cray.angle);
    this.updateg();
  }
};
/**
 * extends iep.objetBase.prototype.finAction
 */
iep.ligneContinue.prototype.finAction = function() {
  var len = this.abs.length;
  this.doc.crayon.translate(parseFloat(this.abs[len-1]),parseFloat(this.ord[len-1]));
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant un polygone de la figure
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {string} id : l'id de l'objet dans le fichier XML de la figure
 * @param {string[]} abs : un tableau contenant les abscisses des sommets
 * @param {string[]} ord : : un tableau contenant les ordonnées des sommets
 * @param {string} couleur : la couleur du polygone
 * @param {Float} epaisseur : l'épaisseur du tracé
 * @param {string} couleurFond : la couelur de onc de l'intérieur du polygone
 * @param {string} opacite : l'opacité du remplissage (de 0 à 100)
 */
iep.polygone = function(doc,id,abs,ord,couleur,epaisseur,couleurFond,opacite) {
  iep.objetBase.call(this,doc,id,couleur);
  this.abs = abs.split(',');
  this.ord = ord.split(',');
  this.sauveabs = this.abs;
  this.sauveord = this.ord;
  this.epaisseur = epaisseur;
  this.opacite = (opacite == null) ? "60" : opacite;
  var op = parseFloat(this.opacite/100);
  if (couleurFond == null) this.couleurFond = this.couleur; else this.couleurFond = couleurFond;
  this.style = "stroke:" + couleur + ";stroke-width:" + epaisseur +
          ";fill:" + this.couleurFond + ";";
  this.style += "fill-opacity:"+ op + ";"
  this.objet = "trait";
};
iep.polygone.prototype = new iep.objetBase();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.polygone.prototype.creeg = function() {
  var p,points,i;
  p = document.createElementNS(iep.svgsn,"polygon");
  points = "";
  for (i = 0;i < this.abs.length;i++) {
    points += " " + this.abs[i] + " " + this.ord[i];
  }
  p.setAttribute("points",points);
  p.setAttribute("style",this.style);
  p.setAttribute("visibility","hidden");
  p.setAttribute("id",this.id);
  this.g = p;
};
/** @inheritDoc */
iep.polygone.prototype.creationAnimee = function() {
  return true;
};
/**
 * Fonction lançant l'animation de visualisation de la ligne continue
 * Utilise un timer et une fonction de callBack
 * Vitesse est le nombre de pixels à parcourir à chaque dixième de seconde
 * @param {type} vitesse
 */
iep.polygone.prototype.lanceAnimation = function(vitesse) {
  // Dans la version flash, la vitesse n'est pas le nombre de pixels par dixième de seconde
  // mais le nombre de pixlels par seconde semble le double
  this.pix = parseFloat(vitesse/2);
  this.abs = [];
  this.abs[0] = this.sauveabs[0];
  this.ord = [];
  this.ord[0] = this.sauveord[0];
  this.ind = 0; // L'indice du sommet en cours dans le tableau des coordonnées
  this.n = 0; // L'indice du pas sur le segment en cours
  var cray = this.doc.crayon;
  var v = new iep.vect(parseFloat(this.sauveabs[0]),parseFloat(this.sauveord[0]),
    parseFloat(this.sauveabs[1]),parseFloat(this.sauveord[1]));
  this.longcote = v.norme(); // Longueur du côté en cours
  this.vect = v.vecteurColineaire(this.pix);
  cray.setPosition(parseFloat(this.abs[0]),parseFloat(this.ord[0]),cray.angle);
  var t = this;
  this.timer = setInterval(function(){iep.polygone.actionPourAnimation.call(t)},25);
};
/**
 * Fonction de callBack appelée par un timer pour l'animation du tracé
 */
iep.polygone.actionPourAnimation = function(){
  var cray = this.doc.crayon;
  var ind = this.ind;
  var x = parseFloat(this.abs[ind]);
  var y = parseFloat(this.ord[ind]);
  this.n++;
  if (this.doc.animationEnCours) {
    if (this.n*this.pix <= this.longcote) {
      var x1 = x + this.n*this.vect.x;
      var y1 = y + this.n*this.vect.y;
      this.abs[ind+1] = String(x1);
      this.ord[ind+1] = String(y1);
      cray.translate(x1,y1);
      this.updateg();
    }
    else {
      this.ind++;
      ind++;
      if (ind === this.sauveabs.length) {
        clearInterval(this.timer);
        this.finAction();
        this.doc.actionSuivante(); 
      }
      else { // On passe au côté suivant
        this.abs[ind] = this.sauveabs[ind];
        this.ord[ind] = this.sauveord[ind];
        cray.translate(parseFloat(this.abs[ind]),parseFloat(this.ord[ind]));
        var v = new iep.vect(parseFloat(this.sauveabs[ind]),parseFloat(this.sauveord[ind]),
          parseFloat(this.sauveabs[ind+1]),parseFloat(this.sauveord[ind+1]));
        this.longcote = v.norme(); // Longueur du côté en cours
        this.vect = v.vecteurColineaire(this.pix);
        this.n = 0;
      }
    }
  }
  else {
    clearInterval(this.timer);
    this.finAction();
    this.doc.actionSuivante();
  }
};
/**
 * extends iep.objetBase.prototype.finAction
 */
iep.polygone.prototype.finAction = function() {
  this.abs = this.sauveabs;
  this.ord = this.sauveord;
  var len = this.abs.length;
  this.updateg();
  this.doc.crayon.translate(parseFloat(this.abs[len-1]),parseFloat(this.ord[len-1]));
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant une marque d'angle sur la figure
 * @extends iep.objetBase
 * @constructor
 * @param {Float} x  : abscisse du centre
 * @param {Float} y : ordonnée du centre
 * @param {Float} ray : le rayon
 * @param {Float} ang1 : angle de départ (en degrés)
 * @param {Float} ang2 : angle de fin (en degrés)
 * @param {string} couleur : la couleur
 * @param {string} epaisseur : épaisseur du trait
 * @param {string} opacite : chaine de caractère pour l'opacité (0 à 100)
 * @param {string} motif ; chaîne de caractères. Si elle commence par plein la marque doit être remplie.
 * Si elle finit par un, deux ou trois slashes, elle a des traits de marque
 * d'angle (autant que de /)
 */
iep.angle = function(doc,id,x,y,ray,ang1,ang2,couleur,epaisseur,opacite,motif) {
  this.x = parseFloat(x);
  this.y = parseFloat(y);
  this.ray = parseFloat(ray);
  this.ang1 = parseFloat(ang1);
  this.ang2 = parseFloat(ang2);
  this.motif = (motif == null) ? "simple" : motif;
  // Pas d'opacité pour le trait
  iep.objetLigne.call(this,doc,id,couleur,epaisseur,1);
  this.opaciteRemplissage = (opacite == null) ? 0.7 : parseFloat(opacite)/100;
  this.objet = "angle";
};
iep.angle.prototype = new iep.objetLigne();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.angle.prototype.creeg = function() {
  var direct;
  var path;
  var demilong = 5; // Demi-longueur des traits de marque
  var motif = this.motif;
  var ray = this.ray;
  var a1 = this.ang1;
  var a2 = this.ang2;
  var d = a2-a1;
  var ang = iep.mesurePrincDeg(d);
  // var direct = (ang>0) && !iep.zeroAngle(d+180); // Direct dans le sens du repère du svg
  if (iep.zeroAngle(Math.abs(d)-180)) direct = (this.ang1<=this.ang2);
  else direct = ang>0;
  var u1 = (new iep.vect(this.x,this.y,this.x+Math.cos(a1*iep.convDegRad),this.y+Math.sin(a1*iep.convDegRad))).
          vecteurColineaire(ray);
  var u2 = (new iep.vect(this.x,this.y,this.x+Math.cos(a2*iep.convDegRad),this.y+Math.sin(a2*iep.convDegRad))).
          vecteurColineaire(ray);
  // Début de l'arc
  var xd = this.x + u1.x;
  var yd = this.y + u1.y;
  // Fin de l'arc
  var xf = this.x + u2.x;
  var yf = this.y + u2.y;
  var g = document.createElementNS(iep.svgsn,"g");
  var p = document.createElementNS(iep.svgsn,"path");
  var remplis = (motif.indexOf("plein")!==-1) ? this.couleur : "none";
  if (remplis !== "none")
    path = "M" + this.x + " " + this.y + "L" + xd + " " + yd + "A" + ray + " " + ray + " " +
            this.ang1 + " 0 " + (direct ? "1 " : "0 ") + xf + " " + yf + " Z";
    else
    path = "M" + xd + " " + yd + "A" + ray + " " + ray + " " +
            this.ang1 + " 0 " + (direct ? "1 " : "0 ") + xf + " " + yf ;
      
  p.setAttribute("d", path);
  var style1 = "stroke:" + this.couleur + ";stroke-width:" + this.epaisseur + ";";
  var style = style1 + "fill:" + remplis + ";fill-opacity:" + this.opaciteRemplissage + ";"
  p.setAttribute("style", style);
  g.appendChild(p);
  var rond = (motif.indexOf("O") !== -1);
  var uc = u1.tourne(ang/2); // Vecteur du centre vers le milieu de l'arc
  var xc = this.x+uc.x; // Abscisse du centre de l'arc
  var yc = this.y+uc.y; // Ordonnée du centre de l'arc
  if (rond) {
    var circ = document.createElementNS(iep.svgsn,"circle");
    circ.setAttribute("r",5); // Rayon de 5 pixels pour le petir rond de marque
    circ.setAttribute("cx",xc);
    circ.setAttribute("cy",yc);
    circ.setAttribute("style",style1 + "fill:none;");
    g.appendChild(circ);
  }
  // On regarde s'il y a un, deux ou trois arcs de cercle à tracer
  var n = (motif.indexOf("triple")!==-1) ? 2 :((motif.indexOf("double")!==-1) ? 1 : 0);
  for (var i = 1; i <= n; i++) {
    p = document.createElementNS(iep.svgsn,"path");
    var r = ray-i*5;
    var u3 = u1.vecteurColineaire(r);
    var u4 = u2.vecteurColineaire(r);
    xd = this.x + u3.x;
    yd = this.y + u3.y;
    xf = this.x + u4.x;
    yf = this.y + u4.y;
    path = "M" + xd + " " + yd + "A" + r + " " + r + " " +
          this.ang1 + " 0 " + (direct ? "1 " : "0 ") + xf + " " + yf;
    p.setAttribute("d",path);
    p.setAttribute("style",style1 + "fill:none;");
    g.appendChild(p);
  }
  // On rajoute les éventuels traits (de 1 à 3)
  var nbtraits = (motif.indexOf("///")!==-1) ? 3 : ((motif.indexOf("//")!==-1) ? 2 :((motif.indexOf("/")!==-1) ? 1 : 0));
  if (nbtraits === 0) {
    this.g = g;
    return;
  }
  var n1 = n+1;
  var v = uc.vecteurColineaire(1); // Vecteur de longueur 1
  switch (nbtraits) {
    case 1 :
      var xmilieu = this.x + v.x*ray;
      var ymilieu = this.y + v.y*ray;
      var line = document.createElementNS(iep.svgsn,"line");
      line.setAttribute("x1",xmilieu - v.x*demilong*n1);
      line.setAttribute("y1",ymilieu - v.y*demilong*n1);
      line.setAttribute("x2",xmilieu + v.x*demilong);
      line.setAttribute("y2",ymilieu + v.y*demilong);
      line.setAttribute("style", style);
      g.appendChild(line);
    break;
  case 2 :
    var ang2 = ang/6;
    var ang3 = 6;
    if (Math.abs(ang2) >= Math.abs(ang3)) ang2 = ang3;
    var v2 = v.tourne(ang2);
    var xc1 = this.x + v2.x*ray;
    var yc1 = this.y + v2.y*ray;			
    line = document.createElementNS(iep.svgsn,"line");
    line.setAttribute("x1",xc1 - v.x*demilong*n1);
    line.setAttribute("y1",yc1 - v.y*demilong*n1);
    line.setAttribute("x2",xc1 + v.x*demilong);
    line.setAttribute("y2",yc1 + v.y*demilong);
    line.setAttribute("style", style);
    g.appendChild(line);
    v2 = v.tourne(-ang2);
    xc1 = this.x + v2.x*ray;
    yc1 = this.y + v2.y*ray;			
    line = document.createElementNS(iep.svgsn,"line");
    line.setAttribute("x1",xc1 - v.x*demilong*n1);
    line.setAttribute("y1",yc1 - v.y*demilong*n1);
    line.setAttribute("x2",xc1 + v.x*demilong);
    line.setAttribute("y2",yc1 + v.y*demilong);
    line.setAttribute("style", style);
    g.appendChild(line);
    break;
  case 3 :
    xmilieu = this.x + v.x*ray;
    ymilieu = this.y + v.y*ray;    
    line = document.createElementNS(iep.svgsn,"line");
    line.setAttribute("x1",xmilieu - v.x*demilong*n1);
    line.setAttribute("y1",ymilieu - v.y*demilong*n1);
    line.setAttribute("x2",xmilieu + v.x*demilong);
    line.setAttribute("y2",ymilieu + v.y*demilong);
    line.setAttribute("style", style);
    g.appendChild(line);
    ang2 = ang/4;
    ang3 = 9;
    if (Math.abs(ang2) >= Math.abs(ang3)) ang2 = ang3;
    v2 = v.tourne(ang2);
    xc1 = this.x + v2.x*ray;
    yc1 = this.y + v2.y*ray;			
    line = document.createElementNS(iep.svgsn,"line");
    line.setAttribute("x1",xc1 - v.x*demilong*n1);
    line.setAttribute("y1",yc1 - v.y*demilong*n1);
    line.setAttribute("x2",xc1 + v.x*demilong);
    line.setAttribute("y2",yc1 + v.y*demilong);
    line.setAttribute("style", style);
    g.appendChild(line);
    v2 = v.tourne(-ang2);
    xc1 = this.x + v2.x*ray;
    yc1 = this.y + v2.y*ray;			
    line = document.createElementNS(iep.svgsn,"line");
    line.setAttribute("x1",xc1 - v.x*demilong*n1);
    line.setAttribute("y1",yc1 - v.y*demilong*n1);
    line.setAttribute("x2",xc1 + v.x*demilong);
    line.setAttribute("y2",yc1 + v.y*demilong);
    line.setAttribute("style", style);
    g.appendChild(line);
    break;
  }
  this.g = g;        
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant une marque d'angle droit de la figure
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : le document propiétaire de la figure
 * @param {type} id : l'ide de l'objet dans le xml de la figure
 * @param {Float} xsommet : absisse du sommet de l'angle droit
 * @param {Float} ysommet : ordonnée du sommet de l'angle droit
 * @param {Float} xinter : abscisse du sommet de la marque d'angle
 * @param {type} yinter : ordonnée du sommet de la marque d'angle
 * @param {type} couleur : couleur de la marque
 * @param {type} epaisseur
 */
iep.angleDroit = function(doc,id,xsommet,ysommet,xinter,yinter,couleur,epaisseur) {
  this.xsommet = parseFloat(xsommet);
  this.ysommet = parseFloat(ysommet);
  this.xinter = parseFloat(xinter);
  this.yinter = parseFloat(yinter);
  iep.objetLigne.call(this,doc,id,couleur,epaisseur,100);
  this.objet = "angle_droit";
};
iep.angleDroit.prototype = new iep.objetLigne();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.angleDroit.prototype.creeg = function() {
  var v1 = new iep.vect(this.xsommet,this.ysommet,this.xinter,this.yinter);
  var n = v1.norme();
  v1 = v1.vecteurColineaire(n/Math.sqrt(2));
  v1 = v1.tourne(45);
  var v2 = v1.tourne(-90);
  var p = document.createElementNS(iep.svgsn,"polyline");
  var points = String(this.xsommet + v1.x) + " " + String(this.ysommet + v1.y) + " " +
    this.xsommet + " " + this.ysommet + " " + String(this.xsommet + v2.x) + " " + String(this.ysommet + v2.y);
  p.setAttribute("points",points);
  p.setAttribute("style",this.style + "fill:none;");
  this.g = p;
};
 /* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe arc de cercle (servant aussi à tracer les cercles)
 * extends iep.objetBase
 * @constructor
 * @param {type} x : abscisse du centre
 * @param {type} y : Ordonnée du centre
 * @param {type} ray : Le rayon
 * @param {type} debut : L'angle de début de tracé
 * @param {type} fin : L'angle de fin de tracé
 * @param {type} couleur : La couleur
 * @param {type} epaisseur : L'épaisseur du trait
 * @param {type} opacite : L'opacité du tracé
 * @param {type} styleTrait : "tiret" pour avoir des pointillés
 * Si debut < angle, l'arc est tracé dans le sens des ailluilles d'une montre, sinon dans le sens inverse
 */
// Important : l'objet graphque (g element) sera initialisé avec comme centre l'origine
iep.arc = function(doc,id,x,y,ray,debut,fin,couleur,epaisseur,opacite,styleTrait) {
  this.x = parseFloat(x);
  this.y = parseFloat(y);
  this.ray = parseFloat(ray);
  this.debut = parseFloat(debut);
  this.fin = parseFloat(fin);
  iep.objetLigne.call(this,doc,id,couleur,epaisseur,opacite,styleTrait);
};
iep.arc.prototype = new iep.objetLigne();

iep.arc.prototype.creeg = function() {
  var deb = this.debut;
  var fin = this.fin;
  var ray = this.ray;
  var sens = (deb < fin) ? "1" : "0"; // 1 pour le sens des aiguilles d'une montre (direct pour le SVG)
  var ecart = Math.abs(fin-deb);
  var g = document.createElementNS(iep.svgsn,"g");
  if (ecart >= 359.9) { // Cas d'un cercle ou presque
    var circ = document.createElementNS(iep.svgsn,"circle");
    circ.setAttribute("r",ray);
    circ.setAttribute("cx",0);
    circ.setAttribute("cy",0);
    circ.setAttribute("style",this.style + ";fill:none;");
    g.appendChild(circ);
  }
  else {
    var p = document.createElementNS(iep.svgsn,"path");
    var u1 = new iep.vect(Math.cos(deb*iep.convDegRad)*ray,Math.sin(deb*iep.convDegRad)*ray);
    var u2 = new iep.vect(Math.cos(fin*iep.convDegRad)*ray,Math.sin(fin*iep.convDegRad)*ray);
    var xdeb = u1.x;
    var ydeb = u1.y;
    var xfin = u2.x;
    var yfin = u2.y;
    var path = "M" + xdeb + " " + ydeb + "A" + ray + " " + ray + " " + deb + " " +
            ((ecart>180) ? "1" : "0") + " " + sens + " " +xfin + " " + yfin;
    p.setAttribute("d",path);
    p.setAttribute("style",this.style + ";fill:none");
    g.appendChild(p);
  }
  g.setAttribute("transform","translate(" + this.x + "," + this.y + ")");
  g.setAttribute("id",this.id);
  g.setAttribute("visibility","hidden");
  this.g = g;
};

iep.arc.prototype.creationAnimee = function() {
  return true;
};
iep.arc.prototype.lanceAnimation = function(vitesse) {
  var compas = this.doc.compas;
  if (this.doc.compasLeve == null) this.doc.compasLeve = new iep.compasLeve(this.doc,compas.x,compas.y,
    compas.angle,compas.ecart);
  var compasLeve = this.doc.compasLeve;
  this.ray = this.doc.compas.ecart; // Le rayon ne peut etre connu qu'au mancement de l'animation
  this.angfin = this.fin;
  this.pasdeg = vitesse/2; // Dans la version JavaScript on quadruple la fréquence mais on la double par rapport aux autres animations
  var sens = (this.fin >= this.debut) ? 1 : -1; // 1 pour le sens direct svg
  this.pasdeg *= sens;
  this.distang = Math.abs(this.fin-this.debut);
  this.fin = this.debut;
  compas.setPosition(this.x,this.y,this.debut);
  compasLeve.setPosition(this.x,this.y,this.debut);
  var t = this;
  this.timer = setInterval(function(){iep.arc.actionPourAnimation.call(t)},25);
};
iep.arc.actionPourAnimation = function(){
  var compas = this.doc.compas;
  var compasLeve = this.doc.compasLeve;
  this.fin += this.pasdeg;
  var dis = Math.abs(this.angfin-this.fin);
  if ((dis > this.distang) || !this.doc.animationEnCours) {
    this.fin = this.angfin;
    this.updateg();
    // compas.setPosition(this.x,this.y,this.fin);
    this.finAction();
    clearInterval(this.timer);
    this.doc.actionSuivante();    
    return;
  }
  else {
    this.distang = dis;
    this.updateg();
    compas.setPosition(this.x,this.y,this.fin);
    if (compasLeve !== null) compasLeve.setPosition(this.x,this.y,this.fin);
  }
};
/**
 * @inheri
 */
iep.arc.prototype.finAction = function() {
   this.doc.compas.setPosition(this.x,this.y,this.fin);
   if (this.doc.compasLeve !== null) this.doc.compasLeve.setPosition(this.x,this.y,this.fin);
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant une image incluse dans la figure InstrumenPoche
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {string} id : id de l'objet créé dans le fichier XML de la figure
 * @param {string} url : l'url de la figure
 */
iep.image = function(doc,id,url) {
  this.doc =doc;
  this.id = id;
  this.url = url;
  this.x = 0;
  this.y = 0;
  this.angle = 0;
  this.objet = "image";
  this.zoomfactor = 1;
};
iep.image.prototype = new iep.objetBase();
/** @inheritDoc */
iep.image.prototype.initialisePosition = function() {
  this.x = 0;
  this.y = 0;
  this.angle = 0;
  this.zoomfactor = 1;
  this.positionne();
};
/**
 * Fonction appelée dans iepDoc.creeActions
 * crée une image avec l'url url de façon à récupérer ses dimensions
 * Lorsque l'image est chargée, une fonction de callBack crée l'élément graphique
 * image dans le DOM du svg contenant la igure
 */
iep.image.prototype.prepareAction = function() {
  var img = new Image();
  img.owner = this;
  this.action = new iep.actionCreation(this.doc,this.id,this);
  this.action.isReady = false;
  this.doc.ajouteAction(this.action);
  img.onload = function() {
    var own = this.owner;
    own.width = this.width;
    own.height = this.height;
    own.action.isReady = true;
    own.doc.waitForReadyState();
  }
  img.src = this.url; // Provoque le chargement de l'image
}
/**
 * Fonction mettant dans this.g l'élément graphique svg représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.image.prototype.creeg = function(ind) {
  var image = document.createElementNS(iep.svgsn,"image");
  image.setAttribute("width",this.width);
  image.setAttribute("height",this.height);
  image.setAttributeNS('http://www.w3.org/1999/xlink', 'href', this.url);
  image.setAttribute("x",this.x);
  image.setAttribute("y",this.y);
  image.setAttribute("visibility","hidden");
  this.g = image;
  // image.setAttribute("id",this.id);
  this.setPosition(this.x,this.y,this.angle,this.zoomfactor);
  this.doc.svg.appendChild(image);
};
/**
 * Fonction mettant l'image aux coordonnées (x,y) avec un angle angle et
 * un coefficient de zoom zoomfactor et modifiant la position du svg element qui
 * le représente dans le DOM du svg.
 * @param {float} x
 * @param {float} y
 * @param {float} angle
 * @param {integer} zoomfactor
 * @returns {undefined}
 */

iep.image.prototype.setPosition = function(x,y,angle,zoomfactor) {
  this.x = x;
  this.y = y;
  this.angle = angle; 
  this.zoomfactor = zoomfactor;
  this.g.setAttribute("transform","scale(" + zoomfactor + ") translate(" +
    String(x/zoomfactor) + "," + String(y/zoomfactor) + ") rotate(" + angle + ")");
};
/**
 * Fonction mettant à jour l'élément graphique image dans le DOM du svg de la figure
 */
iep.image.prototype.positionne = function() {
  this.setPosition(this.x, this.y, this.angle,this.zoomfactor);
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Objet représentant une marque de segment
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : Le document propriétaire
 * @param {string} id : L'id de l'objet
 * @param {string} x : L'abscisse
 * @param {string} y : L'ordonnée
 * @param {string} couleur : La couleur de tracé
 * @param {string} epaisseur : L'épaisseur de tracé
 * @param {string} motif : Donne le motif de la marque
 */
iep.marqueSegment = function(doc,id,x,y,couleur,epaisseur,motif) {
  this.x = parseFloat(x);
  this.y = parseFloat(y);
  this.motif = motif;
  iep.objetLigne.call(this,doc,id,couleur,epaisseur,100);
  this.objet = "longueur";
};
iep.marqueSegment.prototype = new iep.objetLigne();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.marqueSegment.prototype.creeg = function() {
  var g,li,k;
  var demlong = 5;
  var ec = 5; // demi écart horizontal entre deux traits
  var dec = ec/2;
  var diam = 5; // Diamètre du rond dans le cas où style == O
  switch(this.motif) {
    case "/" :
      g = document.createElementNS(iep.svgsn,"line");
      g.setAttribute("x1",demlong);
      g.setAttribute("y1",-demlong);
      g.setAttribute("x2",-demlong);
      g.setAttribute("y2",demlong);
      g.setAttribute("style",this.style);
      break;
    case "\\" :
      g = document.createElementNS(iep.svgsn,"line");
      g.setAttribute("x1",-demlong);
      g.setAttribute("y1",-demlong);
      g.setAttribute("x2",demlong);
      g.setAttribute("y2",demlong);
      g.setAttribute("style",this.style);
      break;
    case "//":
      g = document.createElementNS(iep.svgsn,"g");
      for (k=-1; k<=1;k+=2) {
        li = document.createElementNS(iep.svgsn,"line");
        li.setAttribute("x1",k*dec+demlong);
        li.setAttribute("y1",-demlong);
        li.setAttribute("x2",k*dec-demlong);
        li.setAttribute("y2",demlong);
        li.setAttribute("style",this.style);
        g.appendChild(li);
      }
      break;
    case "///":
      g = document.createElementNS(iep.svgsn,"g");
      for (k=-1; k<=1;k++) {
        li = document.createElementNS(iep.svgsn,"line");
        li.setAttribute("x1",k*ec+demlong);
        li.setAttribute("y1",-demlong);
        li.setAttribute("x2",k*ec-demlong);
        li.setAttribute("y2",demlong);
        li.setAttribute("style",this.style);
        g.appendChild(li);
      }
      break;
    case "\\\\":
      g = document.createElementNS(iep.svgsn,"g");
      for (k=-1; k<=1;k+=2) {
        li = document.createElementNS(iep.svgsn,"line");
        li.setAttribute("x1",k*dec-demlong);
        li.setAttribute("y1",-demlong);
        li.setAttribute("x2",k*dec+demlong);
        li.setAttribute("y2",demlong);
        li.setAttribute("style",this.style);
        g.appendChild(li);
      }
      break;
    case "\\\\\\":
      g = document.createElementNS(iep.svgsn,"g");
      for (k=-1; k<=1;k++) {
        li = document.createElementNS(iep.svgsn,"line");
        li.setAttribute("x1",k*ec-demlong);
        li.setAttribute("y1",-demlong);
        li.setAttribute("x2",k*ec+demlong);
        li.setAttribute("y2",demlong);
        li.setAttribute("style",this.style);
        g.appendChild(li);
      }
      break;
    case "X": // Croix
      g = document.createElementNS(iep.svgsn,"g");
      li = document.createElementNS(iep.svgsn,"line");
      li.setAttribute("x1",-demlong);
      li.setAttribute("y1",-demlong);
      li.setAttribute("x2",demlong);
      li.setAttribute("y2",demlong);
      li.setAttribute("style",this.style);
      g.appendChild(li);
      li = document.createElementNS(iep.svgsn,"line");
      li.setAttribute("x1",demlong);
      li.setAttribute("y1",-demlong);
      li.setAttribute("x2",-demlong);
      li.setAttribute("y2",demlong);
      li.setAttribute("style",this.style);
      g.appendChild(li);
      break;
    case "O": // Rond
      g = document.createElementNS(iep.svgsn,"circle");
      g.setAttribute("cx",0);
      g.setAttribute("cy",0);
      g.setAttribute("r",diam);
      g.setAttribute("style",this.style+"fill:none;");
      break;
    default :
      g = document.createElementNS(iep.svgsn,"g"); // Vide en cas d'erreur

  }
  this.g = g;
};
/** @inheritDoc */
iep.marqueSegment.prototype.positionne = function() {
  this.g.setAttribute("transform","translate(" + this.x + "," + this.y + ")");  
}
/** @inheritDoc */
iep.marqueSegment.prototype.translate = function(x,y) {
  this.x = x;
  this.y = y;
  this.positionne();
};

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant un texte.
 * L'écriture dans le texte se fait via des actionEcrireTexte
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : Le docmument propriétaire
 * @param {string} id : L'id du texte
 * @param {string} x : L'asbsisse d'affichage
 * @param {string} y : L'ordonnée d'affichage
 */
iep.texte = function(doc,id,x,y) {
  iep.objetBase.call(this,doc,id,"black");
  this.x = parseFloat(x);
  this.y = parseFloat(y);
  this.taille = 20;
  this.angle = 0; // Possibilité de tourner un etxte (pas donnée avec la version Flash)
  this.texte = "";
  this.objet = "texte";
};
iep.texte.prototype = new iep.objetBase();

// Attention : POur un affichage de texte, il faut décaler vers le bas de la hauteur de la police pour
// garder la compatibilité avec le flash
iep.texte.prototype.positionne = function() {
  // var y = this.y + parseFloat(this.taille);
  // this.g.setAttribute("transform","translate(" + this.x + "," + y + ")");
  this.setPosition(this.x, this.y, this.angle, this.zoomfactor);
}
iep.texte.prototype.translate = function(x,y) {
  this.x = x;
  this.y = y;
  this.positionne();
};
/** @inheritDoc */
iep.texte.prototype.updateg = function(g) {
  var oldg = this.g;
  this.doc.svg.replaceChild(g, oldg);
  this.g = g;
  g.setAttribute("visibility","visible");
};
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.texte.prototype.creeg = function() {
  this.g = document.createElementNS(iep.svgsn,"g");
};
/**
 * Fonction mettant le texte aux coordonnées (x,y) avec un angle angle et
 * un coefficient de zoom zoomfactor et modifiant la spoition du svg element qui
 * le représente dans le DOM du svg.
 * @param {float} x
 * @param {float} y
 * @param {float} angle
 * @param {integer} zoomfactor
 * @returns {undefined}
 */
iep.texte.prototype.setPosition = function(x,y,angle,zoomfactor) {
  this.x = x;
  this.y = y;
  this.angle = angle; 
  this.zoomfactor = zoomfactor;
  var y2 = y + parseFloat(this.taille);
  this.g.setAttribute("transform","scale(" + zoomfactor + ") translate(" +
    String(x/zoomfactor) + "," + String(y2/zoomfactor) + ") rotate(" + angle + ")");
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Objet servant à nommer un point
 * @extends iep.objetBase
 * @constructor
 * @param {iepDoc} doc : le document propriétaire
 * @param {string} id : l'id du point auquel le nom est attribué
 * @param {string} dx : le décalage en abscisses du nom par rapport au point (haut-gauche de la matrice du nom)
 * @param {string} dy : le décalage en ordonnées du nom par rapport au point (haut-gauche de la matrice du nom)
 * @param {string} couleur : la couleur du nom
 */
iep.nomPoint = function(doc,id,dx,dy,nom,couleur) {
  iep.objetBase.call(this,doc,id,couleur);
  this.dx = (dx == null) ? 2 : parseFloat(dx);
  this.dy = (dy == null) ? 4 : parseFloat(dy);
  this.nom = iep.remplaceAccentsHtml(iep.remplaceBalises(iep.remplaceCarSpe(nom)));
  this.taille = 20;
  this.point = this.doc.getElement(this.id,"point");
  this.objet = "point";
  this.nom = this.nom.replace(/\*/g,"×");
  if (iep.necessiteLatex(nom)) {
    this.nom = "$" + this.traiteMaths(this.nom) + "$";
    this.estLatex = true;
  }
  else {
    this.nom = iep.traiteAccents(this.nom);
    this.estLatex = false;
  }
};
iep.nomPoint.prototype = new iep.objetBase();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
/*
iep.nomPoint.prototype.creeg = function() {
  this.nomCree = true; // Pour pouvoir tester lors du pas à pas si l'action de nommer le point a déjà été exécutée
  var nom = this.nom;
  var text = document.createElementNS(iep.svgsn,"text");
  text.setAttribute("x", 0);
  text.setAttribute("y", 0);
  text.setAttribute("style", "text-anchor : left;"+"fill:"+this.couleur+";"+"font-size:"+this.taille+"px;");
  // On recherche un éventuel indice
  for (var i = 0; i < nom.length; i++) {
    if (iep.chiffre(nom.charAt(i))) break;
  }
  var deb = nom.substring(0,i);
  var span1 = document.createElementNS(iep.svgsn,"tspan");
  span1.setAttribute("pointer-events", "none");
  var cont1 = document.createTextNode(deb);
  span1.appendChild(cont1);
  text.appendChild(span1);
  if (i < nom.length) {
    var fin = nom.substring(i);
    var span2 = document.createElementNS(iep.svgsn,"tspan");
    span2.setAttribute("pointer-events", "none");
    span2.setAttribute("font-size", Math.floor(this.taille*2/3)+"pt;");
    span2.setAttribute("dy", Math.floor(this.taille/3));
    var cont2 = document.createTextNode(fin);
    span2.appendChild(cont2);
    text.appendChild(span2);
  }
  text.setAttribute("pointer-events", "none");
  this.g = text;
};
*/
iep.nomPoint.prototype.creeg = function() {
  var indbalise,tspan,ch,ch2,style,txt,ind,indexp,indind,mini,bexp,tailleind,stylespan,
          y,sp,i,debutLigne,an;
  var dy = 0; // Le baselineshift de chaque tspan
  var inf = "<"; // Equivalent du symbole <
  var debexp = "£e(";
  var debind = "£i(";
  var g = document.createElementNS(iep.svgsn,"g");
  var hautlig = parseFloat(this.taille)+2;
  var decblp = 0; // Décalage vers le bas de la ligne précédente
  var decalage = parseFloat(this.taille)*0.4; // Décalage vers le bas ou le haut en cas d'indice ou d'exposant
 
  if (this.nom.length != 0 ) {
    txt = document.createElementNS(iep.svgsn,"text");
    txt.setAttribute("pointer-events", "none");
    txt.setAttribute("x",0);
    txt.setAttribute("y",0);
    var style = "text-anchor:left;font-size:" + this.taille +"px;" + "fill:" + this.couleur+";";
    txt.setAttribute("style",style);
    // On remplace les espaces par des espaces insécables
    ch2 = this.nom.replace(new RegExp(" ","g"),"\u00A0");
    // Affecter une longueur ne peut être efficace que pour du texte simpel sans balise
    if ((this.nom.indexOf(inf) === -1) && (this.nom.indexOf(debexp) === -1) && (this.nom.indexOf(debind) === -1)
      && (this.nom.indexOf("<br>") === -1)) {
      txt.appendChild(document.createTextNode(ch2));
    }
    else {
      sp = ch2.split(/<br>/gi);
      y = -hautlig;
      for (i = 0; i < sp.length; i++) {      
        debutLigne = true;
        ch = sp[i];
        while (ch.length !== 0) {
          indbalise = ch.indexOf(inf);
          indexp = ch.indexOf(debexp);
          indind = ch.indexOf(debind);
          if (debutLigne) y += hautlig + decblp;
          if (indexp !== -1) y += decalage;
          if ((indbalise === -1) && (indexp === -1) && (indind === -1)) {
            tspan = document.createElementNS(iep.svgsn,"tspan");
            tspan.setAttribute("pointer-events", "none");
            tspan.setAttribute("dy",dy);
            if (debutLigne){
              tspan.setAttribute("x",0);
              tspan.setAttribute("y",y);
              debutLigne = false;
            }
            dy = 0; // On est revenu au niveau 0
            tspan.appendChild(document.createTextNode(ch))
            txt.appendChild(tspan);
            break;
          }
          else {
            while ((ch.indexOf(inf) !== -1) || (ch.indexOf(debexp) !== -1)
                    || (ch.indexOf(debind) !== -1)) {
              indbalise = ch.indexOf(inf);
              indexp = ch.indexOf(debexp);
              indind = ch.indexOf(debind);
              if (indbalise === -1) {
                if (indexp === -1) mini = indind;
                else mini = (indind === -1) ? indexp : Math.min(indind,indexp);
              }
              else {
                if (indexp === -1) mini = (indind === -1) ? indbalise : Math.min(indbalise,indind);
                else {
                  if (indind === -1) mini = Math.min(indbalise,indexp);
                  else mini = Math.min(indbalise,indind,indexp);
                }
              }
              if (mini>0) { // Il y a du texte avant les balises
                tspan = document.createElementNS(iep.svgsn,"tspan");
                tspan.setAttribute("pointer-events", "none");
                tspan.setAttribute("dy",dy);
                if (debutLigne){
                  tspan.setAttribute("x",0);
                  tspan.setAttribute("y",y);
                  debutLigne = false;
                }
                dy = 0; // On est revenu au niveau 0
                tspan.appendChild(document.createTextNode(ch.substring(0,mini)))
                txt.appendChild(tspan);
                ch = ch.substring(mini);
              }
              else {
                if (indbalise === 0) {
                  var infoBalise = new iep.infoBalise(false,false,false,this.couleur,"",this.taille);
                  ind = iep.indiceFinBalise(ch);
                  if (ind === -1) iep.traiteBalise(ch,infoBalise,txt,debutLigne,y);
                  else iep.traiteBalise(ch.substring(0,ind),infoBalise,txt,debutLigne,y);
                  debutLigne = false;
                  if (ind !== -1) ch = ch.substring(ind); else ch = "";
                  dy = 0; // On est revenu au niveau 0

                }
                else { // mini est égal à 0
                  bexp = mini === indexp;
                  an = iep.analyseExposantOuIndice(ch);
                  tspan = document.createElementNS(iep.svgsn,"tspan");
                  tspan.setAttribute("pointer-events", "none");
                  tspan.setAttribute("dy",dy+"px");
                  if (debutLigne){
                    tspan.setAttribute("x",0);
                    tspan.setAttribute("y",y);
                    debutLigne = false;
                  }
                  if (an.erreur) tspan.appendChild(document.createTextNode(ch));
                  else {
                    tspan.appendChild(document.createTextNode(an.operande));
                    txt.appendChild(tspan);
                    tspan = document.createElementNS(iep.svgsn,"tspan");
                    tspan.setAttribute("pointer-events", "none");
                    tspan.appendChild(document.createTextNode(an.exposant));
                    dy = decalage;
                    if (bexp) dy = -dy;
                    else decblp = dy;
                    tspan.setAttribute("dy",dy+"px");
                    tailleind = parseFloat(this.taille)*0.6;
                    stylespan = "font-size:"+tailleind+"px;";
                    tspan.setAttribute("style",stylespan);
                    txt.appendChild(tspan);
                    dy = -dy; //Car sinon la suite sera décalée aussi en hauteur
                  }
                  ch = an.texte;
                }
              }
            }
          }
        }
      }
    }
    g.appendChild(txt);
    this.g = g;
  }
  g.setAttribute("visibility","hidden");
  // g.setAttribute("id",this.id);
}
/**
 * Fonction appelée par prepare() qui récupère l'élement svg graphique représentant
 * la formule dans le div provisoire précédemment créé, détruit ensuite ce div provisoire
 * Rajoute les éléments graphiques correspondant à un cadre si une couleur de fond
 * et un cadre ont été demandés
 */
iep.nomPoint.prototype.creegLatex = function() {
  var w,h,ratio;
  try {
    var g = document.createElementNS(iep.svgsn,"g");
    var c1 = this.div.childNodes[1];
    if (c1 == undefined) return document.createElementNS(iep.svgsn,"g");
    var c2 = c1.childNodes[0];
    if (c2 == undefined) return document.createElementNS(iep.svgsn,"g");
    var s = c2.childNodes[0];
    if (s == undefined) return document.createElementNS(iep.svgsn,"g");
    // Pour gérer Chrome
    while(s.tagName === "SPAN") s = s.childNodes[0];
    // Le test suivant est du à la compatibilité avec l'explorer
    if ((s.clientWidth != 0) && (s.clientHeight != 0)) {
      w = s.clientWidth;
      h = s.clientHeight;
    }
    else {
      var b = s.getBBox();
      ratio = b.height/b.width;
      w = this.div.clientWidth;
      h = w*ratio;
    }
    var t = this.taille;
    if (h < t) h = t;
    s.setAttribute("x","0");
    s.setAttribute("y", String(-this.taille)); // Différent de MathGraph32
    s.setAttribute("width", w+"px");
    s.setAttribute("height",h+"px");
    g.appendChild(s.parentNode.removeChild(s));
    document.body.removeChild(this.div);
    g.setAttribute("visibility","hidden");
    g.setAttribute("id",this.id);
    this.g = g;
  }
  catch(e) {
    if (this.div != null) document.body.removeChild(this.div);
    this.g = document.createElementNS(iep.svgsn,"g"); //Crée un g vide en cas de problème
  }
};

/** @inheritDoc */
iep.nomPoint.prototype.positionne = function() {
  var xp = this.point.x;
  
  var yp = this.point.y;
  this.g.setAttribute("transform","translate(" + String(xp + this.dx) + "," + String(yp + this.dy + this.taille) + ")");
};

/**
 * Fonction traitant la chaîne ch pour la transcrire en code LaTeX
 * Renvoie la chaîne LaTeX correspondante.
 * Si la chaîne contient des balises <br>, un tableau LaTeX est utilisé pour rendre le contenu
 * @param {string} ch : la chaîne à traiter
 * @returns {string} la chaîne LaTeX correpondante une fois ch traduite
 */
iep.nomPoint.prototype.traiteMaths = function(ch) {
   return iep.traiteAccents(iep.getMathsForName(ch,true)); // True pour mode texte
 };



/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant un repère de la figure
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : le docmument propriétaire
 * @param {string} hauteur : Hauteur du cadre contenant le repère, exprimées en centimètres
 * @param {string} largeur : Largeur du cadre contenant le repère, exprimées en centimètres
 * @param {string} haut : abscisse de ce cadre relativement au bord haut et au
 * bord gauche de la zone de dessin, en centimètres
 * @param {string} gauche : Ordonnée de ce cadre relativement au bord haut et
 * au bord gauche de la zone de dessin, en centimètres
 * @param {string} xgrad : donne les unités de graduations : si Xgrad est égal à 5,
 *  le repère va être gradué de 5 en 5
 * @param {string} ygrad : donne les unités de graduations : si Ygrad est égal à 5, le repère va être gradué de 5 en 5
 * @param {string} xmin : valeur mini des abscisses dans le repère
 * @param {string} xmax : valeur maxi des abscisses dans le repère
 * @param {string} ymin : valeur mini des ordonnées dans le repère
 * @param {string} ymax : valeur maxi des ordonnées dans le repère
 * @param {string} couleur : Couleur du tracé
 * @param {string} grille : "invisible” si on souhaite que le quadrillage soit masqué
 * @param {type} axes : “invisible” si on souhaite que les axes soient masqué
 * @param {type} etiquettes : “invisible” si on souhaite que les étiquettes des axes soient masquées
 */
iep.repere = function (doc, hauteur, largeur, haut, gauche, xgrad, ygrad, xmin, xmax,
        ymin, ymax, couleur, grille, axes, etiquettes) {
  iep.objetBase.call(this, doc, "repereIEP", couleur); // Le repère aura pour Id repereIEP
  this.hauteur = hauteur;
  this.largeur = largeur;
  this.haut = (haut == null) ? 0 : parseFloat(haut);
  this.gauche = (gauche == null) ? 0 : parseFloat(gauche);
  this.xgrad = parseFloat(xgrad);
  this.ygrad = parseFloat(ygrad);
  this.xmin = parseFloat(xmin);
  this.xmax = parseFloat(xmax);
  this.ymin = parseFloat(ymin);
  this.ymax = parseFloat(ymax);
  this.grille = (grille == null) ? "visible" : grille;
  this.axes = (axes == null) ? "visible" : axes;
  this.etiquettes = (etiquettes == null) ? "visible" : etiquettes;
  this.cadre = new Object();
  this.cadre.gauche = this.gauche*30; // 30 px pour 1 cm
  this.cadre.haut = this.haut*30;
  this.cadre.droite = this.cadre.gauche + this.largeur*30;
  this.cadre.bas = this.cadre.haut + this.hauteur*30;
};
iep.repere.prototype = new iep.objetBase();
/**
 * 
 * @param {type} nbr
 * @returns {Float}
 */
iep.repere.prototype.mettre_x_en_pixels = function (nbr) {
  return iep.mettre_en_pixels(nbr, this.xmin, this.xmax, this.cadre.gauche, this.cadre.droite);
};
/**
 * Fonction utilisée dans l'objet repere. Repris du code Flash
 */
iep.repere.prototype.mettre_y_en_pixels = function (nbr) {
  return iep.mettre_en_pixels(nbr, this.ymax, this.ymin, this.cadre.haut, this.cadre.bas);
};
/**
 * 
 * @param {type} debut_trace_x
 * @param {type} unite_x
 * @param {type} nb_grad_x
 * @param {type} ordo_etiquettes
 * @param {type} haut
 * @param {type} bas
 */
iep.repere.prototype.tracer_abscisses = function (debut_trace_x, unite_x, nb_grad_x, ordo_etiquettes, haut, bas) {
  var li, txt;
  var tailleEtiquettes = 12; // Taille de la police utilisée pour les étiquettes
  var style = "stroke:black;stroke-width:0.5;";

  if (this.grille === "invisible" && this.axes !== "invisible") {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    li.setAttribute("x1", this.cadre.gauche);
    li.setAttribute("y1", ordo_etiquettes);
    li.setAttribute("x2", this.cadre.droite);
    li.setAttribute("y2", ordo_etiquettes);
    this.g.appendChild(li);
  }
  var abscisse;
  for (var i = 0; i <= nb_grad_x; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    abscisse = this.mettre_x_en_pixels(debut_trace_x + i * unite_x);
    if (this.grille === "invisible") {
      if (this.axes !== "invisible") {
        li.setAttribute("x1", abscisse);
        li.setAttribute("y1", String(ordo_etiquettes - 5));
        li.setAttribute("x2", abscisse);
        li.setAttribute("y2", String(ordo_etiquettes + 5));
        this.g.appendChild(li);
      }
    } else {
      li.setAttribute("x1", abscisse);
      li.setAttribute("y1", haut);
      li.setAttribute("x2", abscisse);
      li.setAttribute("y2", bas);
      this.g.appendChild(li);
    }
    if (this.etiquettes !== "invisible" && this.axes !== "invisible") {
      txt = document.createElementNS(iep.svgsn, "text");
      txt.setAttribute("x", abscisse+2);
      txt.setAttribute("y", ordo_etiquettes+tailleEtiquettes);
      var stylet = "text-anchor:left;font-size:" + tailleEtiquettes + "px;" + "fill:" + this.couleur + ";";
      txt.setAttribute("style", stylet);
      txt.appendChild(document.createTextNode(String(debut_trace_x + i * unite_x)));
      this.g.appendChild(txt);
    }
  }
};
/**
 * 
 * @param {type} debut_trace_y
 * @param {type} unite_y
 * @param {type} nb_grad_y
 * @param {type} abs_etiquettes
 * @param {type} gauche
 * @param {type} droite
 */
iep.repere.prototype.tracer_ordonnees = function (debut_trace_y, unite_y, nb_grad_y, abs_etiquettes, gauche, droite) {
  var li, txt;
  var tailleEtiquettes = 12; // Taille de la police utilisée pour les étiquettes
  var style = "stroke:black;stroke-width:0.5;";
  if (this.grille === "invisible" && this.axes !== "invisible") {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    li.setAttribute("x1", abs_etiquettes);
    li.setAttribute("y1", this.cadre.haut);
    li.setAttribute("x2", abs_etiquettes);
    li.setAttribute("y2", this.cadre.bas);
    this.g.appendChild(li);
  }
  var ordonnee;
  for (var i = 0; i <= nb_grad_y; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    ordonnee = this.mettre_y_en_pixels(debut_trace_y + i * unite_y);
    if (this.grille === "invisible") {
      if (this.axes !== "invisible") {
        li.setAttribute("x1", String(abs_etiquettes - 5));
        li.setAttribute("y1", ordonnee);
        li.setAttribute("x2", String(abs_etiquettes + 5));
        li.setAttribute("y2", ordonnee);
        this.g.appendChild(li);
      }
    } else
    {
      li.setAttribute("x1", gauche);
      li.setAttribute("y1", ordonnee);
      li.setAttribute("x2", droite);
      li.setAttribute("y2", ordonnee);
      this.g.appendChild(li);
    }
    if (this.etiquettes !== "invisible" && this.axes !== "invisible") {
      txt = document.createElementNS(iep.svgsn, "text");
      txt.setAttribute("x", abs_etiquettes+2);
      txt.setAttribute("y", ordonnee+tailleEtiquettes);
      var stylet = "text-anchor:left;font-size:" + tailleEtiquettes + "px;" + "fill:" + this.couleur + ";";
      txt.setAttribute("style", stylet);
      txt.appendChild(document.createTextNode(String(debut_trace_y + i * unite_y)));
      this.g.appendChild(txt);
    }
  }
};
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.repere.prototype.creeg = function() {
  var width = this.cadre.droite - this.cadre.gauche;
  var height = this.cadre.bas - this.cadre.haut;
  this.g = document.createElementNS(iep.svgsn,"g");
  var defs = document.createElementNS (iep.svgsn,"defs");
  var clip = document.createElementNS (iep.svgsn,"clipPath");
  clip.setAttribute("id","clipRepere");
  var rect = document.createElementNS(iep.svgsn, "rect");
  rect.setAttribute("style",style);
  rect.setAttribute("x", this.cadre.gauche);
  rect.setAttribute("y",this.cadre.haut);
  rect.setAttribute("width",width);
  rect.setAttribute("height",height);
  clip.appendChild(rect);
  defs.appendChild(clip);
  this.g.appendChild(defs);
  this.g.setAttribute("style","clip-path:url(#clipRepere)");

  //Si besoin, on trace des graduations pour les abscisses
  if ((this.xmax - this.xmin) > this.xgrad) {
    //nombre de graduations qui appraitront
    var nb_grad_x = Math.floor((this.xmax - this.xmin) / this.xgrad);
    /* 
     determination de l'ordonnée des étiquettes pour les abscisses
     */
    var ordo_etiquettes;
    if (this.ymax * this.ymin < 0) {
      ordo_etiquettes = this.mettre_y_en_pixels(0);
    } else if (this.ymax < 0) {
      ordo_etiquettes = this.cadre.haut;
    }
    else {
      ordo_etiquettes = this.cadre.bas;
    }
    /*
     FIN ETIQUETTES
     */

    /*
     DETERMINATION ABSCISSES
     */
    var debut_trace_x = iep.determiner_graduations(this.xmin, this.xmax, this.xgrad);
    this.tracer_abscisses(debut_trace_x, this.xgrad, nb_grad_x, ordo_etiquettes, this.cadre.haut, this.cadre.bas);
    /*
     FIN ABSCISSES
     */

  }
  if ((this.ymax - this.ymin) > this.ygrad) {
    var nb_grad_y = Math.floor((this.ymax - this.ymin) / this.ygrad);
    /* 
     determination de l'abscisse des étiquettes pour les ordonnées
     */
    var abs_etiquettes;
    if (this.xmax * this.xmin < 0) {
      abs_etiquettes = this.mettre_x_en_pixels(0);
    } else if (this.xmax < 0) {
      abs_etiquettes = this.cadre.droite;
    }
    else {
      abs_etiquettes = this.cadre.gauche;
    }
    var debut_trace_y = iep.determiner_graduations(this.ymin, this.ymax, this.ygrad);
    this.tracer_ordonnees(debut_trace_y, this.ygrad, nb_grad_y, abs_etiquettes, this.cadre.gauche, this.cadre.droite);
  }
  // tracer_les_fonctions();
  var rect2 = document.createElementNS(iep.svgsn, "rect");
  var style = "stroke:" + this.couleur + ";stroke-width:1;fill:none;";
  rect2.setAttribute("style",style);
  rect2.setAttribute("x", this.cadre.gauche);
  rect2.setAttribute("y",this.cadre.haut);
  rect2.setAttribute("width",width);
  rect2.setAttribute("height",height);
  this.g.appendChild(rect2);
  this.g.setAttribute("visibility","hidden");
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Classe représentant un quadrillage de la figure
 * @extends iep.objetBase
 * @constructor
 * @param {iep.iepDoc} doc : Le document propriétaire
 * @param {string} type : Le type de quadrillage : "quadrillage","seyes","5x5","10x10","millimetre"
 * @param {string} hauteur : La hauteur du cadre en cm (1 cm = 30 pixels)
 * @param {string} largeur : La largeur du cadre en cm (1 cm = 30 pixels)
 * @param {string} haut : La position en ordonnées du cadre dans la fenêtre
 * @param {string} gauche : La position en abscisses du cadre dans la fenêtre
 * @param {type} couleur : La couleur du quadrillage
 */
iep.quadrillage = function (doc, type, hauteur, largeur, haut, gauche, couleur) {
  iep.objetBase.call(this, doc, "quadrillageIEP", couleur); // Le repère aura pour Id quadrillageIEP 
  this.hauteur = parseFloat(hauteur);
  this.largeur = parseFloat(largeur);
  this.haut = (haut == null) ? 0 : parseFloat(haut);
  this.gauche = (gauche == null) ? 0 : parseFloat(gauche);
  this.type = type;
  this.cadre = new Object();
  this.cadre.gauche = this.gauche * 30; // 30 px pour 1 cm
  this.cadre.haut = this.haut * 30;
  this.cadre.droite = this.cadre.gauche + this.largeur * 30;
  this.cadre.bas = this.cadre.haut + this.hauteur * 30;
  this.objet = "quadrillage";
};

iep.quadrillage.prototype = new iep.objetBase();
/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.quadrillage.prototype.creeg = function() {
  var gauche = this.cadre.gauche;
  var droite = this.cadre.droite;
  var haut = this.cadre.haut;
  var bas = this.cadre.bas;
  var couleur = this.couleur;
  this.g = document.createElementNS(iep.svgsn,"g");
  // Un seul quadrillage sur une figure.
  var oldg = this.doc.svg.getElementById("quadrillageIEP");
  if (oldg != null) this.doc.svg.removeChild(oldg);
  // this.g.setAttribute("id","quadrillageIEP");
  // DessinIep.rectanglePlein(grille.cache, cadre.gauche, cadre.haut, cadre.droite, cadre.bas, 0, 100);
  switch (this.type) {
  case "seyes":
    this.tracer_grille(couleur, 24, 6, gauche, haut, droite, bas, false, 1); // 1 est l'opacité
    this.tracer_grille(couleur, 24, 24, gauche, haut, droite, bas, false, 1);
    break;
  case "millimetre":
    this.tracer_grille(couleur, 3, 3, gauche, haut, droite, bas, false, 0.6);
    this.tracer_grille(couleur, 30, 30, gauche, haut, droite, bas, false, 0.8);
    this.tracer_grille(couleur, 150, 150, gauche, haut, droite, bas, false, 1);
    break;
  case "10x10":
  case "10x":
    this.tracer_grille(couleur, 30, 30, gauche, haut, droite, bas, false, 1);
    break;
  case "5x5":
  case "5x":
  default:
    this.tracer_grille(couleur, 15, 15, gauche, haut, droite, bas, false, 1);
    break;
  }
};
/**
 * Fonctoon reprise à partir du code de la version Flash
 * @param {string} epaisseur
 * @param {string} couleur
 * @param {Float} ecart_x
 * @param {Float} ecart_y
 * @param {Float} debut_x
 * @param {Float} debut_y
 * @param {Float} fin_x
 * @param {Float} fin_y
 * @param {Float} pointille
 * @param {Float} alpha
 */
iep.quadrillage.prototype.tracer_grille = function (couleur, ecart_x, ecart_y, debut_x, debut_y, fin_x, fin_y, pointille, alpha) {
  var li;
  var style = "stroke:" + couleur + ";stroke-width:0.5;"+"stroke-opacity:"+alpha+";";
  if(pointille) this.style += "stroke-dasharray:2 2;" // Pointillés
  var nb = Math.ceil(Math.abs(fin_x - debut_x) / ecart_x);
  for (var i = 0; i < nb; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    li.setAttribute("x1", debut_x + i * ecart_x);
    li.setAttribute("y1", debut_y);
    li.setAttribute("x2", debut_x + i * ecart_x);
    li.setAttribute("y2", fin_y);
    li.setAttribute("style", style);
    this.g.appendChild(li);
    // segment(unClip, debut_x + i * ecart_x, debut_y, debut_x + i * ecart_x, fin_y, pointille, false, epaisseur, couleur, alpha);
  }
  nb = Math.ceil(Math.abs(fin_y - debut_y) / ecart_y);
  for (var i = 0; i < nb; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    li.setAttribute("x1", debut_x);
    li.setAttribute("y1", debut_y + i * ecart_y);
    li.setAttribute("x2", fin_x);
    li.setAttribute("y2", debut_y + i * ecart_y);
    li.setAttribute("style", style);
    this.g.appendChild(li);

    // segment(unClip, debut_x, debut_y + i * ecart_y, fin_x, debut_y + i * ecart_y, pointille, false, epaisseur, couleur, alpha);
  }
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Classe représentant un axe dans une figure InstrumenPoche
 * Repris à partir du code Flash
 * @extends iep.objetBase
 * @constructor
 * @param {ie.iepDoc} doc : le document propriétaire
 * @param {string} pente : "horizontal" pour un axe horizontal, sinon vertical
 * @param {string} largeur
 * @param {string} haut
 * @param {string} gauche
 * @param {string} distanceBord
 * @param {string} xgrad
 * @param {string} xmin
 * @param {string} xmax
 * @param {string} couleur : la couleur de l'axe
 */
iep.axe = function (doc, pente, largeur, haut, gauche, distanceBord, xgrad, xmin, xmax, couleur) {
  iep.objetBase.call(this, doc, "axeIEP", couleur); // Le repère aura pour Id axeIEP 
  this.pente = (pente == null) ? "horizontal" : pente;
  this.largeur = largeur;
  this.haut = (haut == null) ? 0 : parseFloat(haut);
  this.gauche = (gauche == null) ? 0 : parseFloat(gauche);
  this.distanceBord = parseFloat(distanceBord);
  this.xgrad = xgrad;
  this.xmin = xmin;
  this.xmax = xmax;
  this.cadre = new Object();
  this.cadre.gauche = this.gauche * 30; // 30 px pour 1 cm
  this.cadre.haut = this.haut * 30;
  this.width = 30*largeur;
  this.cadre.droite = this.gauche + this.width;
  this.cadre.bas = this.haut + this.width;
};
iep.axe.prototype = new iep.objetBase();

/**
 * Fonction mettant dans this.g l'élément graphique représentant l'objet dans
 * le DOM du svg de la figure
 */
iep.axe.prototype.creeg = function () {
  var li;
  this.g = document.createElementNS(iep.svgsn,"g");
  var style = "stroke:black;stroke-width:1;";
  //Si besoin, on trace des graduations pour les abscisses
  if (this.pente === "horizontal" && (this.xmax - this.xmin) > this.xgrad) {
    //nombre de graduations qui apparaitront
    var nb_grad_x = Math.floor((this.xmax - this.xmin) / this.xgrad);
    /*
     determination de l'ordonnée de l'axe horizontal
     */
    var bord = this.distanceBord * 30;

    /*
     FIN ETIQUETTES
     */

    /*
     DETERMINATION ABSCISSES
     */
    var debut_trace_x = iep.determiner_graduations(this.xmin, this.xmax, this.xgrad);

    this.tracer_abscisses(debut_trace_x, this.xgrad, nb_grad_x, bord, bord - 10, bord + 10);
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    li.setAttribute("x1", this.cadre.gauche);
    li.setAttribute("y1", bord);
    li.setAttribute("x2", this.cadre.droite);
    li.setAttribute("y2", bord);
    this.g.appendChild(li);
    /*
     FIN ABSCISSES
     */

  } else if ((this.xmax - this.xmin) > this.xgrad) {
    //nombre de graduations qui appraitront
    var nb_grad_x = Math.floor((this.xmax - this.xmin) / this.xgrad);
    /* 
     determination de l'ordonnée de l'axe horizontal
     */
    var bord = Number(this.distanceBord) * 30;

    /*
     FIN ETIQUETTES
     */

    /*
     DETERMINATION ABSCISSES
     */
    var debut_trace_x = iep.determiner_graduations(this.xmin, this.xmax, this.xgrad);

    this.tracer_ordonnees(debut_trace_x, this.xgrad, nb_grad_x, bord, bord - 10, bord + 10);
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    li.setAttribute("x1", bord);
    li.setAttribute("y1", this.cadre.bas);
    li.setAttribute("x2", bord);
    li.setAttribute("y2", this.cadre.haut);
    this.g.appendChild(li);

    /*
     FIN ABSCISSES
     */
  }
};
/**
 * Fonction reprise du code Flash, utiilisée dans creeg()
 */
iep.axe.prototype.tracer_abscisses = function (debut_trace_x, unite_x, nb_grad_x, ordo_etiquettes, haut, bas) {
  var li, txt;
  var tailleEtiquettes = 12; // Taille de la police utilisée pour les étiquettes
  var style = "stroke:black;stroke-width:1.5;";

  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("style", style);
  li.setAttribute("x1", this.cadre.gauche);
  li.setAttribute("y1", ordo_etiquettes);
  li.setAttribute("x2", this.cadre.droite);
  li.setAttribute("y2", ordo_etiquettes);
  this.g.appendChild(li);
  var abscisse;
  for (var i = 0; i <= nb_grad_x; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    abscisse = this.mettre_x_en_pixels(debut_trace_x + i * unite_x);
    li.setAttribute("x1", abscisse);
    li.setAttribute("y1", String(ordo_etiquettes - 5));
    li.setAttribute("x2", abscisse);
    li.setAttribute("y2", String(ordo_etiquettes + 5));
    this.g.appendChild(li);
    txt = document.createElementNS(iep.svgsn, "text");
    txt.setAttribute("x", abscisse);
    txt.setAttribute("y", ordo_etiquettes+tailleEtiquettes+5);
    var stylet = "text-anchor:left;font-size:" + tailleEtiquettes + "px;" + "fill:" + this.couleur + ";";
    txt.setAttribute("style", stylet);
    txt.appendChild(document.createTextNode(String(debut_trace_x + i * unite_x)));
    this.g.appendChild(txt);
  }
};
/**
 * Fonction reprise du code Flash, utiilisée dans creeg()
 */
iep.axe.prototype.tracer_ordonnees = function (debut_trace_y, unite_y, nb_grad_y, abs_etiquettes, gauche, droite) {
  var li, txt;
  var tailleEtiquettes = 12; // Taille de la police utilisée pour les étiquettes
  var style = "stroke:black;stroke-width:1.5;";
  li = document.createElementNS(iep.svgsn, "line");
  li.setAttribute("style", style);
  li.setAttribute("x1", abs_etiquettes);
  li.setAttribute("y1", this.cadre.haut);
  li.setAttribute("x2", abs_etiquettes);
  li.setAttribute("y2", this.cadre.bas);
  this.g.appendChild(li);
  var ordonnee;
  for (var i = 0; i <= nb_grad_y; i++) {
    li = document.createElementNS(iep.svgsn, "line");
    li.setAttribute("style", style);
    ordonnee = this.mettre_y_en_pixels(debut_trace_y + i * unite_y);
    li.setAttribute("x1", String(abs_etiquettes - 5));
    li.setAttribute("y1", ordonnee);
    li.setAttribute("x2", String(abs_etiquettes + 5));
    li.setAttribute("y2", ordonnee);
    this.g.appendChild(li);
    txt = document.createElementNS(iep.svgsn, "text");
    txt.setAttribute("x", abs_etiquettes+10);
    txt.setAttribute("y", ordonnee + 2);
    var stylet = "text-anchor:left;font-size:" + tailleEtiquettes + "px;" + "fill:" + this.couleur + ";";
    txt.setAttribute("style", stylet);
    txt.appendChild(document.createTextNode(String(debut_trace_y + i * unite_y)));
    this.g.appendChild(txt);
  }
};
/**
 * Fonction utilisée dans iep.axe
 * @param {type} nbr
 * @returns {Float}
 */
iep.axe.prototype.mettre_x_en_pixels = function (nbr) {
  return iep.mettre_en_pixels(nbr, this.xmin, this.xmax, this.cadre.gauche, this.cadre.droite);
};
/**
 * Fonction utilisée dans iep.axe
 * @param {type} nbr
 * @returns {Float}
 */
iep.axe.prototype.mettre_y_en_pixels = function (nbr) {
  return iep.mettre_en_pixels(nbr, this.xmax, this.xmin, this.cadre.haut, this.cadre.bas);
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action ancetre de toutes les actions de la figure
 * @constructor
 * @param {iep.iepDoc} doc : le document contenant les objets
 * @param {string} tempo : Le tempo à attendre à la fin de l'action en dixièmes de seconde
 * avant de passer à l'action suivante. Si null on passe directement à l'action suivante.
 */
iep.actionAncetre = function(doc, tempo) {
  if (arguments.length === 0) return;
  this.doc = doc;
  this.tempo = (tempo === null) ? null : parseInt(tempo)*100; // tempo est en dizièmes de seconde
  this.isReady = false; // Sera mis à true une fois qu'on est sûr que l'élément est bien chargé et prêt.
};
/**
 * Fonction renvoyant lorsque l'action a été chargé et prête.
 * Est redéfini pour les actions dont le chargement est asynchrone : images et actions
 * d'écriture de texte nécessitant l'utilisation du LaTeX.
 */
iep.actionAncetre.prototype.setReady = function() {
  this.isReady = true;
};
/**
 * Fonction qui n'aura une action que pour les actions de création d'objet et créera l'élément graphique
 * du DOM associé Srea redéfini pour les actions de création d'objet
 */
iep.actionAncetre.prototype.creegElement = function() {
};
/**
 * Fonction renvoyant true seulement si l'action a une action visible sur la figure.
 * Lors de l'appel de iepDoc.ajouteAction, chaque action se voit attribuer un membre indice
 * qui est son indice dans la liste des actions créées..
 * Renvoie true par défaut.
 * @returns {boolean}
 */
iep.actionAncetre.prototype.actionVisible = function() {
  return true;
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action de création de l'objet objet d'id id dans le document doc
 * @extends iep.actionAncetre
 * @constructor
 * @param {type} doc
 * @param {type} id
 * @param {type} objet
 */
iep.actionCreation = function(doc,id,objet,tempo,vitesse) {
  iep.actionAncetre.call(this,doc,tempo);
  this.id = id;
  var ob = this.doc.getElement(id, objet.objet);
  this.objetRemplace = (ob === null) ? null : ob;
  this.objet = objet;
  if (arguments.length >= 5) this.vitesse = (vitesse != null) ? Math.abs(parseInt(vitesse)) : 8;
  else this.vitesse = 8;
  if (this.objetRemplace !== null) this.doc.addElement(this.objet);
  else this.doc.pushElement(this.objet);
};
iep.actionCreation.prototype = new iep.actionAncetre();
/**
 * Fonction lançant l'animation de création de l'objet objet si la création de l'objet nécessite une animation
 * et si immediat est à false et sinon affichant immédiatement l'objet
 * @param {boolean} immediat
 */
iep.actionCreation.prototype.execute = function(immediat) {
  // this.doc.svg.appendChild(this.objet.g);
  if (this.objetRemplace !== null) {
    this.objetRemplace.montre(false);
    // this.doc.svg.replaceChild(this.objet.g,this.objetRemplace.g);
  }
  if (this.objet.creationAnimee() && !immediat) {
    this.objet.lanceAnimation(this.vitesse);
  }
  else {
    this.objet.montre(true);
    if (immediat) this.objet.finAction();
    else this.doc.actionSuivante(immediat);
  }
};
/**
 * Fonction créant l'élément graphique du DOM associé à une action de création d'objet
 * L'objet est calculé par positionne() et caché
 */
iep.actionCreation.prototype.creegElement = function() {
  var doc = this.doc;
  var objet = this.objet;
  objet.creeg();
  objet.positionne();
  objet.g.setAttribute("visibility","hidden");
  doc.svg.appendChild(objet.g);
};
/** @inheritDoc */
iep.actionCreation.prototype.actionVisible = function() {
  return !(this.objet instanceof iep.texte);
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action masquant un objet.
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.objetBase} objet : l'objet à masquer
 */
iep.actionMasquer = function(doc,idel,nature,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  // this.idel = idel;
  this.objet = this.doc.getElement(idel,nature);
};
iep.actionMasquer.prototype = new iep.actionAncetre();
/**
 * Fonction exécutant le masque de l'objet objet
 * @param {boolean} immediat : true en cas d'exécution immédiate sans passage
 * à l'action suivante
 */
iep.actionMasquer.prototype.execute = function(immediat) {
  // var el = this.doc.getElement(this.idel);
  var el = this.objet;
  if (el != null) el.montre(false);
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionMasquer.prototype.actionVisible = function() {
  return this.doc.getObjectVisibility(this.objet,this.indice-1);
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action montrant un objet.
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.objetBase} objet : l'objet à démasquer
 */
iep.actionMontrer = function(doc,idel,nature,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  // this.idel = idel;
  this.objet = this.doc.getElement(idel,nature);
};
iep.actionMontrer.prototype = new iep.actionAncetre();
/**
 * Fonction démasquant l'élément graphique de l'objet objet
 * @param {type} immediat
 */
iep.actionMontrer.prototype.execute = function(immediat) {
  // var el = this.doc.getElement(this.idel);
  var el =  this.objet;
  if (el !== null) el.montre(true);
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionMontrer.prototype.actionVisible = function() {
  return !this.doc.getObjectVisibility(this.objet,this.indice-1);
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action masquant un instrument.
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.instrumentAncetre} instrument : l'instrument à masquer
 */
iep.actionMasquerInstrument = function(doc,instrument,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  this.instrument = instrument;
};
iep.actionMasquerInstrument.prototype = new iep.actionAncetre();
/**
 * Fonction exécutant l'action et masquanf l'instrument instrument
 * @param {type} immediat
 */
iep.actionMasquerInstrument.prototype.execute = function(immediat) {
  var compas = this.doc.compas;
  this.instrument.montre(false);
  if (compas.leve) this.doc.compasLeve.montre(false);
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionMasquerInstrument.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action montrant un instrument.
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.instrumentAncetre} instrument : l'instrument à montrer
 */
iep.actionMontrerInstrument = function(doc,instrument,x,y,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  this.x = (x === null) ? null : parseFloat(x);
  this.y = (y === null) ? null : parseFloat(y);
  this.instrument = instrument;
};
iep.actionMontrerInstrument.prototype = new iep.actionAncetre();
/**
 * Fonction montrant l'instrument instrument
 * @param {boolean} immediat : si rrue on ne passe pas à l'action suivante
 */
iep.actionMontrerInstrument.prototype.execute = function(immediat) {
  var compas = this.doc.compas;
  if ((this.x !== null) && (this.y !== null)) this.instrument.translate(this.x, this.y);
  if ((this.instrument === compas) && !immediat) {
    if (!compas.leve) compas.montre(true);
    else this.doc.compasLeve.montre(true);
    compas.visible = true;
  }
  else this.instrument.montre(true);
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionMontrerInstrument.prototype.actionVisible = function() {
  return !this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}


/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action de rotation d'un instrument
 * A noter que le paramètre correspondant à la vitesse s'appelle sens 
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.instrementAncetre} instrument : l'instrument à tourner
 * @param {string} anglefin : l'angle de fin
 * @param {string} tempo :  le tempo de temporisation (ou null)
 * @param {string} deg10 : le nombre de degrés à tourner à chaque dizième de seconde
 * ou null( sera alors de 8)
 */
iep.actionRotationInstrument = function(doc,instrument,anglefin,tempo,deg10) {
  iep.actionAncetre.call(this,doc,tempo);
  this.instrument = instrument;
  this.anglefin = parseFloat(anglefin);
  this.deg10 = (deg10 == null) ? 8 : Math.abs(parseFloat(deg10));
};

iep.actionRotationInstrument.prototype = new iep.actionAncetre();
/**
 * Fonction lançant l'animation de rotation de l'instrument si immediat est false
 * @param {boolean} immediat
 */
iep.actionRotationInstrument.prototype.execute = function(immediat) {
  if (!immediat) this.instrument.lanceAnimationRotation(this.anglefin, this.deg10);
  else {
    this.instrument.angle = this.anglefin;
    this.instrument.positionne();
    if (!immediat) this.doc.actionSuivante(immediat);
  }
};
/** @inheritDoc */
iep.actionRotationInstrument.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action de translation d'un instrument
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.instrumentAncetre} instrument : l'instrument à translater
 * @param {string} xfin : l'abscisse de fin de translation
 * @param {string} yfin : l'ordonnée de fin de translation
 * @param {type} tempo : le tempo (peut être null)
 * @param {type} pix10 : Le nombre de pixels par dixième de seconde pour la translation
 * Sera 
 */
iep.actionTranslationInstrument = function(doc,instrument,xfin,yfin,tempo,pix10) {
  iep.actionAncetre.call(this,doc,tempo);
  this.instrument = instrument;
  this.xfin = parseFloat(xfin);
  this.yfin = parseFloat(yfin);
  this.pix10 = (pix10 == null)? 8 : parseFloat(pix10);
};
iep.actionTranslationInstrument.prototype = new iep.actionAncetre();

iep.actionTranslationInstrument.prototype.execute = function(immediat) {
  if (isNaN(this.xfin) || isNaN(this.yfin)) this.doc.actionSuivante(immediat);
  else {
    if (immediat) this.instrument.translate(this.xfin,this.yfin);
    else this.instrument.lanceAnimationTranslation(this.xfin,this.yfin,this.pix10);
  }
};
/** @inheritDoc */
iep.actionTranslationInstrument.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Écarte le compas
 * @extends iep.actionAncetre
 * @constructor
 * @param doc
 * @param ecartement
 * @param tempo
 * @param ec10
 */
iep.actionEcarterCompas = function(doc,ecartement,tempo,ec10) {
  iep.actionAncetre.call(this,doc,tempo);
  this.ecartement = ecartement;
  this.ec10 = (ec10 === null) ? 16 : parseFloat(ec10);
  // this.doc.compas.ecart = this.ecartement;
};
iep.actionEcarterCompas.prototype = new iep.actionAncetre();
/**
 * Fonction lançant l'animation d'écartement du compas sauf si immediat est true
 * @param {boolean} immediat
 */
iep.actionEcarterCompas.prototype.execute = function(immediat) {
  if (!immediat) this.doc.compas.lanceAnimationEcartement(this.ecartement, this.ec10);
  else {
    this.doc.compas.ecart = this.ecartement;
    this.doc.compas.positionne();
  }
};
/** @inheritDoc */
iep.actionEcarterCompas.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.doc.compas,this.indice-1);
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action levant le compas
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 */
iep.actionLeverCompas = function(doc, tempo) {
  iep.actionAncetre.call(this,doc,tempo);
};
iep.actionLeverCompas.prototype = new iep.actionAncetre();

/**
 * Fonction cachant le compas normal et créant un compas couché qui est ensuite affiché
 * si le compas est visible 
 * @param {string} immediat : true dans le cas d'une exécution sans passage à l'action suivante
 */
iep.actionLeverCompas.prototype.execute = function(immediat) {
  var compas = this.doc.compas;
  if (compas.visible) {
    compas.montre(false);
    compas.visible = true; // Car a été modifié par montre()
  }
  this.doc.compasLeve = new iep.compasLeve(this.doc,compas.x,compas.y,
    compas.angle,compas.ecart);
  if (!immediat && compas.visible) this.doc.compasLeve.montre(true);
  compas.leve = true;
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionLeverCompas.prototype.actionVisible = function() {
  var visible = this.doc.getInstrumentVisibility(this.doc.compas,this.indice-1);
  if (!visible) return false;
  else return this.doc.getCompasStatus(this.indice-1) == "couche";
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action couchant le compas.
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc
 */
iep.actionCoucherCompas = function(doc, tempo) {
  iep.actionAncetre.call(this, doc, tempo);
};
iep.actionCoucherCompas.prototype = new iep.actionAncetre();

/**
 * Fonction couchant le compas et le montrant si immediat est false
 * @param {boolean} immediat
 */
iep.actionCoucherCompas.prototype.execute = function(immediat) {
  var compas = this.doc.compas;
  var compasLeve = this.doc.compasLeve;
  if (compas.leve) {
    compasLeve.montre(false);
    compas.setPosition(compasLeve.x,compasLeve.y,compasLeve.angle);
  }
  if (!immediat && compas.visible) compas.montre(true);
  compas.leve = false;
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionCoucherCompas.prototype.actionVisible = function() {
  var visible = this.doc.getInstrumentVisibility(this.doc.compas.compas,this.indice-1);
  if (!visible) return false;
  else return this.doc.getCompasStatus(this.indice-1) == "leve";
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

// Cette action agit sur les graduations externes du rapporteur
/**
 * Action montrant changeant le statut des gradiations externes du rapporteur
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {boolean} bmontrer : true si on montre les graduations, false sinon
 * @param {type} tempo
 */
iep.actionMontrerNombres = function(doc,bmontrer,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  this.bmontrer = bmontrer;
};
iep.actionMontrerNombres.prototype = new iep.actionAncetre();
/**
 * Exécution de l'action changeant le statut des gradiations externes du rapporteur
 * @param {boolean} immediat : si true on ne passe pas à l'action suivante
 */
iep.actionMontrerNombres.prototype.execute = function(immediat) {
  this.doc.rapporteur.montreGraduationsExternes(this.bmontrer);
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionMontrerNombres.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.doc.rapporteur,this.indice-1);
}

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action montrant ou masquant les graduations de l'instrument instrument
 * Attention : Pour la rèle, cette action s'exerce sur la graduation
 * et pour le rapporteur, sur la graduation interne
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {iep.instrumentAncetre} instrument : l'instrument possédant les graduations
 * @param {boolean} bmontrer : true pour montrer les graduations, false pour les cacher
 * @param {type} tempo
 */
iep.actionMontrerGraduations = function(doc,instrument,bmontrer,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  this.instrument = instrument;
  this.bmontrer = bmontrer;
};
iep.actionMontrerGraduations.prototype = new iep.actionAncetre();
/**
 * Fonction exécutant l'action donc montrant ou masquant les graduations de instrument
 * @param {booela} immediat Si true,pas de passage à l'action suivante
 */
iep.actionMontrerGraduations.prototype.execute = function(immediat) {
  this.instrument.montreGraduations(this.bmontrer);
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionMontrerGraduations.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Action de retournement du compas
 * @extends iep.actionAncetre
 * @constructor
 * @param {iepDoc} doc
 * @param {Integer} tempo
 */
iep.actionRetourner = function(doc,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
};
iep.actionRetourner.prototype = new iep.actionAncetre();

/**
 * Exécution de l'action de retournement du compas
 * @param {boolean} immediat : Si true on ne passe pas à l'action suivante
 */
iep.actionRetourner.prototype.execute = function(immediat) {
  this.doc.compas.retourne();
  if (!immediat) this.doc.actionSuivante(immediat);
};
/** @inheritDoc */
iep.actionRetourner.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.doc.compas,this.indice-1);
}/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action faisant glisser l'équerre de la règle équerre
 * @constructor
 * @extends iep.actionAncetre
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {int} abscisseFin : l'abscisse de fin de déplacement
 * @param {string} tempo : le tempo en dixième de seconde (ou null)
 * @param {string} pix10 : le nombre de pixels de déplacement à chaque dixième de seconde
 */
iep.actionGlisser = function(doc, abscisseFin,tempo,pix10) {
  iep.actionAncetre.call(this,doc,tempo);
  this.abscisseFin = parseFloat(abscisseFin);
  this.pix10 = (pix10 === null) ? 8 : String(Math.abs(pix10));
};
iep.actionGlisser.prototype = new iep.actionAncetre();
/**
 * Fonction lançant une animation de glissement de la règle équerre sauf si immediat est true
 * @param {type} immediat
 */
iep.actionGlisser.prototype.execute = function(immediat) {
  if (!immediat) this.doc.requerre.lanceAnimationGlissement(this.abscisseFin, this.pix10);
  else {
    this.doc.requerre.setAbs(this.abscisseFin);
    this.doc.requerre.positionne();
    // this.doc.actionSuivante(immediat);
  }
};
/** @inheritDoc */
iep.actionGlisser.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.doc.requerre,this.indice-1);
}

/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action translatant un objet
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc
 * @param {string} idel : L'id de l'objet à translater
 * @param {string} xfin : l'abscisse de fin de translation
 * @param {string} yfin : L'ordonnée de fin de translation
 * @param {string} type : le type de l'objet
 * @param {string} tempo : Le tempo de temporisation
 * @param {string} pix10 : Le nombre de pixels pour la translation par dixième de seconde. Peut être null
 */
iep.actionTranslationObjet = function(doc,idel,xfin,yfin,type,tempo,pix10) {
  iep.actionAncetre.call(this,doc,tempo);
  this.xfin = parseFloat(xfin);
  this.yfin = parseFloat(yfin);
  this.pix10 = (pix10 == null)? 8 : parseFloat(pix10);
  this.type = type;
  this.objet = this.doc.getElement(idel,type);
};
iep.actionTranslationObjet.prototype = new iep.actionAncetre();

/**
 * Fonction lançant la translation de objet sauf si immediat est false
 * @param {type} immediat
 */
iep.actionTranslationObjet.prototype.execute = function(immediat) {
  var objet = this.objet;
  if (objet == null) {
    this.doc.actionSuivante(immediat);
    return;
  }
  if (!immediat) objet.lanceAnimationTranslation(this.xfin, this.yfin, this.pix10);
  else {
    objet.x = this.xfin;
    objet.y = this.yfin;
    objet.positionne();
    // this.doc.actionSuivante(immediat);
  }
};
/** @inheritDoc */
iep.actionTranslationObjet.prototype.actionVisible = function() {
  return this.doc.getObjectVisibility(this.objet,this.indice-1);
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action de rotation d'un objet
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document proprétaire
 * @param {string} idel : l'id de l'objet à faire tourner
 * @param {string} anglefin : l'angle de fin de rotation
 * @param {string} type : le type de l'objet à faire tourner "image"
 * ou "texte" (la possibilité de tourner un texte n'était pas présnete
 * dans la version Flash)
 * car seules les images ont des objets que l'on peut faire tourner)
 * @param {string} tempo :  le tempo de temporisation (peut être null)
 * @param {string} deg10 : Le pas d'incrémentation de l'angle par dixième de seconde
 * Vaudra 8 si  null
 */
iep.actionRotationObjet = function(doc,idel,anglefin,type,tempo,deg10) {
  iep.actionAncetre.call(this,doc,tempo);
  this.anglefin = parseFloat(anglefin);
  this.deg10 = (deg10 == null) ? 8 : Math.abs(parseFloat(deg10));
  this.objet = this.doc.getElement(idel,type);
};
iep.actionRotationObjet.prototype = new iep.actionAncetre();

/**
 * Fonction lançant l'animation de l'objet seulement si immediat est false;
 * @param {boolean} immediat
 */
iep.actionRotationObjet.prototype.execute = function(immediat) {
  var objet = this.objet;
  if (objet == null) {
    if (!immediat) this.doc.actionSuivante(immediat);
    return;
  }
  if (!immediat) objet.lanceAnimationRotation(this.anglefin,this.deg10,this.tempo);
  else {
    objet.angle = this.anglefin;
    objet.positionne();
    // this.doc.actionSuivante(immediat);
  }
};
/** @inheritDoc */
iep.actionRotationObjet.prototype.actionVisible = function() {
  return this.doc.getObjectVisibility(this.objet,this.indice-1);
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action zoomant sur un objet
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : Le document propriétaire
 * @param {Stirng} idel
 * @param {string} zoomfin : L'id de l'objet sur lequel on zoome
 * @param {string} type : Le type de l'objet
 * @param {string} tempo : Le tempo de factorisation
 * @param {string} vitesse : Facteur de vitesse pour l'animation
 */
iep.actionZoomObjet = function(doc,idel,zoomfin,type,tempo,vitesse) {
  iep.actionAncetre.call(this,doc,tempo);
  this.objet = this.doc.getElement(idel,type);
  this.zoomfin = parseFloat(zoomfin)/100;
  this.vitesse = vitesse;
  this.type = type;
};
iep.actionZoomObjet.prototype = new iep.actionAncetre();
/**
 * Fonction lançant l'animation de zoom de l'objet sauf si immediat est false
 * @param {type} immediat
 */
iep.actionZoomObjet.prototype.execute = function(immediat) {
  var objet = this.objet;
  if (objet == null) {
    this.doc.actionSuivante(immediat);
    return;
  }
  if (!immediat) objet.lanceAnimationZoom(this.zoomfin, this.vitesse);
  else {
    objet.zoomfactor = this.zoomfin;
    objet.positionne();
    // this.doc.actionSuivante(immediat);
  }
};
/** @inheritDoc */
iep.actionTranslationObjet.prototype.actionVisible = function() {
  return this.doc.getObjectVisibility(this.objet,this.indice-1);
};/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action zoomant sur un instrument
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc
 * @param {iep.instrumentAncetre} instrument
 * @param {string} zoomfin : Le zoom de fin (de 0 à 100)
 * @param {string} tempo : Le tempo de factorisation
 * @param {string} vitesse : Facteur de rapidité de l'animation
 */
iep.actionZoomInstrument = function(doc,instrument,zoomfin,tempo,vitesse) {
  iep.actionAncetre.call(this,doc,tempo);
  this.instrument = instrument;
  this.zoomfin = parseFloat(zoomfin)/100;
  this.vitesse = parseInt(vitesse);
};
iep.actionZoomInstrument.prototype = new iep.actionAncetre();

iep.actionZoomInstrument.prototype.execute = function(immediat) {
  if (!immediat) this.instrument.lanceAnimationZoom(this.zoomfin, this.vitesse);
  else {
    this.instrument.zoomfactor = this.zoomfin;
    this.instrument.positionne();
    // this.doc.actionSuivante(immediat);
  }
};
/** @inheritDoc */
iep.actionZoomInstrument.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
var iep;
if (!iep) iep = {};

/**
 * Action d'écriture d'un texte. Le texte doit avoir été créé auparavant.
 * @constructor
 * @extends iep.actionAncetre
 * @param {iepDoc} doc            le document propriétaire
 * @param {string} id             l'id du texte sur lequel écrire
 * @param {string} couleur        la couleur du texte
 * @param {string} taille         la taille du texte
 * @param {string} texte          Le texte à afficher
 * @param {string} marge          la marge éventuelle
 * @param {string} margeGauche    la marge gauche éventuelle
 * @param {string} margeDroite    la marge droite éventuelle
 * @param {string} margeHaut      la marge haute éventuelle
 * @param {string} margeBas       la marge basse éventuelle
 * @param {string} tempo          le tempo
 */
iep.actionEcrireTexte = function(doc,id,couleur,taille,texte,style,couleurFond,
  opaciteFond,couleurCadre,epaisseurCadre,marge,margeGauche,margeDroite,margeHaut,margeBas,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  this.txt = this.doc.getElement(id,"texte");
  this.id = id;
  this.couleur = couleur;
  // On remplace dans la taille les lettres O mises par les étourdis à la place de 0
  this.taille = (taille==null) ? "20" : taille.replace(/O/g,"0");
  this.texte = iep.remplaceAccentsHtml(iep.remplaceBalises(iep.remplaceCarSpe(texte)));
  this.style = (style === null) ? "texte" : style;
  this.couleurFond = couleurFond;
  this.opaciteFond = (opaciteFond == null) ? "0.6" : parseFloat(opaciteFond)/100;
  // Modification version 5.0.2. Causait des problèmes avec explorer
  // this.couleurCadre = (couleurCadre == null) ? couleur : couleurCadre;
  this.couleurCadre = couleurCadre;
  if ((epaisseurCadre !== null) && (couleurCadre === null)) this.couleurCadre = couleur;
  //
  this.epaisseurCadre = (epaisseurCadre != null) ? epaisseurCadre : ((couleurCadre == null) ? 0 : 1);
  this.marge = (marge == null) ? 0 : parseFloat(marge);
  this.margeGauche = (margeGauche == null) ? 0 : parseFloat(margeGauche);
  this.margeDroite = (margeDroite == null) ? 0 : parseFloat(margeDroite);
  this.margeHaut = (margeHaut == null) ? 0 : parseFloat(margeHaut);
  this.margeBas = (margeBas == null) ? 0 : parseFloat(margeBas);
  this.estLatex = this.estAffichageLatex();
  this.texte = this.traduitCaracteresGrecs(this.texte);
  if (! this.estLatex) {
    this.texte = this.texte.replace(/\*/g,"×");
    if (iep.necessiteLatex(texte)) {
      this.texte = "$" + this.traiteMaths(this.texte) + "$";
      this.estLatex = true;
    }
    else this.texte = iep.traiteAccents(this.texte);
  }
};

iep.actionEcrireTexte.prototype = new iep.actionAncetre();
/**
 * Action écrivant le contenu du texte sur la figure
 * @param {Boolean} immediat
 * @returns {undefined}
 */
iep.actionEcrireTexte.prototype.execute = function(immediat) {
  if (this.txt != null) { // Pour éviter les erreurs des auteurs
    this.angle = 0; // Car on peut dans la version js faire toruner un texte
    var txt = this.txt;
    txt.couleur = this.couleur;
    txt.taille = this.taille;
    txt.texte = this.texte;
    txt.couleurFond = this.couleurFond;
    txt.couleurCadre = this.couleurCadre;
    txt.epaisseurCadre = this.epaisseurCadre;
    txt.updateg(this.g);
    txt.positionne();
  }
  if (!immediat) this.doc.actionSuivante(immediat);
};

/**
 * Fonction renvoyant true si l'affichage de texte utilise le LaTeX de façon délibérée
 * en encadrant alors le code LaTeX par deux caractères $
 * A priori ne sera utilisé que pour une prochaine version
 * @returns {boolean}
 */
iep.actionEcrireTexte.prototype.estAffichageLatex = function() {
  return ((this.texte.charAt(0)==="$") && (this.texte.charAt(this.texte.length-1)==="$"));
}


/**
 * Fonction remplaçant les codes du type £...£ représentant des caractères grecs
 * par les caractères UTF8 correspondants
 * @param {type} ch
 * @returns {String}
 */
iep.actionEcrireTexte.prototype.traduitCaracteresGrecs = function(ch) {
  var i,ta,search;
  ta = iep.caracteresGrecs;
  if (ch.indexOf("£") !== -1) {
    for (i = 0; i < ta.length; i++) {
      search = "£"+ta[i]+"£";
      if (ch.indexOf(search) !== -1) ch = ch.replace(new RegExp(search,"g"),iep.caracteresGrecsUtf8[i]);
    }
  }
  return ch;
}
/**
 * Fonction renvoyant true si ch conteint une balise LaTeX : fraction, racine carrée,
 * puissance, indice, chapeau, parenthèses, crochets, valeur abolue, norme,
 * vecteur ou texte
 * @param {type} ch
 * @returns {Boolean}
 */
iep.contientBaliseLaTeX = function(ch) {
  return (ch.indexOf("\\frac") !== -1) || (ch.indexOf("\\sqrt") !== -1) ||
    (ch.indexOf("}^{")!== -1) || (ch.indexOf("}_{") !== -1) ||
    (ch.indexOf("\\widehat{") !== -1) || (ch.indexOf("\\left[") !== -1) ||
    (ch.indexOf("\\left(") !== -1) || (ch.indexOf("\\left|") !== -1) ||
    (ch.indexOf("\\left\\|") !== -1) || (ch.indexOf("\\overrightarrow{") !== -1) ||
    (ch.indexOf("\\text{") !== -1);
};
/**
 * Fonction traitant la chaîne ch pour la transcrire en code LaTeX
 * Renvoie la chaîne LaTeX correspondante.
 * Si la chaîne contient des balises <br>, un tableau LaTeX est utilisé pour rendre le contenu
 * @param {string} ch : la chaîne à traiter
 * @returns {string} la chaîne LaTeX correpondante une fois ch traduite
 */
iep.actionEcrireTexte.prototype.traiteMaths = function(ch) {
  var i;
  var ch2 = ch;
  var bStyletTexte = this.style === "texte";
  var a = ch2.split(new RegExp("<br>|</br>","gi")); // On recherche les codes <br> et on remplace les lignes par une matrice
  if (a.length <= 1) return iep.traiteAccents(iep.getMaths(ch,bStyletTexte)); // True pour mode texte par défaut
  var res = "\\begin{array}{l}";
  for (var i = 0; i < a.length; i++) {
    if (i !== 0) res += "\\\\";
    res += iep.traiteAccents(iep.getMaths(a[i], bStyletTexte)); // true pour mode texte par défaut
  }
  res += "\\end{array}";
  return res;
};
/**
 * Fonction appelée lors de l'appel de iepDoc.creeActions()
 * Si l'écriture du texte ne nécessite pas l'emploi du LaTeX, on crée l'élément grapique qui sera
 * démasqué quand l'action sera exécutée
 * Sinon, on crée un div provisoire dans la figure dans lequel on met la formule et on demande à MathJax via sa pile d'appels
 * de traiter ce div pour le remplacer par un svg
 * Via la pile d'appel de MathJax, on apelle la fonction callBack qui sera appelée
 * une fois le svg représentant la figure prêt . Cette fonction de callback appelera ensuite
 * iepDoc.waitForReadyState sui est la fonction attendant que tout les objets asynchrones soient prêts
 * (images et affichages de texte utilisant le LaTeX)
 */
iep.actionEcrireTexte.prototype.prepare = function() {
  if (!this.estLatex) {
    this.creeg();
    this.setReady();
  }
  else {
    this.div = document.createElement("div");
    // On diminue la taille pour le LaTeX eet on ne spécifie pas la taille en px
    // différence par rapport à MathGraph32
    this.div.setAttribute("style", "top:0px;left:0px;position:absolute;font-size:" +
        String(this.taille-2) + "px;visibility:hidden;");
    this.div.setAttribute("id", this.id+"iepprov");
    var ch  = "$$" + this.prelatex() + this.texte.substring(1) + "$";
    this.div.appendChild(document.createTextNode(ch));
    document.body.appendChild(this.div);
    // MathJax.Hub.Typeset(this.div);
    MathJax.Hub.Queue(["Typeset",MathJax.Hub,this.div]);
    var t = this;
    MathJax.Hub.Queue([function() {t.creegLatex();}]);
    MathJax.Hub.Queue([this.callBack,this]);
  }
};
/**
 * Fonction de callback qui sera appelée quand MathJax aura traité le code LaTeX
 * @param {type} action
 */
iep.actionEcrireTexte.prototype.callBack = function(action) {
  action.setReady();
  action.doc.waitForReadyState();
};
/**
 * Fonction renvoyant l'en-tête à rajouter pour que, dans le cas d'utilisation du
 * LaTeX, l'affichage soit de la bonne couleur.
 */
iep.actionEcrireTexte.prototype.prelatex = function() {
  return "\\color{"+this.couleur+"}";  
};

/**
 * Fonction appelée par prepare() qui récupère l'élement svg graphique représentant
 * la formule dans le div provisoire précédemment créé, détruit ensuite ce div provisoire
 * Rajoute les éléments graphiques correspondant à un cadre si une couleur de fond
 * et un cadre ont été demandés
 */
iep.actionEcrireTexte.prototype.creegLatex = function() {
  var w,h,ratio;
  try {
    var g = document.createElementNS(iep.svgsn,"g");
    var c1 = this.div.childNodes[1];
    if (c1 == undefined) return document.createElementNS(iep.svgsn,"g");
    var c2 = c1.childNodes[0];
    if (c2 == undefined) return document.createElementNS(iep.svgsn,"g");
    var s = c2.childNodes[0];
    if (s == undefined) return document.createElementNS(iep.svgsn,"g");
    // Pour gérer Chrome
    while(s.tagName === "SPAN") s = s.childNodes[0];
    // Le test suivant est du à la compatibilité avec l'explorer
    if ((s.clientWidth != 0) && (s.clientHeight != 0)) {
      w = s.clientWidth;
      h = s.clientHeight;
    }
    else {
      /* Modifié version 5.0.2
      var b = s.getBBox();
      ratio = b.height/b.width;
      w = this.div.clientWidth;
      h = w*ratio;
      */
      w = c2.clientWidth;
      h = c2.clientHeight;
    }
    var t = this.taille;
    if (h < t) h = t;
    /* Modifié version 5.0.2 pour assurer compatibilité avec explorer
    s.setAttribute("x","0");
    s.setAttribute("y", String(-this.taille)); // Différent de MathGraph32
    s.setAttribute("width", w+"px");
    s.setAttribute("height",h+"px");
    g.appendChild(s.parentNode.removeChild(s));
    */
    var clone = s.cloneNode(true);
    clone.setAttribute("x","0");
    clone.setAttribute("y", String(-this.taille)); // Différent de MathGraph32
    clone.setAttribute("width", w+"px");
    clone.setAttribute("height",h+"px");
    g.appendChild(clone);
    document.body.removeChild(this.div);
    g.setAttribute("visibility","hidden");
    g.setAttribute("id",this.id);
    if ((this.couleurFond !== null) || (this.couleurCadre !== null)) {
      g.setAttribute("visibility", "hidden");
      this.doc.svg.appendChild(g);
      this.rectAff = new Object();
      var epc = parseFloat(this.epaisseurCadre);
      this.rectAff.height = g.getBBox().height + 2*epc + 4 + 2*this.marge+this.margeHaut+this.margeBas;
      this.rectAff.width = g.getBBox().width + 2*epc + 2 + 2*this.marge+this.margeGauche+this.margeDroite;
      this.rectAff.x  = g.getBBox().x-epc-1-this.marge-this.margeGauche;
      this.rectAff.y  = g.getBBox().y-epc-1-this.marge-this.margeHaut;
      g.setAttribute("visibility", "visible");  
      this.doc.svg.removeChild(g);
      this.creeRectangle(g,(this.couleurFond == null) ? "white" : this.couleurFond);
    }
    this.g = g;
  }
  catch(e) {
    if (this.div != null) document.body.removeChild(this.div);
    this.g = document.createElementNS(iep.svgsn,"g"); //Crée un g vide en cas de problème
  }
};

/**
 * Retourne l'élément graphique associé dans le cas où l'affichage n'utilise pas le LaTeX
 * Avant appel, this.text doit voir été affecté
 */
iep.actionEcrireTexte.prototype.creeg = function() {
  var indbalise,tspan,ch,ch2,style,txt,ind,indexp,indind,mini,bexp,tailleind,stylespan,
          y,sp,i,debutLigne,an;
  var dy = 0; // Le baselineshift de chaque tspan
  var inf = "<"; // Equivalent du symbole <
  var debexp = "£e(";
  var debind = "£i(";
  var g = document.createElementNS(iep.svgsn,"g");
  var hautlig = parseFloat(this.taille)+2;
  var decblp = 0; // Décalage vers le bas de la ligne précédente
  var decalage = parseFloat(this.taille)*0.4; // Décalage vers le bas ou le haut en cas d'indice ou d'exposant
 
  if (this.texte != "" ) {
    txt = document.createElementNS(iep.svgsn,"text");
    txt.setAttribute("pointer-events", "none");
    txt.setAttribute("x",0);
    txt.setAttribute("y",0);
    var style = "text-anchor:left;font-size:" + this.taille +"px;" + "fill:" + this.couleur+";";
    txt.setAttribute("style",style);
    // On remplace les espaces par des espaces insécables
    ch2 = this.texte.replace(new RegExp(" ","g"),"\u00A0");
    // Affecter une longueur ne peut être efficace que pour du texte simpel sans balise
    if ((this.texte.indexOf(inf) === -1) && (this.texte.indexOf(debexp) === -1) && (this.texte.indexOf(debind) === -1)
      && (this.texte.indexOf("<br>") === -1)) {
      txt.appendChild(document.createTextNode(ch2));
    }
    else {
      sp = ch2.split(/<br>/gi);
      y = -hautlig;
      for (i = 0; i < sp.length; i++) {      
        debutLigne = true;
        ch = sp[i];
        while (ch != "") {
          indbalise = ch.indexOf(inf);
          indexp = ch.indexOf(debexp);
          indind = ch.indexOf(debind);
          if (debutLigne) y += hautlig + decblp;
          if (indexp !== -1) y += decalage;
          if ((indbalise === -1) && (indexp === -1) && (indind === -1)) {
            //txt.appendChild(document.createTextNode(this.texte));
            tspan = document.createElementNS(iep.svgsn,"tspan");
            tspan.setAttribute("pointer-events", "none");
            tspan.setAttribute("dy",dy);
            if (debutLigne){
              tspan.setAttribute("x",0);
              tspan.setAttribute("y",y);
              debutLigne = false;
            }
            dy = 0; // On est revenu au niveau 0
            tspan.appendChild(document.createTextNode(ch))
            txt.appendChild(tspan);
            break;
          }
          else {
            while ((ch.indexOf(inf) !== -1) || (ch.indexOf(debexp) !== -1)
                    || (ch.indexOf(debind) !== -1)) {
              indbalise = ch.indexOf(inf);
              indexp = ch.indexOf(debexp);
              indind = ch.indexOf(debind);
              if (indbalise === -1) {
                if (indexp === -1) mini = indind;
                else mini = (indind === -1) ? indexp : Math.min(indind,indexp);
              }
              else {
                if (indexp === -1) mini = (indind === -1) ? indbalise : Math.min(indbalise,indind);
                else {
                  if (indind === -1) mini = Math.min(indbalise,indexp);
                  else mini = Math.min(indbalise,indind,indexp);
                }
              }
              if (mini>0) { // Il y a du texte avant les balises
                tspan = document.createElementNS(iep.svgsn,"tspan");
                tspan.setAttribute("pointer-events", "none");
                tspan.setAttribute("dy",dy);
                if (debutLigne){
                  tspan.setAttribute("x",0);
                  tspan.setAttribute("y",y);
                  debutLigne = false;
                }
                dy = 0; // On est revenu au niveau 0
                tspan.appendChild(document.createTextNode(ch.substring(0,mini)))
                txt.appendChild(tspan);
                ch = ch.substring(mini);
              }
              else {
                if (indbalise === 0) {
                  var infoBalise = new iep.infoBalise(false,false,false,this.couleur,"",this.taille);
                  ind = iep.indiceFinBalise(ch);
                  if (ind === -1) iep.traiteBalise(ch,infoBalise,txt,debutLigne,y);
                  else iep.traiteBalise(ch.substring(0,ind),infoBalise,txt,debutLigne,y);
                  debutLigne = false;
                  if (ind !== -1) ch = ch.substring(ind); else ch = "";
                  dy = 0; // On est revenu au niveau 0

                }
                else { // mini est égal à 0
                  bexp = mini === indexp;
                  an = iep.analyseExposantOuIndice(ch);
                  tspan = document.createElementNS(iep.svgsn,"tspan");
                  tspan.setAttribute("pointer-events", "none");
                  tspan.setAttribute("dy",dy+"px");
                  if (debutLigne){
                    tspan.setAttribute("x",0);
                    tspan.setAttribute("y",y);
                    debutLigne = false;
                  }
                  if (an.erreur) tspan.appendChild(document.createTextNode(ch));
                  else {
                    tspan.appendChild(document.createTextNode(an.operande));
                    txt.appendChild(tspan);
                    tspan = document.createElementNS(iep.svgsn,"tspan");
                    tspan.setAttribute("pointer-events", "none");
                    tspan.appendChild(document.createTextNode(an.exposant));
                    dy = decalage;
                    if (bexp) dy = -dy;
                    else decblp = dy;
                    tspan.setAttribute("dy",dy+"px");
                    tailleind = parseFloat(this.taille)*0.6;
                    stylespan = "font-size:"+tailleind+"px;";
                    tspan.setAttribute("style",stylespan);
                    txt.appendChild(tspan);
                    dy = -dy; //Car sinon la suite sera décalée aussi en hauteur
                  }
                  ch = an.texte;
                }
              }
            }
          }
        }
      }
    }
    g.appendChild(txt);
  }
  g.setAttribute("visibility","hidden");
  g.setAttribute("id",this.id);
  if ((this.couleurFond != null) || (this.couleurCadre != null)) {
    g.setAttribute("visibility", "hidden");
    this.doc.svg.appendChild(g);
    this.rectAff = new Object();
    var epc = parseFloat(this.epaisseurCadre);
    this.rectAff.height = g.getBBox().height + 2*epc + 3 + 2*this.marge + this.margeHaut + this.margeBas;
    this.rectAff.width = g.getBBox().width + 2*epc + 2 +2*this.marge + this.margeGauche + this.margeDroite;
    this.rectAff.x  = g.getBBox().x-epc-1-this.marge-this.margeGauche;
    this.rectAff.y  = g.getBBox().y-epc-1-this.marge-this.margeHaut;
    // this.doc.svg.removeChild(g);
    g.setAttribute("visibility", "visible");  
    this.doc.svg.removeChild(g);
    this.creeRectangle(g,(this.couleurFond == null) ? "white" : this.couleurFond);
  }
  this.g = g;
};

/**
 * Renvoie true si la chaîne ch contient des balises &lt;u\> ou \<i\>
 * @param {string} ch
 * @returns {boolean}
 */
iep.actionEcrireTexte.prototype.contientBalisesItaliqueOuUnderlineOuFontOuBold = function(ch) {
  var regex = new RegExp(/<(u|b|i|font[^>]+)>/, 'gi');
  return regex.test(ch);
}

/**
 * Fonction appelée dans le cas où l'affihcage de texte a une couleur de fond ou doit être encadrée.
 * Elle rajoute le rectangle au g élément avant l'afficgae correspondant au texte (de faaçon à pouvoir
 * le cas échéant effacer le fond avant l'affichage du texte
 * @param {svg.g} : le svg.g élément qui contient l'affichae de texte (normal ou svg dans le cas du LaTeX
 */
iep.actionEcrireTexte.prototype.creeRectangle = function(g,coulFond) {
  var r = document.createElementNS(iep.svgsn,"rect");
  var style = "stroke-width:"+this.epaisseurCadre+"px;" + "stroke:" + this.couleurCadre + ";"+
    "fill:" + coulFond + ";fill-opacity:" + this.opaciteFond + ";";
  r.setAttribute("style", style);
  r.setAttribute("x", this.rectAff.x);
  r.setAttribute("y", this.rectAff.y);
  r.setAttribute("width", this.rectAff.width);
  r.setAttribute("height", this.rectAff.height);
  r.setAttribute("pointer-events", "none");
  // r.setAttribute("transform","translate("+this.rectAff.x+","+this.rectAff.y+")");
  g.insertBefore(r, g.childNodes[0]);
};


// On de redéfinit pas actionVisible() car cette action est toujours visible
/**
 * Fontion qui, dans le cas d'utilisation du LaTeX, remplace les caractères accentués
 * qui ne sont pas bien traités par MathJax par des codes LaTeX
 * @param {string} ch
 * @returns {string|iep.traiteAccents.st} : La chaîne avec les caractères remplacés
 */
iep.traiteAccents = function(ch) {
  var indpf,st,i,car,car2,bool;
  var a = "éèàùâêô";
  var b = ["acute","grave","grave","grave","hat","hat","hat"];
  var c = "eeauaeo";
  // On traite les £virg£ et £virg£
  ch = ch.replace(/£virg£/gi,",");
  ch = ch.replace(/£virgule£/gi,",");
  bool = false; 
  for (i = 0; i < a.length;i++) bool = bool || (ch.indexOf(a.charAt(i)) != 0);
  if (!bool || ch.indexOf("\\text{") == -1) return ch;
  var res = "";
  var ind = -1;
  var inddeb = 0;
  while (((ind = ch.indexOf("\\text{", inddeb)) != -1) && (inddeb < ch.length)) {
    indpf = iep.accoladeFermante(ch, ind+ 6);
    if (indpf === -1) break;
    res = res + ch.substring(inddeb, ind);
    st = ch.substring(ind+6, indpf + 1);
    for (i = 0; i < a.length;i++) {
      car = a.charAt(i);
      car2 = c.charAt(i);
      if (st.charAt(0) === car) st = "\\"+b[i]+"{"+car2+"}" +"\\text{"+st.substring(1);
      if (st.charAt(st.length-1) === car)
        st = st.substring(0,st.length-1)+"\\"+b[i]+"{"+car2+"}";
      st = st.replace(new RegExp(car,"g"), "}\\"+b[i]+"{"+car2+"} \\text{");
    }
    // Impossible de traite le c cédille
    // // Pour le lecteur IEP je laisse comme ça
    // st = st.replace(new RegExp("ç","g"),"c");
    bool = false;
    for (i = 0; i < a.length;i++) bool = bool || (st.indexOf("\\"+b[i]) === 0);
    if (bool) res = res + st;
    else res = res+"\\text{"+st;
    inddeb = indpf + 1;
  }
  if (inddeb < ch.length) res = res + ch.substring(inddeb);
  return res;
}
/**
 * Fonction renvoyant l'idice dans la chaîne chaine de l'accolade fermante
 * correspondant à l'accolade ouvrante dont l'indice dans la chaîne est pdebut
 * Avant appel,l le caractère d'indice pdebut de la chaîne ch doit contenir
 * une parenthèse ouvrante.
 */
iep.accoladeFermante = function(chaine, pdebut) {
  var p;
  var ch;
  var somme;

  somme = 1;
  p = pdebut + 1;
  while (p < chaine.length) {
    ch = chaine.charAt(p);
    if (ch == '{')
      somme++;
    else {
      if (ch == '}')
        somme--;
    }
    if (somme == 0) break;
    p++;
  }
  if (somme == 0) return p;
  else return - 1; // On renvoie -1 si pas trouvé
};
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action donnant un nom à un point
 * @extends iep.actionAncetre
 * @constructor
 * @param {ie.iepDoc} doc : le doculent propriétaire
 * @param {iep.nomPoint} objet :  l'objet représentant le nom
 * @param {string} tempo : le tempk en dixièmes de seconde (ou null)
 */
iep.actionNommerPoint = function(doc,objet,tempo) {
  iep.actionAncetre.call(this,doc,tempo);
  this.nomPoint = objet;
};
iep.actionNommerPoint.prototype = new iep.actionAncetre();
iep.actionNommerPoint.prototype.prepare = function() {
  if (!this.nomPoint.estLatex) {
    this.nomPoint.creeg();
    this.setReady();
  }
  else {
    this.nomPoint.div = document.createElement("div");
    // On diminue la taille pour le LaTeX eet on ne spécifie pas la taille en px
    // différence par rapport à MathGraph32
    this.nomPoint.div.setAttribute("style", "top:0px;left:0px;position:absolute;font-size:" +
        String(this.taille-2) + "px;visibility:hidden;");
    this.nomPoint.div.setAttribute("id", this.nomPoint.id+"iepprov");
    var ch  = "$$" + this.nomPoint.nom.substring(1) + "$";
    this.nomPoint.div.appendChild(document.createTextNode(ch));
    document.body.appendChild(this.nomPoint.div);
    // MathJax.Hub.Typeset(this.div);
    MathJax.Hub.Queue(["Typeset",MathJax.Hub,this.nomPoint.div]);
    var t = this;
    MathJax.Hub.Queue([function() {t.nomPoint.creegLatex();}]);
    MathJax.Hub.Queue([this.callBack,this]);
  }
};

/**
 * Exécutant de l'action donnant un nom au point
 * @param {boolean} immediat : Si true pas de passage à l'action suivante
 */
iep.actionNommerPoint.prototype.execute = function(immediat) {
  // if ((this.nomPoint.objet === "point") && !this.nomPoint.nomCree) {
  if (this.nomPoint.objet === "point") {
    // this.nomPoint.creeg();
    this.nomPoint.positionne();
    var point = this.nomPoint.point;
    if (point.nom !== null) 
      this.doc.svg.replaceChild(this.nomPoint.g, point.nom.g);
    else this.doc.svg.appendChild(this.nomPoint.g);
    point.nom = this.nomPoint;
  }
  this.nomPoint.g.setAttribute("visibility", "visible");
  if (!immediat) this.doc.actionSuivante(immediat);
};
/**
 * Fonction de callback qui sera appelée quand MathJax aura traité le code LaTeX
 * @param {type} action
 */
iep.actionNommerPoint.prototype.callBack = function(action) {
  action.setReady();
  action.doc.waitForReadyState();
};

/** @inheritDoc */
iep.actionNommerPoint.prototype.actionVisible = function() {
  return this.doc.getObjectVisibility(this.nomPoint.point,this.indice-1);
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */
/**
 * Action faisant une pause dans l'animation de la figure
 * Pour continuer l'action  l'utilisation doit cliquer sur le bouton rouge dans
 * la barre d'icônes
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} doc : le document propriétaire
 * @param {string} tempo : le tempo de temporisation (ou null)
 */
iep.actionPause = function(doc, tempo) {
  iep.actionAncetre.call(this,doc,tempo); 
};
iep.actionPause.prototype = new iep.actionAncetre();
/**
 * Exécute l'action en suspendant l'animation en cours.
 * Pour continuer, l'utilisateur doit cliquer sur le bouton roouge.
 * @param {type} immediat
 */
iep.actionPause.prototype.execute = function(immediat) {
  if (!immediat) {
    this.doc.activeIconeContinuer();
    this.doc.animationEnCours = false;
  }   
};
/** @inheritDoc */
iep.actionPause.prototype.actionVisible = function() {
  return false;
}
/* 
 * Visualiseur InstrumenPoche en Javascript et SVG
 * @Author Yves Biton (yves.biton@sesamath.net)
 * @License: GNU AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 */

/*
 * Action modifiant la longueur d'un instrument.
 * Pour le moment seulement implémenté pour la règle.
 */

/**
 * Action modifiant la longueur d'un instrument.
 * Pour le moment seulement implémenté pour la règle.
 * @extends iep.actionAncetre
 * @constructor
 * @param {iep.iepDoc} Le document propriétaire
 * @param {iep.instrumentAncetre} instrument : L'istrument sur lequel a lieu l'action
 * @param {string} longueur : La nouvelle longueur
 * @param {string} tempo : Le tempo de temporisation
 */
iep.actionModifierLongueur = function(doc,instrument,longueur,tempo) {
  iep.actionAncetre.call(this,doc,tempo); // null car action immédiate
  this.instrument = instrument;
  this.longueur = longueur;
};
iep.actionModifierLongueur.prototype = new iep.actionAncetre();
/**
 * Fonction exécutant l'action et modifiant la longueur de l'instrument
 * @param {boolean} immediat : true si l'action est immdiate et on ne passe
 * pas ensuite à l'action suivante
 */
iep.actionModifierLongueur.prototype.execute = function(immediat) {
  if (this.instrument === this.doc.regle) {
    var vis = this.instrument.visible;
    this.instrument.longueur = this.longueur;
    var oldg = this.instrument.g;
    this.instrument.creeg();
    this.instrument.g.setAttribute("visibility",vis ? "visible" : "hidden");
    this.doc.svg.replaceChild(this.instrument.g,oldg);
    this.instrument.translate(this.instrument.x,this.instrument.y);
  }
  if (!immediat) this.doc.actionSuivante(immediat); 
};
/** @inheritDoc */
iep.actionModifierLongueur.prototype.actionVisible = function() {
  return this.doc.getInstrumentVisibility(this.instrument,this.indice-1);
}
