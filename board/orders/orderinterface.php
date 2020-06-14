<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('IN_CODE') or die('This script can not be run by itself.');

require_once(l_r('board/orders/order.php'));

require_once(l_r('board/orders/diplomacy.php'));
require_once(l_r('board/orders/retreats.php'));
require_once(l_r('board/orders/builds.php'));

/**
 * A class for submission/retrieval of orders; usable internally or externally
 *
 * __construct
 * => This data is required to load: [gameID, userID, memberID, turn, phase, countryID]
 * 		This must either be in the context of a GameMaster account, a loaded Game/User/userMember set, or a JSON token.
 *
 * loadOrders
 * => Current order data can then be loaded via a JSON token [tokenKey,{orderID,completeStatus,{data}}]
 * 		, if provided with the above token. Or from the DB.
 *
 * getToken / getOrders
 * Once current order data is loaded:
 * <= [gameID, ...] token can be returned
 * <= [tokenKey,{orderID,completeStatus,{data}}] can be returned for the given user.
 *
 * setOrders
 * => To alter orders once loaded this data is provided: lock,{orderID,{input}}
 * 		<= {orderID, completeStatus, error} will be returned
 *
 * @package Board
 * @subpackage Orders
 */
class OrderInterface
{
	/**
	 * @return OrderInterface
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public static function newBoard() {
		global $Game, $Member;
		global $app;
		$User = $app->make('user');
		return self::newContext($Game, $Member, $User);
	}
	public static function newContext(Game $Game, userMember $Member, User $User)
    {
        $gameEntity = static::loadGameEntity((int)$Game->id);

        $userEntity = new \Diplomacy\Models\Entities\User();
        $userEntity->id = $User->id;
        $memberEntity = new \Diplomacy\Models\Entities\Games\Member();
        $memberEntity->id = $Member->id;
	    $country = new \Diplomacy\Models\Entities\Games\Country($Member->countryID, '');
	    $turn = new \Diplomacy\Models\Entities\Games\Turn($Game->turn, '');
        $phase = new \Diplomacy\Models\Entities\Games\Phase($Game->phase, 0,0);
		return $Game->Variant->OrderInterface(
		    $gameEntity,
            $userEntity,
            $memberEntity,
            $turn,
            $phase,
            $country,
			$Member->orderStatus,
            $Game->processTime+6*60*60
        );
	}

	public static function loadGameEntity(int $gameId)
    {
        $gameModel = \Diplomacy\Models\Game::find($gameId);
		$gameFactory = new \Diplomacy\Services\Games\Factory();
		$gameEntity = $gameFactory->build($gameModel);
		libVariant::setGlobals($gameEntity->variant);
		return $gameEntity;
    }


	public static function newJSON($key, $json)
    {

        require_once ROOT_PATH . 'lib/variant.php';
        require_once ROOT_PATH . 'objects/basic/set.php';

		$inContext = (array)json_decode($json);
		$authContext = self::getContext($inContext);
		$inContext = $authContext['context'];

		if ($authContext['key'] != $key) {
            throw new Exception("JSON token given is invalid");
        }

		$gameEntity = static::loadGameEntity((int)$authContext['gameID']);

		// TODO: Verify these
        $userEntity = new \Diplomacy\Models\Entities\User();
        $userEntity->id = $inContext['userID'];
        $memberEntity = new \Diplomacy\Models\Entities\Games\Member();
        $memberEntity->id = $inContext['memberID'];
        $turnEntity = new \Diplomacy\Models\Entities\Games\Turn($inContext['turn'], '');
        $phaseEntity = new \Diplomacy\Models\Entities\Games\Phase($inContext['phase'], 0, 0);
        $countryEntity = new \Diplomacy\Models\Entities\Games\Country($inContext['countryID'], '');
		return $gameEntity->variant->OrderInterface(
		    $gameEntity,
            $userEntity,
            $memberEntity,
            $turnEntity,
            $phaseEntity,
            $countryEntity,
			new setMemberOrderStatus($inContext['orderStatus']),
            $inContext['tokenExpireTime'],
            $inContext['maxOrderID']
        );
	}

	/** @var \Diplomacy\Models\Entities\Game */
	public $game;
	protected $variantID;
	protected $user;
	/** @var \Diplomacy\Models\Entities\Games\Member $member */
	protected $member;
	/** @var \Diplomacy\Models\Entities\Games\Turn $turn */
	protected $turn;
	/** @var \Diplomacy\Models\Entities\Games\Phase $phase */
	protected $phase;
	/** @var \Diplomacy\Models\Entities\Games\Country $country */
	protected $country;
	public $orderStatus;
	protected $tokenExpireTime;
	protected $maxOrderID;

