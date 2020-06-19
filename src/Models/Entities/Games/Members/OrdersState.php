<?php

namespace Diplomacy\Models\Entities\Games\Members;

/**
 *
if( $this->None )
return '- ';
elseif( $this->Ready )
return '<img src="'.l_s('images/icons/tick.png').'" alt="'.l_t('Ready').'" title="'.l_t('Ready to move to the next turn').'" /> ';
elseif( $this->Completed )
{
return '<img src="'.l_s('images/icons/tick_faded.png').'" alt="'.l_t('Completed').'" title="'.l_t('Orders completed, but not ready for next turn').'" /> ';
}
elseif( $this->Saved )
return '<img src="'.l_s('images/icons/alert_minor.png').'" alt="'.l_t('Saved').'" title="'.l_t('Orders saved, but not completed!').'" /> ';
else
return '<img src="'.l_s('images/icons/alert.png').'" alt="'.l_t('Not received').'" title="'.l_t('No orders submitted!').'" /> ';
 *
 */

/**
 * A collection of order statuses
 *
 * @package Diplomacy\Models\Entities\Games\Members
 */
class OrdersState
{
    const STATE_NONE = 'none'; // has no orders to fill
    const STATE_SAVED = 'saved'; // has saved orders, but not all orders are filled
    const STATE_COMPLETED = 'completed'; // has saved orders, and all orders are filled, but has not clicked ready
    const STATE_READY = 'ready'; // has saved all orders and clicked ready

    /** @var array $states Current states of the member */
    public $states;

    /**
     * @param array $states
     */
    public function __construct(array $states)
    {
        array_walk($states, function(&$v) { $v = strtolower($v); });
        $this->states = $states;
    }

    /**
     * Is the member's orders ready for processing?
     *
     * @return bool
     */
    public function readyForProcessing(): bool
    {
        return in_array('completed', $this->states) || in_array('ready', $this->states);
    }

    /**
     * Is this member's orders submitted?
     *
     * @return bool
     */
    public function submitted(): bool
    {
        if (empty($this->states)) return false;

        $submittedStates = [
            static::STATE_SAVED,
            static::STATE_COMPLETED,
            static::STATE_READY,
            static::STATE_NONE
        ];
        return count(array_intersect($this->states, $submittedStates)) > 0;
    }

    /**
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->hasState(OrdersState::STATE_READY);
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->hasState(OrdersState::STATE_COMPLETED);
    }

    /**
     * @param string $state
     * @return bool
     */
    public function hasState(string $state): bool
    {
        return in_array($state, $this->states);
    }

    /**
     * @param string $state
     * @return $this
     */
    public function addState(string $state): OrdersState
    {
        $this->states[] = $state;
        $this->states = array_unique($this->states);
        return $this;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function removeState(string $state): OrdersState
    {
        if (array_search($state, $this->states) !== false) {
            unset($this->states[$state]);
        }
        return $this;
    }

    /**
     * @param array $states
     * @return $this
     */
    public function setStates(array $states): OrdersState
    {
        $this->states = $states;
        return $this;
    }

    /**
     * @return string
     */
    public function icon(): string
    {
        if ($this->hasState(static::STATE_NONE)) {
            return '- ';

        } elseif ($this->hasState(static::STATE_READY)) {
            return '<img src="' . l_s('images/icons/tick.png') . '" alt="' . l_t('Ready') . '" title="' . l_t('Ready to move to the next turn') . '" /> ';

        } elseif ($this->hasState(static::STATE_COMPLETED)) {
            return '<img src="' . l_s('images/icons/tick_faded.png') . '" alt="' . l_t('Completed') . '" title="' . l_t('Orders completed, but not ready for next turn') . '" /> ';

        } elseif ($this->hasState(static::STATE_SAVED)) {
            return '<img src="' . l_s('images/icons/alert_minor.png') . '" alt="' . l_t('Saved') . '" title="' . l_t('Orders saved, but not completed!') . '" /> ';

        } else {
            return '<img src="'.l_s('images/icons/alert.png').'" alt="'.l_t('Not received').'" title="'.l_t('No orders submitted!').'" /> ';
        }
    }

    /**
     * @return string
     */
    public function iconText(): string
    {
        if ($this->hasState(static::STATE_NONE)) {
            return 'No orders to submit';

        } elseif ($this->hasState(static::STATE_READY)) {
            return 'Ready to move to the next turn';

        } elseif ($this->hasState(static::STATE_COMPLETED)) {
            return 'Orders completed, but not ready for next turn';

        } elseif ($this->hasState(static::STATE_SAVED)) {
            return 'Orders saved, but not completed!';

        } else {
            return 'No orders submitted!';
        }
    }

    /**
     * @return string
     */
    public function iconAnon(): string
    {
        if ($this->hasState(static::STATE_NONE)) {
            return '- ';

        } elseif ($this->hasState(static::STATE_READY)) {
            return '<img src="' . l_s('images/icons/lock.png') . '" alt="' . l_t('Anon') . '" title="' . l_t('This country has options this turn') . '" /> ';

        } elseif ($this->hasState(static::STATE_COMPLETED)) {
            return '<img src="' . l_s('images/icons/lock.png') . '" alt="' . l_t('Anon') . '" title="' . l_t('This country has options this turn') . '" /> ';

        } elseif ($this->hasState(static::STATE_SAVED)) {
            return '<img src="' . l_s('images/icons/lock.png') . '" alt="' . l_t('Anon') . '" title="' . l_t('This country has options this turn') . '" /> ';

        } else {
            return '<img src="' . l_s('images/icons/lock.png') . '" alt="' . l_t('Anon') . '" title="' . l_t('This country has options this turn') . '" /> ';
        }
    }

    /**
     * Legacy toSet function for migration assistance
     *
     * @return \setMemberOrderStatus
     */
    public function toSet() : \setMemberOrderStatus
    {
        return new \setMemberOrderStatus(implode(',', $this->states));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->states;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(',', $this->states);
    }
}