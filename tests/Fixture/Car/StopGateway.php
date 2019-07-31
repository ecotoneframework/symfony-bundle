<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\Gateway;
use Ecotone\Messaging\Annotation\MessageEndpoint;

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
     * @Gateway(
     *     requestChannel=StopGateway::CHANNEL_NAME
     * )
     */
    public function stop() : void;
}