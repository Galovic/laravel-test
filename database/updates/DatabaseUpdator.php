<?php

abstract class DatabaseUpdator
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Run the database update.
     *
     * @return void
     */
    abstract public function run();

    /**
     * Resolve an instance of the given seeder class.
     *
     * @param  string  $class
     * @return \Illuminate\Database\Seeder
     */
    protected function resolve($class)
    {
        return $this->container->make($class);
    }

    /**
     * Run a migration inside a transaction if the database supports it.
     *
     * @param  string  $migration
     * @param  string  $method
     * @return void
     */
    protected function runSingleMigration($class, $method)
    {
        $instance = new $class;

        $reflector = new ReflectionClass($class);
        $fileName = pathinfo($reflector->getFileName(), PATHINFO_FILENAME);

        $connection = DB::connection();

        $nextBatch = DB::table('migrations')->max('batch') + 1;

        $callback = function () use ($instance, $method) {
            $instance->$method();
        };

        $grammar = $this->getSchemaGrammar($connection);

        $grammar->supportsSchemaTransactions()
            ? $connection->transaction($callback)
            : $callback();

        DB::table('migrations')->insert([
            'migration' => $fileName,
            'batch' => $nextBatch
        ]);
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return $this
     */
    public function setContainer(\Illuminate\Container\Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get the schema grammar out of a migration connection.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected function getSchemaGrammar($connection)
    {
        if (is_null($grammar = $connection->getSchemaGrammar())) {
            $connection->useDefaultSchemaGrammar();

            $grammar = $connection->getSchemaGrammar();
        }

        return $grammar;
    }
}