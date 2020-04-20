<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\MessageGateway;

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
     * @MessageGateway(
     *     requestChannel=GetSpeedGateway::CHANNEL_NAME
     * )
     */
    public function getSpeed() : int;
}