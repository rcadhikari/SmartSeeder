<?php

return array(
    /**
     * DO NOT CHANGE THIS unless you also change the included migration, since this references
     * the actual table in your database
     */
    'seedTable' => 'seeds',
    'seedFileTable' => 'seeds_files',

    'dataFileDir' => '/data',

    /* For Default Seeder */
    'seedDir' => 'seeds/clients',
    'seedFileDir' => 'database/seeds/core',

    /* For Core Seeder */
    //'coreSeedDir' => 'seeds/clients',
    'coreSeedFileDir' => 'database/seeds/core1',

    /* For Client Seeder */
    'clientSeedDir' => 'seeds/clients',
    'clientSeedFileDir' => 'clients/{client}/seeds',

    /* For Master Seeder */
    'seedMasterDir' => 'master_database/seeds/',
    'seedMasterFile' => 'MasterDatabaseSeeder.php',

    /* For Master Seeder */
    'masterSeedDir' => 'seeds/clients',
    'masterSeedFileDir' => 'clients/{client}/seeds'
);