    /**
     * OrderInterface constructor.
     * @param \Diplomacy\Models\Entities\Game $game
     * @param \Diplomacy\Models\Entities\User $user
     * @param \Diplomacy\Models\Entities\Games\Member $member
     * @param \Diplomacy\Models\Entities\Games\Turn $turn
     * @param \Diplomacy\Models\Entities\Games\Phase $phase
     * @param \Diplomacy\Models\Entities\Games\Country $country
     * @param setMemberOrderStatus $orderStatus
     * @param $tokenExpireTime
     * @param bool $maxOrderID
     */
	public function __construct(
        \Diplomacy\Models\Entities\Game $game,
        \Diplomacy\Models\Entities\User $user,
        \Diplomacy\Models\Entities\Games\Member $member,
        \Diplomacy\Models\Entities\Games\Turn $turn,
        \Diplomacy\Models\Entities\Games\Phase $phase,
        \Diplomacy\Models\Entities\Games\Country $country,
		setMemberOrderStatus $orderStatus,
        $tokenExpireTime,
        $maxOrderID=false
    ) {
		$this->game = $game;
		$this->variantID =(int)$game->variant->id;
		$this->user = $user;
		$this->member = $member;
		$this->turn = $turn;
		$this->phase = $phase;
		$this->country = $country;
		$this->orderStatus=$orderStatus;
		$this->tokenExpireTime=$tokenExpireTime;
		$this->maxOrderID=$maxOrderID;
	}

	protected $Orders;

