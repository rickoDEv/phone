<?php

namespace RickoDev\PhoneReset\Contracts;

use RickoDev\PhoneReset\Contracts\PhonePasswordBroker;

interface PhonePasswordBrokerFactory
{
    /**
     * Get a password broker instance by name.
     *
     * @param  string|null  $name
     * @return PhonePasswordBroker
     */
    public function broker($name = null);
}
