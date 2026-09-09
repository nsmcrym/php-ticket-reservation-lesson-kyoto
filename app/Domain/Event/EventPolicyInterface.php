<?php

declare(strict_types=1);

interface EventPolicyInterface{
    public function eventTitle(): string;
    //一回のチケットの申し込み枚数制限
    public function validateMaxTicketCount(int $quantity): bool;
}