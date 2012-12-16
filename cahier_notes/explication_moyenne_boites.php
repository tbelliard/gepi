<?php
echo "<p>Les ".getSettingValue("gepi_denom_boite")."s peuvent avoir deux usages, deux modes&nbsp;:</p>
<ul>
<li>
Dans un cas, les ".getSettingValue("gepi_denom_boite")."s ne sont que des rangements, sans influence sur la moyenne.<br />
Tous les devoirs sont pris en compte avec leurs coefficients.<br />
C'est tout.<br />
Les coefficients saisis pour les ".getSettingValue("gepi_denom_boite")."s ne sont pas pris en compte.</li>
<li>Dans l'autre cas, on fait la moyenne dans chaque ".getSettingValue("gepi_denom_boite").", puis on fait une moyenne des ".getSettingValue("gepi_denom_boite")."s en tenant compte des coefficients des ".getSettingValue("gepi_denom_boite")."s.</li>
</ul>

<p><em>Exemple&nbsp;:</em> On considère les notes obtenues par l'élève Toto dans la configuration suivante&nbsp;:</p>
<pre style='background-color:white;'>
racine
|-- <span style='color:red'>Devoirs_en_classe - ".getSettingValue("gepi_denom_boite")." coefficient 3</span>
|   |-- <span style='color:plum'>Controle  - coefficient 1 - Note obtenue : 15</span>
|   |-- <span style='color:orange'>DS1       - coefficient 2 - Note obtenue : 12</span>
|   `-- <span style='color:lightblue'>DS2       - coefficient 2 - Note obtenue : 13</span>
`-- <span style='color:blue'>Devoirs_maison - ".getSettingValue("gepi_denom_boite")." coefficient 1</span>
    |-- <span style='color:green'>DM1        - coefficient 2 - Note obtenue : 16</span>
    `-- <span style='color:lime'>DM2        - coefficient 1 - Note obtenue : 17</span>
</pre>

<p>Avec le premier mode, le calcul de la moyenne serait&nbsp;:<br />
<span style='background-color:white;'>
m = (<span style='color:plum'>1*15</span> + <span style='color:orange'>2*12</span> + <span style='color:lightblue'>2*13</span> + <span style='color:green'>2*16</span> + <span style='color:lime'>1*17</span>)/(<span style='color:plum'>1</span> + <span style='color:orange'>2</span> + <span style='color:lightblue'>2</span> + <span style='color:green'>2</span> + <span style='color:lime'>1</span>) = 14.25
</span>
</p>

<br />

<p>Avec le deuxième mode, le calcul de la moyenne serait&nbsp;:<br />

Moyenne de la première ".getSettingValue("gepi_denom_boite")." (<em>Devoirs_en_classe</em>)&nbsp;: 
<span style='background-color:white;'>
<span style='color:red'>m<sub>1</sub></span> = (<span style='color:plum'>1*15</span> + <span style='color:orange'>2*12</span> + <span style='color:lightblue'>2*13</span>)/(<span style='color:plum'>1</span> + <span style='color:orange'>2</span> + <span style='color:lightblue'>2</span>)=<span style='color:red'>13</span>
</span>
<br />

Moyenne de la deuxième ".getSettingValue("gepi_denom_boite")." (<em>Devoirs_maison</em>)&nbsp;: 
<span style='background-color:white;'>
<span style='color:blue'>m<sub>2</sub></span> = (<span style='color:green'>2*16</span> + <span style='color:lime'>1*17</span>)/(<span style='color:green'>2</span> + <span style='color:lime'>1</span>)&approx;<span style='color:blue'>16.33</span>
</span>
<br />

Moyenne de l'ensemble&nbsp;: 
<span style='background-color:white;'>
m = (<span style='color:red'>3*m<sub>1</sub></span> + <span style='color:blue'>1*m<sub>2</sub></span>)/(<span style='color:red'>3</span> + <span style='color:blue'>1</span>)&approx;(<span style='color:red'>3*13</span> + <span style='color:blue'>1*16.33</span>)/(<span style='color:red'>3</span> + <span style='color:blue'>1</span>)&approx;13.83
</span>
</p>
<p><br /></p>
<p>Le paramétrage d'un mode ou l'autre sur un Carnet de notes se fait en <strong>Interface complète</strong> en suivant le lien <strong>Configuration</strong> de la racine du Carnet de notes.</p>";
?>

