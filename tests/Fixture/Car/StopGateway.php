<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\MessageGateway;

/**
 * Interface StopGateway
 * @package Fixture\Car
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @MessageEndpoint()
 */
interface StopGateway
{
    const CHANNEL_NAME = 'stopChannel';

    /**
     * @MessageGateway(
     *     requestChannel=StopGateway::CHANNEL_NAME
     * )
     */
    public function stop() : void;
}