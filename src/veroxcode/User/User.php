<?php

namespace veroxcode\User;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\InputMode;
use veroxcode\Buffers\AttackFrame;
use veroxcode\Buffers\MovementFrame;
use veroxcode\Guardian;
use veroxcode\Utils\Arrays;
use veroxcode\Utils\Random;

class User
{

    private CONST MOVEMENT_BUFFER_SIZE = 100;
    private CONST ATTACK_BUFFER_SIZE = 100;

    private Vector3 $motion;

    private string $uuid;

    private bool $notifications = true;
    private bool $punishNext = false;

    private float $moveForward = 0.0;
    private float $moveStrafe = 0.0;

    private int $lastKnockbackTick = 0;
    private int $firstServerTick = 0;
    private int $firstClientTick = 0;
    private int $lastAttack = 0;
    private int $tickDelay = 0;
    private int $input = 0;

    private array $movementBuffer = [];
    private array $attackBuffer = [];
    private array $violations = [];
    private array $alerts = [];

    /**
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->motion = Vector3::zero();
        $config = Guardian::getInstance()->getConfig();

        foreach (Guardian::getInstance()->getCheckManager()->getChecks() as $Check){

            $frequency = $config->get($Check->getName() . "-AlertFrequency");

            $this->violations[$Check->getName()] = 0;
            $this->alerts[$Check->getName()] = $frequency;
        }
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function addToMovementBuffer(MovementFrame $object) : void
    {
        $size = count($this->movementBuffer);

        if ($size >= ($this::MOVEMENT_BUFFER_SIZE)){
            $this->movementBuffer = Arrays::removeFirst($this->movementBuffer);
        }
        $this->movementBuffer[$size] = $object;
    }

    public function rewindMovementBuffer(int $ticks = 1) : MovementFrame
    {
        $size = count($this->movementBuffer) - 1;
        return $this->movementBuffer[$size - $ticks];
    }

    public function getMovementBuffer(): array
    {
        return $this->movementBuffer;
    }

    public function addToAttackBuffer(AttackFrame $object) : void
    {
        $size = count($this->attackBuffer);

        if ($size >= ($this::ATTACK_BUFFER_SIZE)){
            $this->attackBuffer = Arrays::removeFirst($this->attackBuffer);
        }
        $this->attackBuffer[$size] = $object;
    }

    public function rewindAttackBuffer(int $ticks = 1) : AttackFrame
    {
        $size = count($this->attackBuffer) - 1;
        return $this->attackBuffer[$size - $ticks];
    }

    public function getAttackBuffer(): array
    {
        return $this->attackBuffer;
    }

    public function increaseViolation(string $Check, $amount = 1) : void
    {
        $this->violations[$Check] = Random::clamp(0, PHP_INT_MAX, $this->violations[$Check] + $amount);
    }

    public function decreaseViolation(string $Check, $amount = 1) : void
    {
        $this->violations[$Check] = Random::clamp(0, PHP_INT_MAX, $this->violations[$Check] - $amount);
    }

    public function resetViolation(string $Check) : void
    {
        $this->violations[$Check] = 0;
    }

    public function getViolation(string $Check) : int
    {
        return $this->violations[$Check];
    }

    public function increaseAlertCount(string $Check, $amount = 1) : void
    {
        $this->alerts[$Check] = Random::clamp(0, PHP_INT_MAX, $this->alerts[$Check] + $amount);
    }

    public function decreaseAlertCount(string $Check, $amount = 1) : void
    {
        $this->alerts[$Check] = Random::clamp(0, PHP_INT_MAX, $this->alerts[$Check] - $amount);
    }

    public function resetAlertCount(string $Check) : void
    {
        $this->alerts[$Check] = 0;
    }

    public function getAlertCount(string $Check) : int
    {
        return $this->alerts[$Check];
    }

    public function getFirstServerTick(): int
    {
        return $this->firstServerTick;
    }

    public function setFirstServerTick(int $firstServerTick): void
    {
        $this->firstServerTick = $firstServerTick;
    }

    public function getFirstClientTick(): int
    {
        return $this->firstClientTick;
    }

    public function setFirstClientTick(int $firstClientTick): void
    {
        $this->firstClientTick = $firstClientTick;
    }

    public function getTickDelay(): int
    {
        return $this->tickDelay;
    }

    public function setTickDelay(int $tickDelay): void
    {
        $this->tickDelay = $tickDelay;
    }

    public function getInput(): int
    {
        return $this->input;
    }

    public function setInput(int $input): void
    {
        $this->input = $input;
    }

    public function getLastAttack(): int
    {
        return $this->lastAttack;
    }

    public function setLastAttack(int $lastAttack): void
    {
        $this->lastAttack = $lastAttack;
    }

    public function hasNotifications(): bool
    {
        return $this->notifications;
    }

    public function setNotifications(bool $notifications): void
    {
        $this->notifications = $notifications;
    }

    public function getLastKnockbackTick(): int
    {
        return $this->lastKnockbackTick;
    }

    public function setLastKnockbackTick(int $lastKnockbackTick): void
    {
        $this->lastKnockbackTick = $lastKnockbackTick;
    }

    public function getMotion(): Vector3
    {
        return $this->motion;
    }

    public function setMotion(Vector3 $motion): void
    {
        $this->motion = $motion;
    }

    public function getMoveForward(): float
    {
        return $this->moveForward;
    }

    public function setMoveForward(float $moveForward): void
    {
        $this->moveForward = $moveForward;
    }

    public function getMoveStrafe(): float
    {
        return $this->moveStrafe;
    }

    public function setMoveStrafe(float $moveStrafe): void
    {
        $this->moveStrafe = $moveStrafe;
    }

    public function isPunishNext(): bool
    {
        return $this->punishNext;
    }

    public function setPunishNext(bool $punishNext): void
    {
        $this->punishNext = $punishNext;
    }

}