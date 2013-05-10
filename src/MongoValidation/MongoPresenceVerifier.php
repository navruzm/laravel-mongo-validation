<?php namespace MongoValidation;

use Illuminate\Validation as Validation;
use LMongo\ConnectionResolverInterface;

class MongoPresenceVerifier implements Validation\PresenceVerifierInterface {

	/**
	 * The database connection instance.
	 *
	 * @var  \LMongo\ConnectionResolverInterface
	 */
	protected $db;

	/**
	 * The database connection to use.
	 *
	 * @var string
	 */
	protected $connection = null;

	/**
	 * Create a new database presence verifier.
	 *
	 * @param  \LMongo\ConnectionResolverInterface  $db
	 * @return void
	 */
	public function __construct(ConnectionResolverInterface $db)
	{
		$this->db = $db;
	}

	/**
	 * Count the number of objects in a collection having the given value.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  string  $value
	 * @param  int     $excludeId
	 * @param  string  $idColumn
	 * @param  array   $extra
	 * @return int
	 */
	public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array())
	{
		$query = $this->collection($collection)->where($column, $value);

		if ( ! is_null($excludeId))
		{
			$idColumn = $idColumn ?: '_id';

			$query->whereNe($idColumn, '_id' != $idColumn ? $idColumn : new MongoID($excludeId));
		}

		foreach ($extra as $key => $extraValue)
		{
			$query->where($key, $extraValue);
		}

		return $query->count();
	}

	/**
	 * Count the number of objects in a collection with the given values.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  array   $values
	 * @param  array   $extra
	 * @return int
	 */
	public function getMultiCount($collection, $column, array $values, array $extra = array())
	{
		if('_id' == $column)
		{
			$values = array_map(function($value){ return new MongoID($value); }, $values);
		}

		$query = $this->collection($collection)->whereIn($column, $values);

		foreach ($extra as $key => $extraValue)
		{
			$query->where($key, $extraValue);
		}

		return $query->count();
	}

	/**
	 * Get a query builder for the given collection.
	 *
	 * @param  string  $collection
	 * @return \LMongo\Query\Builder
	 */
	protected function collection($collection)
	{
		return $this->db->connection($this->connection)->collection($collection);
	}

	/**
	 * Set the connection to be used.
	 *
	 * @param  string  $connection
	 * @return void
	 */
	public function setConnection($connection)
	{
		$this->connection = $connection;
	}

}