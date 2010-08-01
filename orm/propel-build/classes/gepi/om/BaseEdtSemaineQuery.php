<?php


/**
 * Base class that represents a query for the 'edt_semaines' table.
 *
 * Liste des semaines de l'annee scolaire courante - 53 enregistrements obligatoires (pas 52!), pour lesquel on assigne un type (A ou B par exemple)
 *
 * @method     EdtSemaineQuery orderByIdEdtSemaine($order = Criteria::ASC) Order by the id_edt_semaine column
 * @method     EdtSemaineQuery orderByNumEdtSemaine($order = Criteria::ASC) Order by the num_edt_semaine column
 * @method     EdtSemaineQuery orderByTypeEdtSemaine($order = Criteria::ASC) Order by the type_edt_semaine column
 * @method     EdtSemaineQuery orderByNumSemainesEtab($order = Criteria::ASC) Order by the num_semaines_etab column
 *
 * @method     EdtSemaineQuery groupByIdEdtSemaine() Group by the id_edt_semaine column
 * @method     EdtSemaineQuery groupByNumEdtSemaine() Group by the num_edt_semaine column
 * @method     EdtSemaineQuery groupByTypeEdtSemaine() Group by the type_edt_semaine column
 * @method     EdtSemaineQuery groupByNumSemainesEtab() Group by the num_semaines_etab column
 *
 * @method     EdtSemaineQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtSemaineQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtSemaineQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtSemaine findOne(PropelPDO $con = null) Return the first EdtSemaine matching the query
 * @method     EdtSemaine findOneOrCreate(PropelPDO $con = null) Return the first EdtSemaine matching the query, or a new EdtSemaine object populated from the query conditions when no match is found
 *
 * @method     EdtSemaine findOneByIdEdtSemaine(int $id_edt_semaine) Return the first EdtSemaine filtered by the id_edt_semaine column
 * @method     EdtSemaine findOneByNumEdtSemaine(int $num_edt_semaine) Return the first EdtSemaine filtered by the num_edt_semaine column
 * @method     EdtSemaine findOneByTypeEdtSemaine(string $type_edt_semaine) Return the first EdtSemaine filtered by the type_edt_semaine column
 * @method     EdtSemaine findOneByNumSemainesEtab(int $num_semaines_etab) Return the first EdtSemaine filtered by the num_semaines_etab column
 *
 * @method     array findByIdEdtSemaine(int $id_edt_semaine) Return EdtSemaine objects filtered by the id_edt_semaine column
 * @method     array findByNumEdtSemaine(int $num_edt_semaine) Return EdtSemaine objects filtered by the num_edt_semaine column
 * @method     array findByTypeEdtSemaine(string $type_edt_semaine) Return EdtSemaine objects filtered by the type_edt_semaine column
 * @method     array findByNumSemainesEtab(int $num_semaines_etab) Return EdtSemaine objects filtered by the num_semaines_etab column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtSemaineQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEdtSemaineQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtSemaine', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtSemaineQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtSemaineQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtSemaineQuery) {
			return $criteria;
		}
		$query = new EdtSemaineQuery();
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
	 * @return    EdtSemaine|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = EdtSemainePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
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
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtSemainePeer::ID_EDT_SEMAINE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtSemainePeer::ID_EDT_SEMAINE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_edt_semaine column
	 * 
	 * @param     int|array $idEdtSemaine The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function filterByIdEdtSemaine($idEdtSemaine = null, $comparison = null)
	{
		if (is_array($idEdtSemaine) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(EdtSemainePeer::ID_EDT_SEMAINE, $idEdtSemaine, $comparison);
	}

	/**
	 * Filter the query on the num_edt_semaine column
	 * 
	 * @param     int|array $numEdtSemaine The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function filterByNumEdtSemaine($numEdtSemaine = null, $comparison = null)
	{
		if (is_array($numEdtSemaine)) {
			$useMinMax = false;
			if (isset($numEdtSemaine['min'])) {
				$this->addUsingAlias(EdtSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($numEdtSemaine['max'])) {
				$this->addUsingAlias(EdtSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtSemainePeer::NUM_EDT_SEMAINE, $numEdtSemaine, $comparison);
	}

	/**
	 * Filter the query on the type_edt_semaine column
	 * 
	 * @param     string $typeEdtSemaine The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function filterByTypeEdtSemaine($typeEdtSemaine = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($typeEdtSemaine)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $typeEdtSemaine)) {
				$typeEdtSemaine = str_replace('*', '%', $typeEdtSemaine);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtSemainePeer::TYPE_EDT_SEMAINE, $typeEdtSemaine, $comparison);
	}

	/**
	 * Filter the query on the num_semaines_etab column
	 * 
	 * @param     int|array $numSemainesEtab The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function filterByNumSemainesEtab($numSemainesEtab = null, $comparison = null)
	{
		if (is_array($numSemainesEtab)) {
			$useMinMax = false;
			if (isset($numSemainesEtab['min'])) {
				$this->addUsingAlias(EdtSemainePeer::NUM_SEMAINES_ETAB, $numSemainesEtab['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($numSemainesEtab['max'])) {
				$this->addUsingAlias(EdtSemainePeer::NUM_SEMAINES_ETAB, $numSemainesEtab['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtSemainePeer::NUM_SEMAINES_ETAB, $numSemainesEtab, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtSemaine $edtSemaine Object to remove from the list of results
	 *
	 * @return    EdtSemaineQuery The current query, for fluid interface
	 */
	public function prune($edtSemaine = null)
	{
		if ($edtSemaine) {
			$this->addUsingAlias(EdtSemainePeer::ID_EDT_SEMAINE, $edtSemaine->getIdEdtSemaine(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseEdtSemaineQuery
