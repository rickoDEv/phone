<?php

namespace RickoDev\PhoneReset;

use RickoDev\PhoneReset\Contracts\CanResetPhonePassword;
use RickoDev\PhoneReset\Contracts\PhonePasswordBroker;
use RickoDev\PhoneReset\Contracts\PhoneTokenRepositoryInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PhonePasswordBroker broker(string|null $name = null)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static string sendResetLink(array $credentials, \Closure|null $callback = null)
 * @method static mixed reset(array $credentials, \Closure $callback)
 * @method static CanResetPhonePassword|null getUser(array $credentials)
 * @method static string createToken(CanResetPhonePassword $user)
 * @method static void deleteToken(CanResetPhonePassword $user)
 * @method static bool tokenExists(CanResetPhonePassword $user, string $token)
 * @method static PhoneTokenRepositoryInterface getRepository()
 *
 * @see PhonePasswordBrokerManager
 * @see PhonePasswordBroker
 */

class PhoneReset extends Facade
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const RESET_LINK_SENT = Contracts\PhonePasswordBroker::RESET_LINK_SENT;

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PASSWORD_RESET = Contracts\PhonePasswordBroker::PASSWORD_RESET;

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = Contracts\PhonePasswordBroker::INVALID_USER;

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = Contracts\PhonePasswordBroker::INVALID_TOKEN;

    /**
     * Constant representing a throttled reset attempt.
     *
     * @var string
     */
    const RESET_THROTTLED = Contracts\PhonePasswordBroker::RESET_THROTTLED;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.phone-password';
    }
}
