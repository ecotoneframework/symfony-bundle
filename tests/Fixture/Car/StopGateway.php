<?php

namespace Fixture\Car;

use SimplyCodedSoftware\Messaging\Annotation\Gateway;
use SimplyCodedSoftware\Messaging\Annotation\MessageEndpoint;

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