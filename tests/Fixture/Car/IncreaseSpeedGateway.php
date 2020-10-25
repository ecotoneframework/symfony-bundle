<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\MessageGateway;

interface IncreaseSpeedGateway
{
    const CHANNEL_NAME = 'speedChannel';

    #[MessageGateway(IncreaseSpeedGateway::CHANNEL_NAME)]
    public function increaseSpeed(int $amount) : void;
}