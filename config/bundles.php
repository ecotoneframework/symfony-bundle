<?php

use Ecotone\Symfony\IntegrationMessagingBundle;
use Fixture\TestBundle;

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    TestBundle::class => ['all' => true],
    IntegrationMessagingBundle::class => ['all' => true]
];
