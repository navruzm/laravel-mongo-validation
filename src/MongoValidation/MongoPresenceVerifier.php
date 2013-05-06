<?php namespace MongoValidation;

use Illuminate\Validation as Validation;
use LMongo\Connection;

class MongoPresenceVerifier implements Validation\PresenceVerifierInterface {

	/**
	 * The database connection instance.
	 *
	 * @var  \LMongo\Connection
	 */
	protected $connection;

	/**
	 * Create a new database presence verifier.
	 *
	 * @param  \LMongo\Connection  $connection
	 * @return void
	 */
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Count the number of objects in a collection having the given value.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  string  $value
	 * @param  int     $excludeId
	 * @param  string  $idColumn
	 * @return int
	 */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array())
	{
		$query = array($column => $value);

		if ( ! is_null($excludeId))
		{
			$idColumn = $idColumn ?: '_id';
			$query[$idColumn] = array('$ne' => '_id' != $idColumn ? $idColumn : new MongoID($excludeId));
		}

		return $this->connection->{$collection}->find($query)->count();
	}

	/**
	 * Count the number of objects in a collection with the given values.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  array   $values
	 * @return int
	 */
    public function getMultiCount($collection, $column, array $values, array $extra = array())
	{
		if('_id' == $column)
		{
			array_map(function($value){ return new MongoID($value); }, $values);
		}

		return $this->connection->{$collection}->find(array($column => array('$in' => $values)))->count();
	}
}