<?php

class BuildAnywhereVariant_OrderInterface extends OrderInterface {

    /**
     * Call the parent constructor transparently to keep things working
     * @param $gameID
     * @param $variantID
     * @param $user
     * @param $memberID
     * @param $turn
     * @param $phase
     * @param $country
     * @param setMemberOrderStatus $orderStatus
     * @param $tokenExpireTime
     * @param bool $maxOrderID
     */
	public function __construct($gameID, $variantID, $user, $memberID, $turn, $phase, $country,
		setMemberOrderStatus $orderStatus, $tokenExpireTime, $maxOrderID = false)
	{
		parent::__construct($gameID, $variantID, $user, $memberID, $turn, $phase, $country,
			$orderStatus, $tokenExpireTime, $maxOrderID);
	}

	protected function jsLoadBoard() {
		parent::jsLoadBoard();

		if( $this->phase->isBuilds())
		{
			// Expand the allowed SupplyCenters array to include non-home SCs.
			libHTML::$footerIncludes[] = l_jf('../variants/BuildAnywhere/resources/supplycenterscorrect.js');
			foreach(libHTML::$footerScript as $index=>$script)
				if(strpos($script, l_jf('loadBoard').'();') )
					libHTML::$footerScript[$index]=str_replace(l_jf('loadBoard').'();',l_jf('loadBoard').'();'.l_jf('SupplyCentersCorrect').'();', $script);
		}
	}


}