<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\MessageGateway;

/**
 * Interface IncreaseSpeedGateway
 * @package Fixture\Car
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @MessageEndpoint()
 */
interface IncreaseSpeedGateway
{
    const CHANNEL_NAME = 'speedChannel';

    /**
     * @MessageGateway(
     *     requestChannel=IncreaseSpeedGateway::CHANNEL_NAME
     * )
     * @param int $amount
     */
    public function increaseSpeed(int $amount) : void;
}