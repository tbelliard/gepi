/**
 ---------------------------------------------------------------------------------------------
 * METHODES JAVASCRIPT
 * Calendrier
 * calendrier.js
 ---------------------------------------------------------------------------------------------
 */

/**
 * Reload la fenêtre avec les nouveaux mois et année choisis
 *
 * @param   object      frm     L'object document du formulaire
 */
function reload(frm){
    var mois = frm.elements['mois'];
    var annee = frm.elements['annee'];
    //Debug du mois et année
    var index1 = mois.options[mois.selectedIndex].value;
    var index2 = annee.options[annee.selectedIndex].value;
    //Envoi du formulaire
    frm.submit();
}

/**
 * Ajoute un zéro devant le jour et le mois s'ils sont plus petit que 10
 *
 * @param   integer     jour        Le numéro du jour dans le mois
 * @param   integer     mois        Le numéro du mois
 */
function checkNum(jour, mois){
    tab = new Array();
    tab[0] = jour;
    tab[1] = mois;
    if (this.checkzero){
        if (jour < 10){
            tab[0] = "0" + jour;
        }
        if (mois < 10){
            tab[1] = "0" + mois;
        }
    }
    return tab;
}

/**
 * Créé la string de retour
 *
 * C'est ici que la string est créé. C'est également ici que le champ du formulaire
 * de la page d'appel reçoit la valeur. La fenêtre s'auto-fermera ensuite toute
 * seule comme une grande.
 * Paisible est l'étudiant qui comme la rivière peut suivre son cours sans quitter son lit...
 *
 * @param   integer     jour        Le numéro du jour dans le mois
 */
function submitDate(jour){
    tab = this.checkNum(jour, this.moisc);
    jour = tab[0];
    mois = tab[1];
    if (this.ordre[0] && this.ordre[0] == "M"){
        if (this.ordre[1] && this.ordre[1] == "A"){
            val = mois + this.format + this.anneec + this.format + jour;
        }else{
            val = mois + this.format + jour + this.format + this.anneec;
        }
    }else if (this.ordre[0] && this.ordre[0] == "J"){
        if (this.ordre[1] == "A"){
            val = jour + this.format + this.anneec + this.format + mois;
        }else{
            val = jour + this.format + mois + this.format + this.anneec;
        }
    }else{
        if (this.ordre[1] && this.ordre[1] == "J"){
            val = this.anneec + this.format + jour + this.format + mois;
        }else{
            val = this.anneec + this.format + mois + this.format + jour;
        }
    }
    //On agit selon qu'on est dans une popup ou non
    this.finOperation(val);
}