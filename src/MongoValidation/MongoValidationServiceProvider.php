<?php namespace MongoValidation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation as Validation;

class MongoValidationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerPresenceVerifier();

		$this->app['validator'] = $this->app->share(function($app)
		{
			$validator = new Validation\Factory($app['translator']);

			// The validation presence verifier is responsible for determing the existence
			// of values in a given data collection, typically a relational database or
			// other persistent data stores. And it is used to check for uniqueness.
			if (isset($app['validation.presence']))
			{
				$validator->setPresenceVerifier($app['validation.presence']);
			}

			return $validator;
		});
	}

	/**
	 * Register the database presence verifier.
	 *
	 * @return void
	 */
	protected function registerPresenceVerifier()
	{
		$this->app['validation.presence'] = $this->app->share(function($app)
		{
			return new MongoPresenceVerifier($app['mongo']->connection());
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('validator', 'validation.presence');
	}

}