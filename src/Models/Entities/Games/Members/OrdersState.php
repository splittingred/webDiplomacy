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
    protected $states;

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
}