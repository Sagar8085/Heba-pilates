<?php

declare(strict_types=1);

namespace App\Types;

final class BookingTableStatus
{
    private function __construct(
        public string $status = 'closed',
        public string $notWorkingReason = '',
        public int $memberId = 0,
        public string $memberName = '',
        public bool $isMemberFirstSession = false,
        public bool $memberHasCreditsRemaining = false,
        public array $memberRequiresAttentionBecause = [],
        public bool $memberHasCompletedPARQ = false,
        public bool $memberHasNewNote = false,
    ) {
    }

    /**
     * @return self
     */
    public static function closed(): self
    {
        return new self('closed');
    }

    /**
     * @return self
     */
    public static function available(): self
    {
        return new self('available');
    }

    /**
     * @return self
     */
    public static function unavailable(): self
    {
        return new self('unavailable');
    }

    /**
     * @param string $reason
     *
     * @return self
     */
    public static function notWorking(string $reason): self
    {
        return new self('notWorking', $reason);
    }

    /**
     * @param int $memberId
     * @param string $memberName
     * @param bool $isMemberFirstSession
     * @param bool $memberHasCreditsRemaining
     * @param array $memberRequiresAttentionBecause
     * @param bool $memberHasCompletedPARQ
     * @param bool $memberHasNewNote
     *
     * @return self
     */
    public static function booking(
        int $memberId,
        string $memberName,
        bool $isMemberFirstSession,
        bool $memberHasCreditsRemaining,
        array $memberRequiresAttentionBecause,
        bool $memberHasCompletedPARQ,
        bool $memberHasNewNote,
    ): self {
        return new self(
            'booking',
            '',
            $memberId,
            $memberName,
            $isMemberFirstSession,
            $memberHasCreditsRemaining,
            $memberRequiresAttentionBecause,
            $memberHasCompletedPARQ,
            $memberHasNewNote,
        );
    }

    /**
     * @return static
     */
    public static function blockedOut(): self
    {
        return new self('blockedOut');
    }

    /**
     * @return static
     */
    public static function trainerOnBreak(): self
    {
        return new self('trainerOnBreak');
    }
}
