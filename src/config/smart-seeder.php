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
    'coreSeedFileDir' => 'database/seeds/core',

    /* For Client Seeder */
    'clientSeedDir' => 'seeds/clients',
    'clientSeedFileDir' => 'clients/{client}/seeds',

    /* For Master Seeder */
    'masterSeedFileDir' => 'master_database/seeds',

    /* For Translation Seeder */
    'translationSeedFileDir' => 'database/seeds/translation'
);
