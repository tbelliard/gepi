<?php



/**
 * Skeleton subclass for performing query and update operations on the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class UtilisateurProfessionnelPeer extends BaseUtilisateurProfessionnelPeer {

	/**
	 *
	 * Renvoi l'utilisateur de la session en cours (null si aucun utilisateur trouvé dans la session
	 *
	 * @return     UtilisateurProfessionnel utilisateur
	 */
	public static function getUtilisateursSessionEnCours() {
        if (isset($_SESSION) && isset($_SESSION['objets_propel']) && isset($_SESSION['objets_propel']['utilisateurProfessionnel'])
            && $_SESSION['objets_propel']['utilisateurProfessionnel'] != null
            && $_SESSION['objets_propel']['utilisateurProfessionnel'] instanceof UtilisateurProfessionnel) {
            //echo 'utilisateur recupere dans la session';
            return $_SESSION['objets_propel']['utilisateurProfessionnel'];
        } elseif (isset($_SESSION) && isset($_SESSION['login'])) {
            $utilisateur = UtilisateurProfessionnelQuery::create()->filterByLogin($_SESSION['login'])->findOne();
            if ($utilisateur != null) {
                $_SESSION['objets_propel']['utilisateurProfessionnel'] = $utilisateur;
            }
            return $utilisateur;
        } else {
            return null;
        }
	}

} // UtilisateurProfessionnelPeer
