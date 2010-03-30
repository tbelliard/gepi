<?php


/**
 * Base class that represents a query for the 'edt_semaines' table.
 *
 * Liste des semaines de l'annee scolaire courante - 53 enregistrements obligatoires (pas 52!), pour lesquel on assign eun type (A ou B par xexemple)
 *
 * @method     EdtTypeSemaineQuery orderByIdEdtSemaine($order = Criteria::ASC) Order by the id_edt_semaine column
 * @method     EdtTypeSemaineQuery orderByNumEdtSemaine($order = Criteria::ASC) Order by the num_edt_semaine column
 * @method     EdtTypeSemaineQuery orderByTypeEdtSemaine($order = Criteria::ASC) Order by the type_edt_semaine column
 *
 * @method     EdtTypeSemaineQuery groupByIdEdtSemaine() Group by the id_edt_semaine column
 * @method     EdtTypeSemaineQuery groupByNumEdtSemaine() Group by the num_edt_semaine column
 * @method     EdtTypeSemaineQuery groupByTypeEdtSemaine() Group by the type_edt_semaine column
 *
 * @method     EdtTypeSemaineQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtTypeSemaineQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtTypeSemaineQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtTypeSemaine findOne(PropelPDO $con = null) Return the first EdtTypeSemaine matching the query
 * @method     EdtTypeSemaine findOneByIdEdtSemaine(int $id_edt_semaine) Return the first EdtTypeSemaine filtered by the id_edt_semaine column
 * @method     EdtTypeSemaine findOneByNumEdtSemaine(int $num_edt_semaine) Return the first EdtTypeSemaine filtered by the num_edt_semaine column
 * @method     EdtTypeSemaine findOneByTypeEdtSemaine(string $type_edt_semaine) Return the first EdtTypeSemaine filtered by the type_edt_semaine column
 *
 * @method     array findByIdEdtSemaine(int $id_edt_semaine) Return EdtTypeSemaine objects filtered by the id_edt_semaine column
 * @method     array findByNumEdtSemaine(int $num_edt_semaine) Return EdtTypeSemaine objects filtered by the num_edt_semaine column
 * @method     array findByTypeEdtSemaine(string $type_edt_semaine) Return EdtTypeSemaine objects filtered by the type_edt_semaine column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtTypeSemaineQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEdtTypeSemaineQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtTypeSemaine', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtTypeSemaineQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtTypeSemaineQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtTypeSemaineQuery) {
			return $criteria;
		}
		$query = new EdtTypeSemaineQuery();
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
	 * @return    EdtTypeSemaine|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = EdtTypeSemainePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$stmt = $this
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $this->getFormatter()->formatOne($stmt);
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
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
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
	 * @return    EdtTypeSemaineQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtTypeSemainePeer::ID_EDT_SEMAINE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtTypeSemaineQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtTypeSemainePeer::ID_EDT_SEMAINE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_edt_semaine column
	 * 
	 * @param     int|array $idEdtSemaine The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtTypeSemaineQuery The current query, for fluid interface
	 */
	public function filterByIdEdtSemaine($idEdtSemaine = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idEdtSemaine)) {
			return $this->addUsingAlias(EdtTypeSemainePeer::ID_EDT_SEMAINE, $idEdtSemaine, Criteria::IN);
		} else {
			return $this->addUsingAlias(EdtTypeSemainePeer::ID_EDT_SEMAINE, $idEdtSemaine, $comparison);
		}
	}

	/**
	 * Filter the query on the num_edt_semaine column
	 * 
	 * @param     int|array $numEdtSemaine The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtTypeSemaineQuery The current query, for fluid interface
	 */
	public function filterByNumEdtSemaine($numEdtSemaine = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($numEdtSemaine)) {
			if (array_values($numEdtSemaine) === $numEdtSemaine) {
				return $this->addUsingAlias(EdtTypeSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine, Criteria::IN);
			} else {
				if (isset($numEdtSemaine['min'])) {
					$this->addUsingAlias(EdtTypeSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($numEdtSemaine['max'])) {
					$this->addUsingAlias(EdtTypeSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(EdtTypeSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine, $comparison);
		}
	}

	/**
	 * Filter the query on the type_edt_semaine column
	 * 
	 * @param     string $typeEdtSemaine The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtTypeSemaineQuery The current query, for fluid interface
	 */
	public function filterByTypeEdtSemaine($typeEdtSemaine = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($typeEdtSemaine)) {
			return $this->addUsingAlias(EdtTypeSemainePeer::TYPE_EDT_SEMAINE, $typeEdtSemaine, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $typeEdtSemaine)) {
			return $this->addUsingAlias(EdtTypeSemainePeer::TYPE_EDT_SEMAINE, str_replace('*', '%', $typeEdtSemaine), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtTypeSemainePeer::TYPE_EDT_SEMAINE, $typeEdtSemaine, $comparison);
		}
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtTypeSemaine $edtTypeSemaine Object to remove from the list of results
	 *
	 * @return    EdtTypeSemaineQuery The current query, for fluid interface
	 */
	public function prune($edtTypeSemaine = null)
	{
		if ($edtTypeSemaine) {
			$this->addUsingAlias(EdtTypeSemainePeer::ID_EDT_SEMAINE, $edtTypeSemaine->getIdEdtSemaine(), Criteria::NOT_EQUAL);
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

} // BaseEdtTypeSemaineQuery
