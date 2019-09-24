<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
?>
[onload;file=menu.php]

<h2>Import CSV type 1</h2>
<div style='margin-left:3em;'>
	<p>Vous allez mettre en place les correspondances entre les logins de Gepi et ceux de votre ENT d'après les noms et les prénoms, controlez dans l'aide les contraintes sur ce fichier :</p>
	<form action="index.php?ctrl=cvsent&action=result" enctype='multipart/form-data' method="post">
	<fieldset class='fieldset_opacite50'>
		<p>
			<input type="radio" name="choix" value="erreur" id='choix_erreur' checked="checked" /><label for='choix_erreur'>Recherche des erreurs : seules les erreurs sont affichées, aucune donnée n'est écrite dans la base</label><br/>
			<input type="radio" name="choix" id='choix_test' value="test" /><label for='choix_test'>Test : toutes les entrées sont listées avec leur état, aucune donnée n'est écrite dans la base</label><br/>
			<input type="radio" name="choix" id='choix_ecrit' value="ecrit" /><label for='choix_ecrit'>Inscription dans la base : toutes les entrées sont traitées puis listées avec leur état. Les données sont écrites dans la base</label><br/>
		</p>
		<p class="title-page">Veuillez fournir le fichier csv&nbsp;:</p>
		<p>
			<input type='file'  name='fichier'  />

		<input type='submit' value='Téléchargement' />
		</p>
	</fieldset>
	</form>

	<p style='margin-top:1em; text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em> Le fichier ENT attendu doit se nommer ENT-Identifiants.csv ou ENT-Identifiants-<strong>RNE</strong>.csv<br />C'est le cas n°<strong>3</strong> détaillé dans l'onglet <strong><a href='index.php?ctrl=help#csv'>Aide</a></strong>.</p>

</div>

<hr />

<h2>Import CSV type 2</h2>
<div style='margin-left:3em;'>
	<form action='traite_export_csv.php' method='post' enctype='multipart/form-data'>
	<fieldset class='fieldset_opacite50'>
		<p style='margin-top:1em; text-indent:-4em; margin-left:4em;'>Si au lieu d'un export CSV avec les 13 champs attendus, vous disposez d'un export CSV du type&nbsp;:<br />
	Civilité;Nom;Prénom;Profil;Login;Identifiant ENT;Etablissement;<br />
	Mme;DUGENOU;CORINNE;Professeur;corinne.dugenou;QAA12345;Collège Jacques Brel;<br />
	M.;LECERCLE;JEROME;Professeur;jerome.lecercle;QAA45678;Collège Jacques Brel;<br />
	...
		</p>

		<p class="title-page">Veuillez fournir le fichier csv&nbsp;:</p>
		<p>
			<input type='file' name='fichier' />

			<input type='hidden' name='mode' value='upload' />
			<input type='submit' value='Téléchargement' />
		</p>

		<p style='margin-top:1em; text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
		<ul>
			<li>
				<p>Le recollement entre les informations ENT et celles de Gepi se fera sur les nom et prénom des utilisateurs.<br />
				Seuls les champs <strong>Nom;Prénom;Identifiant ENT</strong> sont requis dans ce CSV.</p>
			</li>
			<li>
				<p>Ce genre de situation peut arriver si par exemple, pour les personnels, l'authentification passe par le serveur LDAP académique (<em>donnant accès aussi au webmail,...</em>).<br />
				Dans ce cas, l'ENT ne génère pas le mot de passe de l'utilisateur ENT.<br />
				Seul un export plus restreint est possible.</p>
			</li>
			<li>
				<p>Les utilisateurs doivent préalablement disposer d'un compte utilisateur dans Gepi.<br />
				C'est le cas pour les personnels, mais pas toujours pour les élèves et parents.<br />
				Pensez à <a href='../utilisateurs/create_eleve.php'>créer les comptes élèves</a> et <a href='../utilisateurs/create_responsable.php'>parents</a> d'abord.</p>
			</li>
		</ul>
	</fieldset>
	</form>
</div>


<hr />

