/*
 *
 * Copyright 2015 Régis Bouguin
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

function afficher_cacher(id)
{
    if(document.getElementById(id).style.display!=="none")
    {
        document.getElementById(id).style.display="none";
    }
    else
    {
        document.getElementById(id).style.display="block";
    }
    return true;
}
function masque(id)
{
   document.getElementById(id).style.display="none";   
}
function affiche(id)
{
   document.getElementById(id).style.display="block";
}
function activer(id)
{
   if (!document.getElementById(id).classList.contains('div_actif'))
   {
	 document.getElementById(id).classList.add('div_actif');
	 affiche(id);
   }
   if (!document.getElementById('menu_lien_'+id).classList.contains('lien_actif'))
   {
	 document.getElementById('menu_lien_'+id).classList.add('lien_actif');
   }
}
function desactiver(id)
{
   if (document.getElementById(id).classList.contains('div_actif'))
   {
	 document.getElementById(id).classList.remove('div_actif');
	 masque(id);
   }
   if (document.getElementById('menu_lien_'+id).classList.contains('lien_actif'))
   {
	 document.getElementById('menu_lien_'+id).classList.remove('lien_actif');
   }
}

function inverse(col)
{
   affiche('saisie'+col);
   masque('vision'+col);
   // document.getElementById('entree'+col).focus();
   setFocus('entree'+col);
}

function setFocus(id)
{   
   var tValue = document.getElementById(id).value;
   document.getElementById(id).value = "";
   document.getElementById(id).focus();
   document.getElementById(id).value = tValue;
}

function supprime(login, nom, prenom)
{
   var r = confirm("Voulez-vous vraiment supprimer "+nom+' '+prenom+' ('+login+')');
   if (r === true) {
	 //alert ("on supprime "+login+" → "+'formSupprimeEleve'+login);
	 document.getElementById('formSupprimeEleve'+login).submit();
	 //alert ("on a supprimé "+login);
   }
}

function supprimeColonne(id, titre)
{
   var r = confirm("Voulez-vous vraiment supprimer '"+titre+"'");
   if (r === true) {
	 alert ("on supprime "+id);
	 document.getElementById('sauveSupprimeCol'+id).value = "supprimeColonne";
	 document.getElementById('formModifieTitre'+id).submit();
   }
   
}

function afficheAide()
{
   f=open("",'aide','width=600,height=400,toolbar=no,scrollbars=no,resizable=yes,location=no,directories=0');
   f.document.write("<title>Listes personnelles → Aide</title>");
   f.document.write("<body style='background-color: #eeeeee;'>");
   f.document.write("<p><strong>Listes perso</strong> → choisir une liste ou en créer une nouvelle.</p>");
   f.document.write("<p><strong>Construction</strong> → donner un nom à la liste, choisir les colonnes prédéfinies, déterminer le nombre de colonnes libres puis en ajouter au besoin. Il est aussi possible de supprimer définitivement la liste</p>");
   f.document.write("<p><strong>Élèves</strong> → ajouter des élèves en les choisissant dans ses listes.</p>");
   f.document.write("<p><strong>Modifier</strong> → supprimer les colonnes ou modifier leurs places.</p>");
   f.document.write("<p>Cliquez dans les entêtes de colonnes pour créer leur titre ou le modifier.</p>");
   f.document.write("<p>Cliquez dans les cellules pour en modifier le contenu.</p>");
   f.document.write("<p>Cliquez en dehors pour enregistrer.</p>");
   f.document.write("<p>Les points rouges <img src='../images/bulle_rouge.png' alt='image supprime' /> permettent de supprimer une ligne ou le contenu d'une cellule.</p>");
   f.document.write("</body>");
   f.document.close();
   
   
}
