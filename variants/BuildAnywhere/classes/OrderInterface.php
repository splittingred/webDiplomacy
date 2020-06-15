<?php

class BuildAnywhereVariant_OrderInterface extends OrderInterface
{
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