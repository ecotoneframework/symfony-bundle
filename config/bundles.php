<?php

use Ecotone\Symfony\EcotoneBundle;
use Fixture\TestBundle;

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    TestBundle::class => ['all' => true],
    EcotoneBundle::class => ['all' => true]
];
