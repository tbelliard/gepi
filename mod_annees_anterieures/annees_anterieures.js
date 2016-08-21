function ajax_annee_anterieure_bull_simp(login_ele, id_classe, annee, periode) {
	new Ajax.Updater($('div_mod_annee_anterieure'),'../mod_annees_anterieures/popup_annee_anterieure.php?logineleve='+login_ele+'&id_classe='+id_classe+'&annee_scolaire='+annee+'&num_periode='+periode+'&mode=bull_simp&mode_js=y',{method: 'get'});
}

function ajax_annee_anterieure_avis(login_ele, annee) {
	new Ajax.Updater($('div_mod_annee_anterieure'),'../mod_annees_anterieures/popup_annee_anterieure.php?logineleve='+login_ele+'&annee_scolaire='+annee+'&mode=avis_conseil&mode_js=y',{method: 'get'});
}

