<?php

namespace RickoDev\PhoneReset;

use InvalidArgumentException;
use RickoDev\PhoneReset\Contracts\CanResetPhonePassword as CanResetPasswordContract;
use RickoDev\PhoneReset\Contracts\PhoneTokenRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Random\RandomException;

class PhoneDatabaseTokenRepository implements PhoneTokenRepositoryInterface
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Minimum number of seconds before re-redefining the token.
     *
     * @var int
     */
    protected $throttle;

    /**
     * Create a new token repository instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $table
     * @param  string  $hashKey
     * @param  int  $expires
     * @param  int  $throttle
     * @return void
     */
    public function __construct(ConnectionInterface $connection, HasherContract $hasher,
                                $table, $hashKey, $expires = 60,
                                $throttle = 60)
    {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
        $this->connection = $connection;
        $this->throttle = $throttle;
    }

    /**
     * Create a new token record.
     *
     * @param CanResetPasswordContract $user
     * @return array
     */
    public function create(CanResetPasswordContract $user)
    {
        $phone = $user->getPhoneForPasswordReset();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $otp = $this->createOneTimePassword();

        $this->getTable()->insert($this->getPayload($phone, $token,$otp));

        return [$token,$otp];
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param CanResetPasswordContract $user
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where('phone_number', $user->getPhoneForPasswordReset())->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param  string  $phoneNumber
     * @param  string  $token
     * @return array
     */
    protected function getPayload($phoneNumber, #[\SensitiveParameter] $token,#[\SensitiveParameter] $otp)
    {
        return ['phone_number' => $phoneNumber, 'token' => $this->hasher->make($token), 'created_at' => new Carbon,'otp' => $otp];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param CanResetPasswordContract $user
     * @param string $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, #[\SensitiveParameter] $token,#[\SensitiveParameter] $otp)
    {
        $record = (array) $this->getTable()->where(
            'phone_number', $user->getPhoneForPasswordReset()
        )->where(
            'otp', $otp
        )->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
                 $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param CanResetPasswordContract $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = (array) $this->getTable()->where(
            'phone_number', $user->getPhoneForPasswordReset()
        )->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Determine if the token was recently created.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenRecentlyCreated($createdAt)
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)->addSeconds(
            $this->throttle
        )->isFuture();
    }

    /**
     * Delete a token record by user.
     *
     * @param CanResetPasswordContract $user
     * @return void
     */
    public function delete(CanResetPasswordContract $user)
    {
        $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * @throws RandomException
     */
    public function createOneTimePassword($length = 4): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException("Length must be at least 1");
        }

        // Generate a cryptographically secure random integer
        $max = pow(10, $length) - 1;
        $min = pow(10, $length - 1);
        $otp = random_int($min, $max);

        return str_pad($otp, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Get the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the hasher instance.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }
}
