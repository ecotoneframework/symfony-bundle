<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\MessageGateway;

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