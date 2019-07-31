<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\Gateway;
use Ecotone\Messaging\Annotation\MessageEndpoint;

/**
 * Interface GetSpeedGateway
 * @package Fixture\Car
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @MessageEndpoint()
 */
interface GetSpeedGateway
{
    const CHANNEL_NAME = 'getSpeedChannel';

    /**
     * @return int
     * @Gateway(
     *     requestChannel=GetSpeedGateway::CHANNEL_NAME
     * )
     */
    public function getSpeed() : int;
}