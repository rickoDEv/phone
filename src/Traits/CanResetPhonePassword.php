<?php

namespace RickoDev\PhoneReset\Traits;

trait CanResetPhonePassword
{


    /**
     * Get the phone number to send otp to.
     *
     * @return string
     */
    public function getPhoneForPasswordReset(): string
    {

        return  $this->phone_number;
    }

    /**
     * Send the password reset notification.
     *
     * @param int $otp
     * @return void
     */
    public function sendPhonePasswordResetNotification(int $otp): void
    {

        //  send sms notification

    }

}
