<?php

namespace Gambling\Common\Bus;

interface Bus
{
    /**
     * Handle the given command.
     *
     * @param mixed $command
     *
     * @return mixed
     * @throws \Exception Any application based exception
     */
    public function handle($command);
}
