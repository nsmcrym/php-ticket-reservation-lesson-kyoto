<?php

declare(strict_types=1);

require_once BASEPASS . "/app/Domain/Event/EventPolicyInterface.php";

class PlasterStatueContest2026Policy implements EventPolicyInterface {
    public function eventTitle(): string {
        return "2026年石膏像デッサン選手権";
    }

    public function validateMaxTicketCount(int $quantity): bool {
        return 1 <= $quantity;
    }
}