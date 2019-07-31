<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\Gateway;
use Ecotone\Messaging\Annotation\MessageEndpoint;

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
     * @Gateway(
     *     requestChannel=IncreaseSpeedGateway::CHANNEL_NAME
     * )
     * @param int $amount
     */
    public function increaseSpeed(int $amount) : void;
}