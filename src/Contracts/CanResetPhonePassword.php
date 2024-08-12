<?php

namespace RickoDev\PhoneReset\Contracts;

interface CanResetPhonePassword
{

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getPhoneForPasswordReset(): string;

    /**
     * Send the password reset notification.
     *
     * @param  int  $otp
     * @return void
     */
    public function sendPhonePasswordResetNotification($otp): void;

}
