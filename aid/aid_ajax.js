
function changerDisplayDiv(nomDiv) {
	Element.toggle(nomDiv);
}

function afficher_cacher(id)
{
    if(document.getElementById(id).style.visibility=="hidden")
    {
        document.getElementById(id).style.visibility="visible";
    }
    else
    {
        document.getElementById(id).style.visibility="hidden";
    }
    return true;
}

function afficher_cacher_parent()
{
    if(document.getElementById('sous_groupe').checked)
    {
        document.getElementById('choix_parent').style.visibility="visible";
    }
    else
    {
        document.getElementById('choix_parent').style.visibility="hidden";
    }
    return true;
}
