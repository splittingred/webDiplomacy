<?php

namespace Diplomacy\Services\Games\Creation;

use Diplomacy\Models\Game;
use Diplomacy\Models\Member;
use Diplomacy\Models\User;
use Diplomacy\Services\Games\Factory as GameFactory;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Log\Logger;

class Command
{
    /** @var Request $request */
    protected $request;
    protected $logger;
    /** @var GameFactory $gameFactory */
    protected $gameFactory;

    public function __construct(Request $request, Logger $logger)
    {
        $this->request = $request;
        $this->logger = $logger;
        $this->gameFactory = new GameFactory();
    }

    /**
     * @return Result
     */
    public function call(): Result
    {
        if ($this->request->name == 'DATC-Adjudicator-Test' && !defined('DATC'))
        {
            return Failure::withError('invalid_game_name', "The game name 'DATC-Adjudicator-Test' is reserved for the automated DATC tester.");
        }

        $currentUserEntity = $this->request->currentUser;
        $currentUser = User::find($currentUserEntity->id);

        $this->request->name = $this->findUniqueName($this->request->name);

        // DB transactions do not work in webdip currently :/
        //Capsule::beginTransaction();

        try {
            $pTime = time() + $this->request->joinPeriod * 60;
            $pTime = $pTime - fmod($pTime, 300) + 300;    // for short game & phase timer

            $game = new Game();
            $game->variantID = $this->request->variantId;
            $game->turn = 0;
            $game->phase = 'Pre-game'; // All games start in pre-game phase until everyone is assigned
            $game->processTime = $pTime;
            $game->pot = 0;
            $game->name = $this->request->name;
            $game->gameOver = 'No';
            $game->processStatus = 'Not-processing';
            if (!empty($this->request->password)) {
                $game->password = hex2bin(md5($this->request->password));
            }
            $game->potType = $this->request->potTypeId;
            $game->minimumBet = $this->request->bet;
            $game->phaseMinutes = $this->request->phaseMinutes;
            $game->nextPhaseMinutes = $this->request->nextPhaseMinutes;
            $game->phaseSwitchPeriod = $this->request->phaseSwitchPeriod;
            $game->anon = $this->request->anon ? 'Yes' : 'No';
            $game->pressType = $this->request->pressTypeId;
            $game->attempts = 0;
            $game->missingPlayerPolicy = $this->request->missingPlayerPolicy;
            $game->directorUserID = 0;
            $game->drawType = $this->request->drawTypeId;
            $game->minimumReliabilityRating = $this->request->minRr;
            $game->excusedMissedTurns = $this->request->excusedMissedTurns;
            $game->playerTypes = $this->request->playersType;
            $game->startTime = 0;
            if (!$game->save()) {
                throw new \Exception('Failed to save new game.', 'game_not_saved');
            }

            $gameEntity = $this->gameFactory->build($game);

            // For now this needs to be done here until a more proper variant plugin architecture is built
            /** @var \WDVariant $Variant */
            $gameEntity->variant->processGame($gameEntity->id);

            $member = new Member();
            $member->userID = $currentUser->id;
            $member->gameID = $gameEntity->id;
            $member->countryID = 0;
            $member->orderStatus = 'None,Completed,Ready';
            $member->bet = 0;
            $member->timeLoggedIn = time();
            $member->excusedMissedTurns = $gameEntity->excusedMissedTurns;
            if (!$member->save()) {
                $game->forceDelete();
                throw new \Exception('Failed to save initial member for game '.$game->id, 'member_not_saved');
            }

            if ($currentUserEntity->isBot()) {
                \User::pointsTransfer($currentUser->id, 'Bet', $this->request->bet, $game->id, $member->id);
                $game->pot += 5;
            } else {
                \User::pointsTransfer($currentUser->id, 'Bet', $this->request->bet, $game->id, $member->id);
                $game->pot += $this->request->bet;
            }
            global $DB;
            $DB->commit(); // needed until above is refactored

            if (!$game->save()) {
                $member->forceDelete();
                $game->forceDelete();
                throw new \Exception('Failed to update pot for new game '.$game->id.' to '.$game->pot, 'pot_not_saved');
            }
        } catch (\Exception $e) {
            $this->logger->error("FAILED TO SAVE NEW GAME: {$e->getMessage()}");
            //Capsule::rollBack();
            return Failure::withError($e->getCode(), $e->getMessage());
        }

        //Capsule::commit();
        return new Success($game);
    }

    /**
     * Ensure every game has a unique name
     *
     * @param string $fromName
     * @param int $maxTries
     * @return string
     */
    protected function findUniqueName(string $fromName, int $maxTries = 100)
    {
        $name = substr($fromName,0,50);
        $newName = $name;
        $i = 1;
        while ($i < $maxTries)
        {
            $newName = $name . ($i > 1 ? '-' . $i : '');
            $count = Game::where('name', '=', $newName)->count();
            if ( $count == 0 )
            {
                break;
            }
            else
            {
                $i++;
            }
        }
        return $newName;
    }
}