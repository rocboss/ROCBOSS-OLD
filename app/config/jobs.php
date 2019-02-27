<?php
/**
 * jobs config
 * @author ROC <i@rocs.me>
 */
return [
    // DEMO JOB
    [
        'name' => 'demoJob',
        'runTime' => '* * * * * *', // sec，min，hour，day，month，week
        'maxTime' => 600, // max time
        'controller' => 'api\HomeController',
        'action' => 'demoJob',
    ],
];