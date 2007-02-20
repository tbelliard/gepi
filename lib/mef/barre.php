<?php
//affichage des boutons de mise en forme
echo "<center><b><u>Mise en forme du texte : </u></b> Sélectionnez le texte puis cliquez sur la mise en forme désirée.</center><br>";
echo "<center>
<a title=\"Gras\" href='#' onclick='insertTags(\" __ \",\" __ \",\"\")'><img alt=\"Gras\" src=\"../lib/mef/gras.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;
<a title=\"Italique\" href='#' onclick='insertTags(\" &#39;&#39; \",\" &#39;&#39; \",\"\")'><img alt=\"Italique\" src=\"../lib/mef/italique.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;
<a title=\"Souligner\" href='#' onclick='insertTags(\" &#39;&#39;&#39; \",\" &#39;&#39;&#39; \",\"\")'><img alt=\"Souligner\" src=\"../lib/mef/souligne.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;
<a title=\"Rouge\" href='#' onclick='insertTags(\" _r_ \",\" _r_ \",\"\")'><img alt=\"Rouge\" src=\"../lib/mef/rouge.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;
<a title=\"Bleu\" href='#' onclick='insertTags(\" _b_ \",\" _b_ \",\"\")'><img alt=\"Bleu\" src=\"../lib/mef/bleu.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Vert\" href='#' onclick='insertTags(\" _v_ \",\" _v_ \",\"\")'><img alt=\"Vert\" src=\"../lib/mef/vert.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Orange\" href='#' onclick='insertTags(\" _o_ \",\" _o_ \",\"\")'><img alt=\"Orange\" src=\"../lib/mef/orange.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Marron\" href='#' onclick='insertTags(\" _m_ \",\" _m_ \",\"\")'><img alt=\"Marron\" src=\"../lib/mef/marron.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Jaune\" href='#' onclick='insertTags(\" _j_ \",\" _j_ \",\"\")'><img alt=\"Jaune\" src=\"../lib/mef/jaune.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Pourpre\" href='#' onclick='insertTags(\" _p_ \",\" _p_ \",\"\")'><img alt=\"Pourpre\" src=\"../lib/mef/pourpre.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Gris\" href='#' onclick='insertTags(\" _g_ \",\" _g_ \",\"\")'><img alt=\"Gris\" src=\"../lib/mef/gris.png\" style=\"border: 0px solid ;\"></a>&nbsp;&nbsp;
<a title=\"Centrer\" href='#' onclick='insertTags(\" _c_ \",\" _c_ \",\"\")'><img alt=\"Centrer\" src=\"../lib/mef/centre.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;

<a title=\"Liste\" href='#' onclick='insertTags(\"* \",\"\",\"\")'><img alt=\"Liste\" src=\"../lib/mef/puce.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;
<a title=\"Liste numérotée\" href='#' onclick='insertTags(\"# \",\"\",\"\")'><img alt=\"Liste numérotée\" src=\"../lib/mef/numero.png\" style=\"border: 0px solid ;\"></a> &nbsp;&nbsp;
<a title=\"Ligne\" href='#' onclick='insertTags(\"----\",\"\",\"\")'><img alt=\"Ligne\" src=\"../lib/mef/ligne.png\" style=\"border: 0px solid ;\"></a></center><br>";
echo "<center>
<a title=\"Retour à la ligne\" href='#' onclick='insertTags(\"%%%\",\"\",\"\")'>Retour à la ligne</a> (à placer en fin de ligne) &nbsp;&nbsp; 
TITRAGE (à placer juste devant le titre) :   <a title=\"Petit titre\" href='#' onclick='insertTags(\"!\",\"\",\"\")'>Petit titre</a> &nbsp;&nbsp;
<a title=\"Gros titre\" href='#' onclick='insertTags(\"!!!\",\"\",\"\")'>Gros titre</a> &nbsp;&nbsp;
</center><br>";
?>