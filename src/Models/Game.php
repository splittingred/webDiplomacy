<?php

namespace Diplomacy\Models;

use Diplomacy\Services\Variants\VariantsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use panelGame;
use panelGameHome;
use WDVariant;

/**
 * @property int id
 * @property string name
 * @property int variantID
 * @property string turn
 * @property string phase
 * @property string processTime
 * @property int pot
 * @property string gameOver
 * @property string processStatus
 * @property string password
 * @property string potType
 * @property int pauseTimeRemaining
 * @property int minimumBet
 * @property int phaseMinutes
 * @property int nextPhaseMinutes
 * @property int phaseSwitchPeriod
 * @property string anon
 * @property string pressType
 * @property int attempts
 * @property string missingPlayerPolicy
 * @property int directorUserID
 * @property string drawType
 * @property int minimumReliabilityRating
 * @property int excusedMissedTurns
 * @property int finishTime
 * @property string playerTypes
 * @property int startTime
 */
class Game extends EloquentBase
{
    protected $table = 'wD_Games';
    protected $hidden = [
        'password',
    ];
    /** @var WDVariant */
    protected $variant;
    /** @var panelGameHome */
    protected $homeGamePanel;
    /** @var panelGame */
    protected $gamePanel;
    /** @var \ScoringSystem $scoringSystem */
    protected $scoringSystem;

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return HasMany
     */
    public function members() : HasMany
    {
        return $this->hasMany(Member::class, 'gameID', 'id');
    }

    /**
     * @return HasMany
     */
    public function units() : HasMany
    {
        return $this->hasMany(Unit::class, 'gameID');
    }

    /**
     * @return HasMany
     */
    public function turnDates() : HasMany
    {
        return $this->hasMany(TurnDate::class, 'gameID');
    }

    /**
     * @return BelongsTo
     */
    public function director() : BelongsTo
    {
        return $this->belongsTo(User::class, 'directorUserID');
    }

