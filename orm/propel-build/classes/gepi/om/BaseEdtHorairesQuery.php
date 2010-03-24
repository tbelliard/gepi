<?php


/**
 * Base class that represents a query for the 'horaires_etablissement' table.
 *
 * Table contenant les heures d'ouverture et de fermeture de l'etablissement par journee
 *
 * @method     EdtHorairesQuery orderByIdHoraireEtablissement($order = Criteria::ASC) Order by the id_horaire_etablissement column
 * @method     EdtHorairesQuery orderByDateHoraireEtablissement($order = Criteria::ASC) Order by the date_horaire_etablissement column
 * @method     EdtHorairesQuery orderByJourHoraireEtablissement($order = Criteria::ASC) Order by the jour_horaire_etablissement column
 * @method     EdtHorairesQuery orderByOuvertureHoraireEtablissement($order = Criteria::ASC) Order by the ouverture_horaire_etablissement column
 * @method     EdtHorairesQuery orderByFermetureHoraireEtablissement($order = Criteria::ASC) Order by the fermeture_horaire_etablissement column
 * @method     EdtHorairesQuery orderByPauseHoraireEtablissement($order = Criteria::ASC) Order by the pause_horaire_etablissement column
 * @method     EdtHorairesQuery orderByOuvertHoraireEtablissement($order = Criteria::ASC) Order by the ouvert_horaire_etablissement column
 *
 * @method     EdtHorairesQuery groupByIdHoraireEtablissement() Group by the id_horaire_etablissement column
 * @method     EdtHorairesQuery groupByDateHoraireEtablissement() Group by the date_horaire_etablissement column
 * @method     EdtHorairesQuery groupByJourHoraireEtablissement() Group by the jour_horaire_etablissement column
 * @method     EdtHorairesQuery groupByOuvertureHoraireEtablissement() Group by the ouverture_horaire_etablissement column
 * @method     EdtHorairesQuery groupByFermetureHoraireEtablissement() Group by the fermeture_horaire_etablissement column
 * @method     EdtHorairesQuery groupByPauseHoraireEtablissement() Group by the pause_horaire_etablissement column
 * @method     EdtHorairesQuery groupByOuvertHoraireEtablissement() Group by the ouvert_horaire_etablissement column
 *
 * @method     EdtHorairesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtHorairesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtHorairesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtHoraires findOne(PropelPDO $con = null) Return the first EdtHoraires matching the query
 * @method     EdtHoraires findOneByIdHoraireEtablissement(int $id_horaire_etablissement) Return the first EdtHoraires filtered by the id_horaire_etablissement column
 * @method     EdtHoraires findOneByDateHoraireEtablissement(string $date_horaire_etablissement) Return the first EdtHoraires filtered by the date_horaire_etablissement column
 * @method     EdtHoraires findOneByJourHoraireEtablissement(string $jour_horaire_etablissement) Return the first EdtHoraires filtered by the jour_horaire_etablissement column
 * @method     EdtHoraires findOneByOuvertureHoraireEtablissement(string $ouverture_horaire_etablissement) Return the first EdtHoraires filtered by the ouverture_horaire_etablissement column
 * @method     EdtHoraires findOneByFermetureHoraireEtablissement(string $fermeture_horaire_etablissement) Return the first EdtHoraires filtered by the fermeture_horaire_etablissement column
 * @method     EdtHoraires findOneByPauseHoraireEtablissement(string $pause_horaire_etablissement) Return the first EdtHoraires filtered by the pause_horaire_etablissement column
 * @method     EdtHoraires findOneByOuvertHoraireEtablissement(boolean $ouvert_horaire_etablissement) Return the first EdtHoraires filtered by the ouvert_horaire_etablissement column
 *
 * @method     array findByIdHoraireEtablissement(int $id_horaire_etablissement) Return EdtHoraires objects filtered by the id_horaire_etablissement column
 * @method     array findByDateHoraireEtablissement(string $date_horaire_etablissement) Return EdtHoraires objects filtered by the date_horaire_etablissement column
 * @method     array findByJourHoraireEtablissement(string $jour_horaire_etablissement) Return EdtHoraires objects filtered by the jour_horaire_etablissement column
 * @method     array findByOuvertureHoraireEtablissement(string $ouverture_horaire_etablissement) Return EdtHoraires objects filtered by the ouverture_horaire_etablissement column
 * @method     array findByFermetureHoraireEtablissement(string $fermeture_horaire_etablissement) Return EdtHoraires objects filtered by the fermeture_horaire_etablissement column
 * @method     array findByPauseHoraireEtablissement(string $pause_horaire_etablissement) Return EdtHoraires objects filtered by the pause_horaire_etablissement column
 * @method     array findByOuvertHoraireEtablissement(boolean $ouvert_horaire_etablissement) Return EdtHoraires objects filtered by the ouvert_horaire_etablissement column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtHorairesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEdtHorairesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtHoraires', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtHorairesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtHorairesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtHorairesQuery) {
			return $criteria;
		}
		$query = new EdtHorairesQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = EdtHorairesPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			return $this
				->filterByPrimaryKey($key)
				->findOne($con);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtHorairesPeer::ID_HORAIRE_ETABLISSEMENT, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtHorairesPeer::ID_HORAIRE_ETABLISSEMENT, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_horaire_etablissement column
	 * 
	 * @param     int|array $idHoraireEtablissement The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByIdHoraireEtablissement($idHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idHoraireEtablissement)) {
			return $this->addUsingAlias(EdtHorairesPeer::ID_HORAIRE_ETABLISSEMENT, $idHoraireEtablissement, Criteria::IN);
		} else {
			return $this->addUsingAlias(EdtHorairesPeer::ID_HORAIRE_ETABLISSEMENT, $idHoraireEtablissement, $comparison);
		}
	}

	/**
	 * Filter the query on the date_horaire_etablissement column
	 * 
	 * @param     string|array $dateHoraireEtablissement The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByDateHoraireEtablissement($dateHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($dateHoraireEtablissement)) {
			if (array_values($dateHoraireEtablissement) === $dateHoraireEtablissement) {
				return $this->addUsingAlias(EdtHorairesPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement, Criteria::IN);
			} else {
				if (isset($dateHoraireEtablissement['min'])) {
					$this->addUsingAlias(EdtHorairesPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($dateHoraireEtablissement['max'])) {
					$this->addUsingAlias(EdtHorairesPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(EdtHorairesPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement, $comparison);
		}
	}

	/**
	 * Filter the query on the jour_horaire_etablissement column
	 * 
	 * @param     string $jourHoraireEtablissement The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByJourHoraireEtablissement($jourHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($jourHoraireEtablissement)) {
			return $this->addUsingAlias(EdtHorairesPeer::JOUR_HORAIRE_ETABLISSEMENT, $jourHoraireEtablissement, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $jourHoraireEtablissement)) {
			return $this->addUsingAlias(EdtHorairesPeer::JOUR_HORAIRE_ETABLISSEMENT, str_replace('*', '%', $jourHoraireEtablissement), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtHorairesPeer::JOUR_HORAIRE_ETABLISSEMENT, $jourHoraireEtablissement, $comparison);
		}
	}

	/**
	 * Filter the query on the ouverture_horaire_etablissement column
	 * 
	 * @param     string|array $ouvertureHoraireEtablissement The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByOuvertureHoraireEtablissement($ouvertureHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($ouvertureHoraireEtablissement)) {
			if (array_values($ouvertureHoraireEtablissement) === $ouvertureHoraireEtablissement) {
				return $this->addUsingAlias(EdtHorairesPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement, Criteria::IN);
			} else {
				if (isset($ouvertureHoraireEtablissement['min'])) {
					$this->addUsingAlias(EdtHorairesPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($ouvertureHoraireEtablissement['max'])) {
					$this->addUsingAlias(EdtHorairesPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(EdtHorairesPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement, $comparison);
		}
	}

	/**
	 * Filter the query on the fermeture_horaire_etablissement column
	 * 
	 * @param     string|array $fermetureHoraireEtablissement The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByFermetureHoraireEtablissement($fermetureHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($fermetureHoraireEtablissement)) {
			if (array_values($fermetureHoraireEtablissement) === $fermetureHoraireEtablissement) {
				return $this->addUsingAlias(EdtHorairesPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement, Criteria::IN);
			} else {
				if (isset($fermetureHoraireEtablissement['min'])) {
					$this->addUsingAlias(EdtHorairesPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($fermetureHoraireEtablissement['max'])) {
					$this->addUsingAlias(EdtHorairesPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(EdtHorairesPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement, $comparison);
		}
	}

	/**
	 * Filter the query on the pause_horaire_etablissement column
	 * 
	 * @param     string|array $pauseHoraireEtablissement The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByPauseHoraireEtablissement($pauseHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($pauseHoraireEtablissement)) {
			if (array_values($pauseHoraireEtablissement) === $pauseHoraireEtablissement) {
				return $this->addUsingAlias(EdtHorairesPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement, Criteria::IN);
			} else {
				if (isset($pauseHoraireEtablissement['min'])) {
					$this->addUsingAlias(EdtHorairesPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($pauseHoraireEtablissement['max'])) {
					$this->addUsingAlias(EdtHorairesPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(EdtHorairesPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement, $comparison);
		}
	}

	/**
	 * Filter the query on the ouvert_horaire_etablissement column
	 * 
	 * @param     boolean|string $ouvertHoraireEtablissement The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function filterByOuvertHoraireEtablissement($ouvertHoraireEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if(is_string($ouvertHoraireEtablissement)) {
			$ouvert_horaire_etablissement = in_array(strtolower($ouvertHoraireEtablissement), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(EdtHorairesPeer::OUVERT_HORAIRE_ETABLISSEMENT, $ouvertHoraireEtablissement, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtHoraires $edtHoraires Object to remove from the list of results
	 *
	 * @return    EdtHorairesQuery The current query, for fluid interface
	 */
	public function prune($edtHoraires = null)
	{
		if ($edtHoraires) {
			$this->addUsingAlias(EdtHorairesPeer::ID_HORAIRE_ETABLISSEMENT, $edtHoraires->getIdHoraireEtablissement(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

	/**
	 * Code to execute before every SELECT statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreSelect(PropelPDO $con)
	{
		return $this->preSelect($con);
	}

	/**
	 * Code to execute before every DELETE statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		return $this->preDelete($con);
	}

	/**
	 * Code to execute before every UPDATE statement
	 * 
	 * @param     array $values The associatiove array of columns and values for the update
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreUpdate(&$values, PropelPDO $con)
	{
		return $this->preUpdate($values, $con);
	}

} // BaseEdtHorairesQuery