	public function load()
	{
		global $app;
		$DB = $app->make('DB');

		$DB->sql_put("SELECT * FROM wD_Members WHERE gameID = ".$this->game->id." AND countryID=".$this->country->id." ".UPDATE);

		$tabl = $DB->sql_tabl("SELECT id, type, unitID, toTerrID, fromTerrID, viaConvoy
			FROM wD_Orders WHERE gameID = ".$this->game->id." AND countryID=".$this->country->id);

		$this->Orders = [];
		$maxOrderID=0;
		while ( $row = $DB->tabl_hash($tabl) )
		{
			if( $row['id'] > $maxOrderID ) $maxOrderID = $row['id'];

			$Order = userOrder::load($this->phase->name,$row['id'],$this->game->id, $this->country->id);

			$Order->loadFromDB($row);

			$this->Orders[] = $Order;
		}

		list($checkTurn, $checkPhase) = $DB->sql_row("SELECT turn, phase FROM wD_Games WHERE id=".$this->game->id);
		if( $checkTurn != $this->turn->id || $checkPhase != $this->phase->name )
			throw new Exception(l_t("The game has moved on, you can no longer alter these orders, please refresh."));

		if( $this->maxOrderID == false ) $this->maxOrderID = $maxOrderID;
	}

	public function set($orderUpdates)
	{
		if( $this->orderStatus->Ready ) return;

		$this->log($orderUpdates);

		$orderUpdates = json_decode($orderUpdates);

		foreach($orderUpdates as $orderUpdate)
		{
			$orderUpdate = (array)$orderUpdate;
			foreach($this->Orders as $Order)
				if( $Order->id == $orderUpdate['id'] )
					$Order->loadFromInput($orderUpdate);
		}
	}

	protected function log($logData)
	{
		$orderlogDirectory = Config::orderlogDirectory();
		if ( false === $orderlogDirectory ) return;

		require_once(l_r('objects/game.php'));
		$directory = libCache::dirID($orderlogDirectory, $this->game->id, true);

		$file = $this->country->id.'.txt';

		if ( ! ($orderLog = fopen($directory.'/'.$file, 'a')) )
			trigger_error(l_t("Couldn't open order log file."));

		if( !fwrite($orderLog, 'Time: '.gmdate("M d Y H:i:s")." (UTC+0)\n".$logData."\n\n") )
			trigger_error(l_t("Couldn't write to order log file."));

		fflush($orderLog) or trigger_error(l_t("Couldn't write to order log file."));
		fclose($orderLog);
	}

	protected $results = [
	    'orders' => [],
        'notice' => '',
        'statusIcon' => '',
        'statusText' => '',
        'invalid' => false
    ];
	public function validate() {

		if( count($this->Orders)==0 )
			$this->orderStatus->None=true;

		$complete=true;
		foreach($this->Orders as $Order)
		{
			$Order->validate();

			$result = $Order->results();
			if( $complete && $result['status'] != 'Complete' )
				$complete=false;

			if( $result['status'] == 'Invalid' )
			{
				$complete=false;
				$this->results['invalid'] = true;
			}

			$this->results['orders'][$Order->id] = $result;
		}
		$this->orderStatus->Completed = $complete;

		return $this->results;
	}

	public function readyToggle() {
		if( !$this->orderStatus->Ready )
		{
			if( !$this->orderStatus->Completed )
				$this->results['notice'] .= l_t(' Could not set to ready, orders not complete and valid.');
			else
				$this->orderStatus->Ready=true;
		}
		else
			$this->orderStatus->Ready = false;

		return $this->orderStatus->Ready;
	}

	public function writeOrders() {
		$updated=false;
		foreach($this->Orders as $Order)
			if( $Order->commit() )
				$updated=true;

		if( $updated )
			$this->orderStatus->Saved=true;
	}

	public function writeOrderStatus() {
		global $Member;
		global $app;
		$DB = $app->make('DB');

		$this->results['statusIcon']=$this->orderStatus->icon();
		$this->results['statusText']=$this->orderStatus->iconText();

		if( $this->orderStatus->updated )
		{
			if( isset($Member) && $Member instanceof Member && $Member->id == $this->member->id )
				$Member->orderStatus = $this->orderStatus;

			$DB->sql_put("UPDATE wD_Members SET orderStatus = '".$this->orderStatus."' WHERE id = ".$this->member->id);

			$newContext = $this->getContext($this);
			$this->results['newContext'] = $newContext['context'];
			$this->results['newContextKey'] = $newContext['key'];
		}
	}

	public function getResults() {
		return $this->results;
	}

	protected static $contextVars = ['gameID','userID','memberID','variantID','turn','phase','countryID','tokenExpireTime','maxOrderID'];

    /**
     * @param $contextOf
     * @return array
     */
	public static function getContext($contextOf) {
        if (is_array($contextOf)) {
            $context = [];
            foreach($contextOf as $name=>$val)
            {
                if(!in_array($name, self::$contextVars)) continue;
                $context[$name] = $val;
            }
            $context['orderStatus'] = ''.$contextOf['orderStatus'];
        } else {
            $context = [
                'gameID' => $contextOf->game->id,
                'variantID' => $contextOf->variantID,
                'userID' => $contextOf->user->id,
                'memberID' => $contextOf->member->id,
                'turn' => $contextOf->turn->id,
                'phase' => $contextOf->phase->name,
                'tokenExpireTime' => $contextOf->tokenExpireTime,
                'maxOrderID' => $contextOf->maxOrderID,
                'orderStatus' => $contextOf->orderStatus,
            ];
        }
        $json = json_encode($context);

		return [
		    'context' => $context,
            'json' => $json,
            'key' => md5(Config::$jsonSecret.$json).sha1(Config::$jsonSecret.$json),
        ];
	}

	protected function jsContextVars() {
		$context = self::getContext($this);
		libHTML::$footerScript[] = '
	context='.$context['json'].';
	contextKey="'.$context['key'].'";
	ordersData = '.json_encode($this->Orders).';
	';
	}

	protected function jsLoadBoard() {
		libHTML::$footerIncludes[] = l_j('board/model.js');
		libHTML::$footerIncludes[] = l_j('board/load.js');
		libHTML::$footerIncludes[] = l_j('orders/order.js');
		libHTML::$footerIncludes[] = l_j('orders/phase'.$this->phase->name.'.js');
		libHTML::$footerIncludes[] = l_s('../'.libVariant::$Variant->territoriesJSONFile());

		foreach(array('loadTerritories','loadBoardTurnData','loadModel','loadBoard','loadOrdersModel','loadOrdersForm','loadOrdersPhase') as $jf)
			libHTML::$footerScript[] = l_jf($jf).'();';
	}

	protected function jsInitForm() {
		libHTML::$footerIncludes[] = l_j('orders/form.js');
		libHTML::$footerScript[] = l_jf('OrdersHTML.formInit').'(context, contextKey);';
	}

	protected function jsLiveBoardData() {
	    $turnId = $this->phase->isMoves() ? $this->turn->id - 1 : $this->turn->id;
		$jsonBoardDataFile=Game::mapFilename($this->game->id, $turnId, 'json');

		if( !file_exists($jsonBoardDataFile) )
			$jsonBoardDataFile='map.php?gameID='.$this->game->id.'&turn='.$this->turn->id.'&phase='.$this->phase->name.'&mapType=json'.(defined('DATC')?'&DATC=1':'').'&nocache='.rand(0,1000);
		else
			$jsonBoardDataFile.='?phase='.$this->phase->name.'&nocache='.rand(0,10000);

		return '<script type="text/javascript" src="'.STATICSRV.$jsonBoardDataFile.'"></script>';
	}

	public function jsHTML() {

		$this->jsContextVars();
		$this->jsLoadBoard();
		$this->jsInitForm();
		return $this->jsLiveBoardData();
	}

	public function html()
	{
		global $Game;
		$html = $this->jsHTML();

		$html .= '
	<form id="orderFormElement" onsubmit="return false;">
		<a name="orders"></a><div class = "chatWrapper"><table class="orders">';

		$alternate = false;
		foreach($this->Orders as $Order)
		{
			$alternate = ! $alternate;
			$html .= '<tr class="barAlt'.($alternate ? '1' : '2').'">
				<td class="uniticon"><span id="orderID'.$Order->id.'UnitIconArea"></span></td>
				<td class="order"><div id="orderID'.$Order->id.'">'.l_t('Loading order').'...</div></td>
				</tr>';
		}

		$html .= "</table></div>".'
				<div style="text-align:center;"><span id="ordersNoticeArea'.$this->member->id.'"></span>
				'.
				($Game->pressType == 'RulebookPress' && !$this->phase->isMoves() ? ''  :
			'<input id="UpdateButton'.$this->member->id.'" type="Submit" class="form-submit spaced-button" name="'.
				l_t('Update').'" value="'.l_t('Save').'" disabled />' ) .'
			<input id="FinalizeButton'.$this->member->id.'" type="Submit" class="form-submit spaced-button" name="'.
				l_t($this->orderStatus->Ready?'Not ready':'Ready').'" value="'.l_t($this->orderStatus->Ready?'Not ready':'Ready').'" disabled />
		</div>
	</form>';

		return $html;
	}
}
