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
<h2>Ce module sert à créer une correspondance entre les logins Gépi et ENT dans le cas d'une authentification CAS</h2>
<h3>Il y a trois possibilités pour la mise en place de la correspondance :</h3>
<ol>
	<li><a name='correspondances'></a><strong>Par importation des correspondances depuis un fichier csv :</strong></li>
	<p>Cliquer sur Import de données</p>
	<p>Le fichier à fournir doit s'appeler correspondances.csv</p>
	<p>Il ne doit contenir par ligne que deux données séparées par un ;</p>
	<p>La première donnée est le login Gépi et la deuxième le login sso (de l'ent par exemple)</p>
	<p>Voici un exemple</p>
	<img src='img/fichier.png' />
	<p class='message_red'>Vérifiez bien dans un logiciel comme notepad++ par exemple qu'il n'y a pas de lignes vides.. </p>
	<p class='message_red'>Attention au formattage des données avec des tableurs comme Excel par exemple.. </p>
	<p>Une fois le traitement effectué vous obtiendrez un tableau avec les résultats :</p>
	<img src='img/resultat.png' />
	<p>Si l'utilisateur n'existe pas dans Gépi , ou si une entrée existe déja dans la table de correspondance (login Gépi ou sso),
	aucune correspondance n'est mis en place.</p>
	<p>Si l'utilisateur existe dans Gépi mais que le compte n'est pas paramétré en sso la correspondance est mise en place mais le mode de connexion doit être modifié dans Gépi </p>
	<p>Dans les autres cas la correspondance est mise en place.</p>
	<br />

	<li><a name='maj'></a><strong>Par mise en place manuelle de la correspondance pour un utilisateur de Gépi :</strong></li>
	<p>Cliquer sur <em>Mise à jour de données </em></p>
	<p>Rechercher le nom d'un utilisateur de Gépi </p>
	<p class='message_red'>Attention cet utilisateur doit avoir son mode d'authentification paramétré en sso</p>
	<p>Cliquez sur le login de l'utilisateur choisi :</p>
	<p>Vous pouvez entrer le login sso pour la correspondance avec Gépi.</p>
	<p>Si une correspondance existe déja le login sso s'affiche. Vous pouvez le mettre à jour.</p>
	<p>Voici une copie d'écran :</p>
	<img src='img/maj.png' />
	<br />
	<br />

	<li><a name='csv'></a><strong>Par recherche des correspondances sur les noms et prénoms, à partir d'un fichier csv :</strong></li>	
	<p>Cliquer sur <em>CVS export ENT</em></p>
	<p>Le fichier à fournir doit s'appeler <em>ENT-Identifiants.csv</em></p>
	<p>Il doit contenir par ligne treize champs séparés par un ;</p>
	<ol>
	  <li>RNE de l'établissement : non utilisé</li>
	  <li>UID : identifiant SSO dans l'ENT, c'est ce champ qui sert de jointure</li>
	  <li>classe de l'élève : sert à repérer les comptes parents et élèves</li>
	  <li>profil : sert à différencier les doublons parents et élèves, les intitulés peuvent être différents de ceux de Gépi mais doivent être cohérents</li>
	  <li>prénom : le premier doit correspondre à celui de Gépi</li>
	  <li>nom : doit correspondre à celui de Gépi</li>
	  <li>login : login dans l'ENT, non utilisé</li>
	  <li>mot de passe : mot de passe dans l'ENT, non utilisé</li>
	  <li>cle de jointure : non utilisé</li>
	  <li>uid père : sert à repérer les élèves et à retrouver les responsables en cas de doublon</li>
	  <li>uid mère : sert à repérer les élèves et à retrouver les responsables en cas de doublon</li>
	  <li>uid tuteur1 : sert à repérer les élèves et à retrouver les responsables en cas de doublon</li>
	  <li>uid tuteur2 : sert à repérer les élèves et à retrouver les responsables en cas de doublon</li>
	</ol>
	<p>Les champs non utilisés peuvent être laissés vides</p>
	<p>Voici un exemple</p>
	<img src='img/identifiants.png' />
	<p class='message_red'>
		Vérifiez bien dans un logiciel comme notepad++ par exemple qu'il n'y a pas de lignes vides..
	</p>
	<p>
		Vous pouvez laisser la première ligne avec les noms de champs. Lors du traitement, vous obtiendrez un enregistrement en erreur dans lequel vous pourrez vérifier sur quels champs vous faites la recherche
	</p>
	<img src='img/cvs_ent_id.png' />
	<p class='message_red'>Attention au formatage des données avec des tableurs comme Excel par exemple.. </p>

	<p>Avant de mettre en place les correspondances dans la base, vous pouvez tester le résultat de l'import :</p>
	<img src='img/cvs_ent.png' />
	<ul>
	<li>Rechercher des erreurs : Toutes les erreurs sont affichées, aucune donnée n'est écrite dans la base</li>
	<li>Test : Toutes les lignes sont traitées et affichées mais aucune donnée n'est écrite dans la base</li>
	<li>Inscription dans la base : Toutes les lignes sont traitées et affichées, les correspondances sont écrites au besoin dans la base</li>

	</ul>
	<p>Une fois le traitement effectué vous obtiendrez un tableau avec les résultats :</p>
	<img src='img/resultat.png' />
	<p>Si l'utilisateur n'existe pas dans Gépi, aucune correspondance n'est mise en place.</p>
	<p>Si une entrée existe déja dans la table de correspondance, aucune correspondance n'est mise en place et on affiche si la correspondance est différente.</p>
	<p>Si l'utilisateur existe dans Gépi mais que le compte n'est pas paramétré en SSO, la correspondance est mise en place mais le mode de connexion doit être modifié dans Gépi.</p>
	<p>Dans les autres cas la correspondance est mise en place.</p>

</ol>
</body>
 </html>
