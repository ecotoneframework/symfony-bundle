<?php

namespace Fixture\Car;

use Ecotone\Messaging\Annotation\MessageEndpoint;
use Ecotone\Messaging\Annotation\ServiceActivator;
use Fixture\Car\IncreaseSpeedGateway;
use Fixture\Car\GetSpeedGateway;
use Fixture\Car\StopGateway;

/**
 * Class Car
 * @package Fixture\Car
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @MessageEndpoint()
 */
class Car
{
    /**
     * @var int
     */
    private $speed = 0;

    /**
     * @param int $amount
     * @ServiceActivator(
     *     inputChannelName=IncreaseSpeedGateway::CHANNEL_NAME
     * )
     */
    public function increaseSpeed(int $amount) : void
    {
        $this->speed += $amount;
    }

    /**
     * @ServiceActivator(
     *     inputChannelName=StopGateway::CHANNEL_NAME
     * )
     */
    public function stop() : void
    {
        $this->speed = 0;
    }

    /**
     * @return int
     * @ServiceActivator(
     *     inputChannelName=GetSpeedGateway::CHANNEL_NAME
     * )
     */
    public function getCurrentSpeed() : int
    {
        return $this->speed;
    }
}