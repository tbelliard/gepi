function openwin() {
msgWindow=window.open("","mafenetre","top=100, left=100, width=280,height=70, resizable='no' menubar='no',toolbar='no', statusbar='no'");
msgWindow.document.write("<HEAD><TITLE>Message window</TITLE></HEAD>");
msgWindow.document.write("<CENTER><BIG><B>Patientez quelques instants...</B></BIG></CENTER>");
return msgWindow;
}

function recup_extension(fichier) // fonction de récupération extension fichier
   {
         if (fichier!="")// si le champ fichier n'est pas vide
         {
            nom_fichier=fichier;// on récupere le chemin complet du fichier
            nbchar = nom_fichier.length;// on compte le nombre de caracteres que compose ce chemin
            extension = nom_fichier.substring(nbchar-4,nbchar); // on récupere les 4 derniers caracteres
            extension=extension.toLowerCase(); //on uniforme les caracteres en minuscules au cas ou cela aurait été écrit en majuscule...
            return extension; // on renvoit l'extension vers la fonction appelante
         }
   }

function verif_extension(fichier)// fonction vérification de l'extension après avoir choisi le fichier
   {
   ext = recup_extension(fichier);// on appelle la fonction de récupération de l'extension et on récupere l'extension
            if(ext==".odt"||ext==".ods"){return true;}// si extension = a une des extensions suivantes alors tout est ok donc ... pas d'erreur
			else if (ext==".txt"){return true;}
            else // sinon on alert l'user de la mauvaise extension
            {
               alert("L'extension du fichier que vous voulez uploader est :'"+ext+"'\n cette extension n'est pas autorisée !\n Seules les extensions suivantes sont autorisées :\n'ODT - Texte ; ODS - Tableur' !");
			   return false;
            }
   }

function bonfich(f) { //vérifie le formulaire avant envoi
 var frm=eval('document.form'+f);
 var odt='odt';
 var nomfich,message;
 nomfich=frm.monfichier.value.toLowerCase();
 if (verif_extension(nomfich)) {
     openwin();
     return true;
 } else {
     return false;
  }
}

function confirmer() {
    return (confirm('Supprimer vraiment ce fichier ?'));
}