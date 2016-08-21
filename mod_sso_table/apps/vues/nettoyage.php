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

<div>[onload;block=div; when [var.choix_info]='table_vide']
  La table de correspondance est vide. Vous ne pouvez utiliser cette option.
</div>

<div>[onload;block=div; when [var.choix_info]='avertissement_vidage_complet']
  Vous allez supprimer [nbre_entrees] entrées de la table de correspondances. <br/>
  <form action="index.php?ctrl=nettoyage&action=vidage_complet" enctype='multipart/form-data' method="post">
    <p>Cette action est irréversible. Valider en cliquant sur le bouton Vider.</p>
    <input type='submit' value='Vider' />
  </form>
</div>


<div>[onload;block=div; when [var.choix_info]='vidage_complet']
  Vous avez supprimé [nbre_entrees_nettoyees] entrées de la table de correspondances. <br/>
</div>
<div>[onload;block=div; when [var.choix_info]='aucun_anciens_comptes']
  <p> Aucune entrée ne semble à supprimer dans la table de correspondance.
</div>


<div>[onload;block=div; when [var.choix_info]='avertissement_anciens_comptes']
  <p>Voici les logins des correspondances à supprimer:</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="20%"><strong>Logins Gepi n'ayant pas de compte utilisateurs dans l'application</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.login_gepi;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.login_gepi;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
  <form action="index.php?ctrl=nettoyage&action=supp_anciens_comptes" enctype='multipart/form-data' method="post">
    <p>Pour supprimer les correspondances de la table cliquez sur supprimer.</p>
    <input type='submit' value='Supprimer' />
  </form>
</div>


<div>[onload;block=div; when [var.choix_info]='supp_anciens_comptes']
  <br />
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="40%"><strong>Logins Gepi n'ayant pas de compte utilisateurs dans l'application</strong></td>
      <td ><strong>Action réalisée</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.login_gepi;block=tr]</td>
      <td ><div>[b1.couleur;att=div#class][b1.message]</div></td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.login_gepi;block=tr]</td>
      <td ><div>[b1.couleur;att=div#class][b1.message]</div></td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>  
</div>
<div>[onload;block=div; when [var.choix_info]='choix_profil']
  <div class="red">[onload;block=div; when [var.message]!='']
    [var.message]
  </div>
  <p>Vous allez choisir un ou plusieurs profils d'utilisateurs pour remettre à zéro les correspondances; Plusieurs choix sont possibles mais tout nettoyage est irreversible</p>
  <p class="title-page">Attention , Une fois leur correspondance nettoyée les utilisateurs ne pourront plus se connecter en SSO avec ce module</p>
  <form action="index.php?ctrl=nettoyage&action=choix_profil" enctype='multipart/form-data' method="post">
    <p>
      <input type="checkbox" name="choix_profil[]" value="Administrateur"  />Administrateur<br/>
      <input type="checkbox" name="choix_profil[]" value="Cpe"  />Cpe<br/>
      <input type="checkbox" name="choix_profil[]" value="Scolarite"  />Scolarité<br/>
      <input type="checkbox" name="choix_profil[]" value="Secours"  />Secours<br/>
      <input type="checkbox" name="choix_profil[]" value="Autre"  />Autre<br/>
      <input type="checkbox" name="choix_profil[]" value="Professeur"  />Professeur<br/>
      <input type="checkbox" name="choix_profil[]" value="Eleve"  />Eleve<br/>
      <input type="checkbox" name="choix_profil[]" value="Responsable"  />Responsable<br/>
    </p>
    <input type='submit' value='Choisir' />
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='avertissement_profil']
  <p>Nombre de correspondances à supprimer</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="40%"><strong>Profil</strong></td>
      <td ><strong>Nombres de correspondances à supprimer</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
  <form action="index.php?ctrl=nettoyage&action=supp_profil" enctype='multipart/form-data' method="post">
    <p>Pour supprimer les correspondances de la table cliquez sur supprimer.</p>
    <input type='submit' value='Supprimer' />
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='resultat_profil']
  <p>Résultats des suppressions :</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="40%"><strong>Profil</strong></td>
      <td ><strong>Nombres de correspondances supprimés</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
</div>

<div>[onload;block=div; when [var.choix_info]='choix_classe']
  <div class="red">[onload;block=div; when [var.message]!='']
    [var.message]
  </div>
  <p>Vous allez remettre à zéro les correspondances pour une ou plusieurs classes (eleves et/ou responsables); Plusieurs choix sont possibles mais tout nettoyage est irreversible</p>
  <p class="title-page">Attention , Une fois leur correspondance nettoyée les utilisateurs ne pourront plus se connecter en SSO avec ce module</p>
  <form action="index.php?ctrl=nettoyage&action=choix_classe" enctype='multipart/form-data' method="post">
    <div class="left">
      <p>
        <input type="checkbox" name="choix_classe[]" value="[b1.id;block=p]" >[b1.classe]
      </p>
    </div>
    <div class="left">
      <input type="checkbox" name="choix_profil[]" value="Eleve"   >Eleve<br/>
      <input type="checkbox" name="choix_profil[]" value="Responsable"  />Responsable<br/>
    </div>
    <div>
      <input type='submit' value='Choisir' />
    </div>
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='avertissement_classe']
  <p>Nombre de correspondances à supprimer</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="20%"><strong>Classe</strong></td>
      <td width="20%"><strong>Profil</strong></td>
      <td ><strong>Nombres de correspondances à supprimer</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
  <form action="index.php?ctrl=nettoyage&action=supp_classe" enctype='multipart/form-data' method="post">
    <p>Pour supprimer les correspondances de la table cliquez sur supprimer.</p>
    <input type='submit' value='Supprimer' />
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='resultat_classe']
  <p>Nombre de correspondances supprimées</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="20%"><strong>Classe</strong></td>
      <td width="20%"><strong>Profil</strong></td>
      <td ><strong>Nombres de correspondances supprimées</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
</div>
</body>
</html>
