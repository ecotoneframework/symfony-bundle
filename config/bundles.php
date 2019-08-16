<?php

use Ecotone\Symfony\EcotoneSymfonyBundle;
use Fixture\TestBundle;

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    TestBundle::class => ['all' => true],
    EcotoneSymfonyBundle::class => ['all' => true]
];
