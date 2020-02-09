<?php

return [
    /**
     * The namespace relative to the app_path. This creates all the neccessary
     * directories according to the namespace thingamabooble.
     */
    'namespace' => 'Settings',

    /**
     * The mapping of the models to the classes.
     */
    'classes' => [
        App\Model::class => App\Settings\ModelSettings::class,
    ],
];