<h2>Import CSV type 3</h2>
<div style='margin-left:3em;'>
	<form action='traite_export_csv.php' method='post' enctype='multipart/form-data'>
	<fieldset class='fieldset_opacite50'>
		<p style='margin-top:1em; text-indent:-4em; margin-left:4em;'>Si au lieu d'un export CSV avec les 13 champs attendus, vous disposez d'un export CSV du type&nbsp;:</p>
		<pre>
	﻿"Id";"Id Siecle";"Type";"Nom";"Prénom";"Login";"Alias de login";"Code d'activation";"Fonction(s)";"Structure(s)";"Classe(s)";"Enfant(s)";"Parent(s)"
	"LILLE-1234567";"";"Enseignant";"TERIEUR";"Alain";"alain.terieur";"alain.terieur";"";"SANS OBJET, ESPAGNOL, ESPAGNOL, ESPAGNOL";"CLG-JACQUES BREL-ac-LILLE-LILLE";"4E1, 4E4, 5E4, 5E3";"";""
	"LILLE-1323451";"2591470";"Élève";"LEUZE";"Lara";"lara.leuze";"lara.leuze";"Abq5zP71";"";"CLG-JACQUES BREL-ac-LILLE-LILLE";"6E1";"";"Corinne LEUZE, Jean LEUZE"
	"LILLE-4431638";"";"Parent";"LEUZE";"Jean";"jean.leuze";"jean.leuze";"jVUmx2C9";"";"CLG-JACQUES BREL-ac-LILLE-LILLE";"6E1";"Lara LEUZE";""
		</pre>

		<p class="title-page">Veuillez fournir le fichier csv&nbsp;:</p>
		<p>
			<input type='file' name='fichier' />

			<input type='hidden' name='type_csv' value='3' />
			<input type='hidden' name='mode' value='upload' />
			<input type='submit' value='Téléchargement' />
		</p>

		<p style='margin-top:1em; text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
		<ul>
			<li>
				<p>Le recollement entre les informations ENT et celles de Gepi se fera sur les nom et prénom des utilisateurs.<br />
				Seuls les champs <strong>"Nom";"Prénom";"Login"</strong> sont requis dans ce CSV.</p>
			</li>
			<li>
				<p>Les utilisateurs doivent préalablement disposer d'un compte utilisateur dans Gepi.<br />
				C'est le cas pour les personnels, mais pas toujours pour les élèves et parents.<br />
				Pensez à <a href='../utilisateurs/create_eleve.php'>créer les comptes élèves</a> et <a href='../utilisateurs/create_responsable.php'>parents</a> d'abord.</p>
			</li>
		</ul>
	</fieldset>
	</form>
</div>

<hr />

<a name='publipostage'></a>
<h2>Publipostage</h2>
<div style='margin-left:3em;'>
	<p>Vous pouvez ici imprimer les logins et mots de passe destinés aux utilisateurs.<br />
	Seuls les fichiers CSV type 1 (<em>ENT-Identifiants</em>) sont actuellement pris en compte.</p>
	<form action="publipostage.php" enctype='multipart/form-data' method="post">
	<fieldset class='fieldset_opacite50'>
		<input type='hidden' name='mode' value='upload' />
		<p class="title-page">Veuillez fournir le fichier csv&nbsp;:</p>
		<p>
			<input type='file'  name='fichier'  />

		<input type='submit' value='Téléchargement' />
		</p>
	</fieldset>
	</form>

	<!--
	<?php
		// ET ZUT ! CETTE PARTIE N'EST PAS INTERPRETEE

		$sql="CREATE TABLE IF NOT EXISTS tempo2_sso ( col1 varchar(100) NOT NULL default '', col2 TEXT NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="SELECT * FROM tempo2_sso;";
		$res_ts=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ts)>0) {
			echo "<br />
	<form action=\"publipostage.php\" enctype='multipart/form-data' method=\"post\">
	<fieldset class='fieldset_opacite50'>
		<input type='hidden' name='mode' value='derniers_parents_et_eleves_inscrits' />
		<p class=\"title-page\">Vous pouvez aussi imprimer les fiches des derniers rapprochements effectués (<em>élèves ou responsables</em>)</p>
		<p>
			<input type='submit' value='Valider' />
		</p>
	</fieldset>
	</form>";
		}
	?>
	-->

	<br />
	<form action="publipostage.php" enctype='multipart/form-data' method="post">
	<fieldset class='fieldset_opacite50'>
		<input type='hidden' name='mode' value='derniers_parents_et_eleves_inscrits' />
		<p class="title-page">Si vous venez d'effectuer des rapprochements (<em>élèves ou responsables</em>), vous pouvez aussi ne générer des fiches que pour les nouveaux rapprochés (<em>sans fournir à nouveau le csv</em>).</p>
		<p>
			<input type='submit' value='Valider' />
		</p>
	</fieldset>
	</form>
</div>

