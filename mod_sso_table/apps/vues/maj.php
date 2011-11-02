<?php
/*
* $Id: maj.php 7744 2011-08-14 13:07:15Z dblanqui $
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
<p>A partir de cette page vous pouvez mettre &agrave; jour manuellement une correspondance :</p>
<p>Rechercher un utilisateur dans G&eacute;pi. Attention son compte doit &ecirc;tre activ&eacute; et param&eacute;tr&eacute; en sso</p>
<form action="index.php?ctrl=maj&action=search"  method="post">
          <input type="text" name="nom" id="nom" value="" />
          <input type="submit" name="action" value="Rechercher" class="submit"/>
</form>
</body>
 </html>