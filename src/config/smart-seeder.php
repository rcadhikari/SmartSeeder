<?php

return array(
    /**
     * DO NOT CHANGE THIS unless you also change the included migration, since this references
     * the actual table in your database
     */
    'seedTable' => 'seeds',
    'seedFileTable' => 'seeds_files',
    //'seedDir' => 'database/seeds/demo',
    'seedDir' => 'seeds/clients',
    'seedFileDir' => 'clients/{client}/seeds',
    'seedDataFileDir' => '/data',
    //'seedDir' => 'seeds/clients',

    'seedMasterDir' => 'master_database/seeds/',
    'seedMasterFile' => 'masterDatabaseSeeder.php',
);