    /**
     * @return \ScoringSystem
     */
    public function getScoringSystem() : \ScoringSystem
    {
        if (!empty($this->scoringSystem)) return $this->scoringSystem;

        switch ($this->potType) {
            case 'Points-per-supply-center':
                $this->scoringSystem = new \ScoringPPSC($this);
                break;
            case 'Winner-takes-all':
                $this->scoringSystem = new \ScoringWTA($this);
                break;
            case 'Unranked':
                $this->scoringSystem = new \ScoringUnranked($this);
                break;
            case 'Sum-of-squares':
                $this->scoringSystem = new \ScoringSoS($this);
                break;
            default:
                trigger_error("Unknown pot type '".$this->potType."'");
                break;
        }
        return $this->scoringSystem;
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function getTournament()
    {
        $tournamentTable = Tournament::getTableName();
        $tournamentGameTable = TournamentGame::getTableName();
        return Tournament::query()
            ->join($tournamentGameTable, $tournamentGameTable.'.tournamentID', '=', $tournamentTable.'.id')
            ->where($tournamentGameTable .'.gameID', '=', $this->id)->first();
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeJoinMembers(Builder $query) : Builder
    {
        $membersTable = Member::getTableName();
        return $query->join($membersTable, $membersTable . '.gameID', '=', static::getTableName() . '.id');
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeWithUser(Builder $query, int $userId) : Builder
    {
        $membersTable = Member::getTableName();
        return $this->scopeJoinMembers($query)->where($membersTable . '.userID', '=', $userId);
    }

    /**
     * Filter by Tournament, and optionally, Tournament Round
     *
     * @param Builder $query
     * @param int $tournamentId
     * @return Builder
     */
    public function scopeForTournament(Builder $query, int $tournamentId, int $rountId = 0) : Builder
    {
        $gameTable = static::getTableName();
        $tgTable = TournamentGame::getTableName();
        $query = $query->join($tgTable, $tgTable . '.gameID', '=', static::raw($gameTable . '.id'))
                       ->where($tgTable . '.tournamentID', '=', $tournamentId);
        if (!empty($roundId)) {
            $query->where($tgTable.'.round', '=', static::raw($roundId));
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeWithoutUser(Builder $query, int $userId) : Builder
    {
        $membersTable = Member::getTableName();
        $gamesTable = static::getTableName();
        return $query
            ->leftJoin($membersTable , function($join) use ($gamesTable, $membersTable, $userId) {
                $join->where($membersTable . '.gameID', '=', Game::raw($gamesTable . '.id'))
                    ->where($membersTable . '.userID', '=', Game::raw($userId));
            })
            ->whereNull($membersTable . '.id');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeAnonymous(Builder $query) : Builder
    {
        return $query->where('anon', '=', 'Yes');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotAnonymous(Builder $query) : Builder
    {
        return $query->where('anon', '=', 'No');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinished(Builder $query) : Builder
    {
        return $query->where('phase', '=', 'Finished');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotFinished(Builder $query) : Builder
    {
        return $query->where('phase', '!=', 'Finished');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGameOver(Builder $query) : Builder
    {
        return $query->where('gameOver', 'Yes');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGameNotOver(Builder $query) : Builder
    {
        return $query->where('gameOver', 'No');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWon(Builder $query) : Builder
    {
        return $query->where('gameOver', '=', 'Won');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeDrawn(Builder $query) : Builder
    {
        return $query->where('gameOver', '=', 'Drawn');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePreGame(Builder $query) : Builder
    {
        return $query->where('phase', '=' , 'Pre-Game');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotPreGame(Builder $query) : Builder
    {
        return $query->where('phase', '!=' , 'Pre-Game');
    }

    /**
     * @param Builder $query
     * @param int $turns
     * @return Builder
     */
    public function scopeWithExcusedMissedTurnsOf(Builder $query, int $turns) : Builder
    {
        return $query->where(static::getTableName() . '.excusedMissedTurns', '=', $turns);
    }

    /**
     * Get all joinable games for a user, based on passed available points and RR
     *
     * @param Builder $query
     * @param int $userId
     * @param int $points
     * @param int $reliabilityRating
     * @return Builder
     */
    public function scopeJoinableForUser(Builder $query, int $userId, int $points, int $reliabilityRating) : Builder
    {
        return $this->scopeWithoutUser($query, $userId)
            ->whereNotNull('minimumBet')
            ->whereNull('password')
            ->gameNotOver()
            ->active()
            ->where('minimumBet', '<=', $points)
            ->where('minimumReliabilityRating', '<=', $reliabilityRating);
    }

    /**
     * Return all active games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->notPreGame()->notFinished();
    }

    /**
     * Return all new (not game over and in Pre-Game) games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNew(Builder $query) : Builder
    {
        return $query->preGame()->notGameOver();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePublic(Builder $query) : Builder
    {
        return $query->whereNull('password');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePrivate(Builder $query) : Builder
    {
        return $query->whereNotNull('password');
    }

    /**
     * Get all active games for a given user
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeActiveForUser(Builder $query, int $userId) : Builder
    {
        return $query->withUser($userId)->notFinished();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePaused(Builder $query) : Builder
    {
        return $query->notPreGame()->notFinished()->where('processStatus', '=', 'Paused');
    }

    /**
     * Filter to only games that are not paused in processing
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRunning(Builder $query) : Builder
    {
        return $query->notPreGame()->notFinished()->where('processStatus', '!=', 'Paused');
    }

    /**
     * Filter games by variant ID
     *
     * @param Builder $query
     * @param string|int $variant Either the ID or name of the variant
     * @return Builder
     */
    public function scopeForVariant(Builder $query, $variant) : Builder
    {
        $variantId = intval($variant) > 0 ? $variant : VariantsService::variantIdFromName($variant);
        return !empty($variant) ? $query->where('variantID', '=', $variantId) : $query;
    }

    /**
     * Filter to games with humans only
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyHumans(Builder $query): Builder
    {
        return $query->where('playerTypes', '=', 'Members');
    }

    /**
     * Filter to only Gunboat games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGunboat(Builder $query): Builder
    {
        return $query->where('pressType', '=', 'NoPress');
    }

    /**
     * Filter to only regular or rulebook Press games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePress(Builder $query): Builder
    {
        return $query->where('pressType', '=', ['NoPress', 'Regular']);
    }

    /**
     * Filter to only ranked games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRanked(Builder $query): Builder
    {
        return $query->where('potType', '!=', 'Unranked');
    }

    /**
     * Filter to only Classic games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeClassic(Builder $query): Builder
    {
        return $query->where('variantID', '=', 1);
    }

    /**
     * Filter to only variants
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNonClassic(Builder $query): Builder
    {
        return $query->where('variantID', '!=', 1);
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

    /**
     * @param int $gameId
     * @param int $userId
     * @return bool
     */
    public static function isMember(int $gameId, int $userId) : bool
    {
        return Member::forGame($gameId)->forUser($userId)->exists();
    }

    /**
     * @return WDVariant
     */
    public function getVariant() : WDVariant
    {
        if (!$this->variant) $this->variant = \libVariant::loadFromVariantID($this->variantID);
        return $this->variant;
    }

    /**
     * @return panelGameHome
     */
    public function getHomeGamePanel() : panelGameHome
    {
        if (!$this->homeGamePanel) $this->homeGamePanel = $this->getVariant()->panelGameHome($this->toArray());
        return $this->homeGamePanel;
    }

    /**
     * Get the home panel summary
     *
     * @return string
     */
    public function getHomeSummary() : string
    {
        return $this->getHomeGamePanel()->summary();
    }

    /**
     * Get the main game panel
     *
     * @return panelGame
     */
    public function getGamePanel() : panelGame
    {
        if (!$this->gamePanel) $this->gamePanel = $this->getVariant()->panelGame($this->toArray());
        return $this->gamePanel;
    }

    /**
     * Get the main summary
     *
     * @return string
     */
    public function getSummary() : string
    {
        return $this->getGamePanel()->summary();
    }

    /**
     * @return bool
     */
    public function isPaused() : bool
    {
        return $this->processStatus == 'Paused';
    }
    /**
     * @return bool
     */
    public function isPreGame() : bool
    {
        return $this->phase == 'Pre-game';
    }

    /**
     * Is the game crashed?
     *
     * @return bool
     */
    public function isCrashed() : bool
    {
        return $this->processStatus == 'Crashed';
    }


    /**
     * @return bool
     */
    public function isProcessing() : bool
    {
        return $this->processStatus == 'Processing';
    }

    /**
     * @return bool
     */
    public function isInNotProcessing() : bool
    {
        return $this->processStatus == 'Not-processing';
    }

    /**
     * @return bool
     */
    public function isStarted() : bool
    {
        return !$this->isPreGame();
    }

    /**
     * @return bool
     */
    public function isInProgress() : bool
    {
        return $this->isStarted() && !$this->isFinished();
    }

    /**
     * @return bool
     */
    public function isFinished() : bool
    {
        return $this->phase == 'Finished';
    }

    /**
     * @return int
     */
    public function getPauseTimeRemaining() : int
    {
        return is_null($this->pauseTimeRemaining) ? $this->phaseMinutes * 60 : (int)$this->pauseTimeRemaining;
    }

    /**
     * @return int
     */
    public function getNextPhaseSeconds() : int
    {
        return (int)$this->nextPhaseMinutes * 60;
    }

    /**
     * @return bool
     */
    public function isSwitchingPhasePeriod() : bool
    {
        return !$this->isFinished() && $this->phaseSwitchPeriod > 0 && $this->nextPhaseMinutes != $this->phaseMinutes;
    }

    /**
     * @return string
     */
    public function getPhaseSwitchAsText() : string
    {
        if (!$this->isSwitchingPhasePeriod()) return '';

        $deadline = $this->getPhaseSwitchPeriod();

        $str = '<div>Changing phase length: <span><strong>'.\libTime::timeLengthText($this->getNextPhaseSeconds()).'</strong> /phase</span></div>';
        if ($this->startTime > 0)
        {
            $timeWhenSwitch = $this->getPhaseSwitchDeadline();
            if (time() >= $timeWhenSwitch) {
                $str .= '<div><strong> At: End Of Phase</strong></div>';
            } else {
                $str .= '<div> In: <strong>'.\libTime::remainingText($timeWhenSwitch).'</strong>' . ' (' . \libTime::detailedText($timeWhenSwitch) . ')</div>';
            }
        } else {
            $timeTillNextPhase = \libTime::timeLengthText($deadline);
            $str .= '<div><span><strong>'.$timeTillNextPhase.'</strong> after game start</span></div></br>';
        }
        return $str;
    }

    /**
     * @return int|mixed
     */
    public function getPhaseSwitchDeadline()
    {
        return $this->getPhaseSwitchPeriod() + $this->startTime;
    }

    /**
     * Get phase switch period in seconds
     *
     * @return int
     */
    public function getPhaseSwitchPeriod() : int
    {
        return (int)$this->phaseSwitchPeriod * 60;
    }

    /**
     * DEPRECATED STUFF
     */

    /** @var \Misc */
    private $misc;
    private function getMisc() : \Misc
    {
        if (empty($this->misc)) {
            global $app;
            $this->misc = $app->make('Misc');
        }
        return $this->misc;
    }

    /** @var \panelGameBoard $oldGame */
    protected $oldGame;
    public function getOldGame(): \panelGameBoard
    {
        $this->oldGame = $this->getVariant()->panelGameBoard($this->id);
        return $this->oldGame;
    }

    /** @var \Members $members */
    protected $members;
    public function getMembers()
    {
        $game = $this->getOldGame();
        $this->members = $this->getVariant()->Members($game);
        return $this->members;
    }
}