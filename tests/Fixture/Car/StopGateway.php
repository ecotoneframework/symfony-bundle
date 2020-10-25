<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\MessageGateway;

interface StopGateway
{
    const CHANNEL_NAME = 'stopChannel';

    #[MessageGateway(StopGateway::CHANNEL_NAME)]
    public function stop() : void;
}