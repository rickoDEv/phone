<?php

namespace RickoDev\PhoneReset\Contracts;



interface PhoneTokenRepositoryInterface
{
    /**
     * Create a new token.
     *
     * @param CanResetPhonePassword $user
     * @return array
     */
    public function create(CanResetPhonePassword $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param CanResetPhonePassword $user
     * @param string $token
     * @return bool
     */
    public function exists(CanResetPhonePassword $user, #[\SensitiveParameter] $token,#[\SensitiveParameter] $otp);

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param CanResetPhonePassword $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPhonePassword $user);

    /**
     * Delete a token record.
     *
     * @param CanResetPhonePassword $user
     * @return void
     */
    public function delete(CanResetPhonePassword $user);

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired();
}
