<?php

namespace Modules\Ticket\Enums;

enum TicketStatusEnum:string {
    case OPEN = "open";
    case CLOSED = 'closed';
    case PENDING = 'pending';

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'باز',
            self::CLOSED => 'بسته شده',
            self::PENDING => 'در انتظار پاسخ',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::OPEN => 'success',
            self::CLOSED => 'info',
            self::PENDING => 'warning',
        };
    }

    public static function options(): array
    {
        return [
            self::CLOSED->value => self::CLOSED->label(),
            self::PENDING->value => self::PENDING->label(),
            self::OPEN->value => self::OPEN->label(),
        ];
    }
}
