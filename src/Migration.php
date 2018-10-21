<?php

namespace CandleLight\Artisan;

use CandleLight\App;
use CandleLight\Database;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Basic DB-Migration Template
 * @package CandleLight\Artisan
 */
abstract class Migration{

    private $app;
    private $db;

    /**
     * Migration constructor.
     * @param App $app
     */
    public function __construct(App $app){
        $this->app = $app;
        $this->db = $app->getDb();
    }

    /**
     * Returns the current CDL application instance
     * @return App
     */
    public function getApp(): App{
        return $this->app;
    }

    /**
     * Returns the CDL applications database instance
     * @return Database
     */
    public function getDb(): Database{
        return $this->db;
    }

    /**
     * Returns the Schema Builder for the Applications Database instance
     * @return Builder
     */
    public function getSchema(){
        return $this->db->getBuilder();
    }

    /**
     * Method called, when the migration is executed
     */
    public abstract function up(): void;

    /**
     * Method called, when the migration is rolled back
     */
    public abstract function down(): void;
}