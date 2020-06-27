<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Games\BackupService;
use Diplomacy\Services\Games\GamesService;
use Diplomacy\Services\Request;

class RestoreFromBackupForm extends BaseForm
{
    public string $id = 'admin-game-restore-from-backup';
    protected string $name = 'admin-game-restore-from-backup';
    protected string $template = 'forms/admin/games/restore_from_backup.twig';
    protected string $requestType = Request::TYPE_POST;

    protected GamesService $gamesService;
    protected BackupService $backupService;

    public function setUp(): void
    {
        $this->gamesService = new GamesService();
        $this->backupService = new BackupService();
    }

    /**
     * @return array
     */
    public function getFieldDefinitions(): array
    {
        $game = $this->getGame();
        if ($game) {
            return [
                'game_id' => [
                    'type' => 'Games\Game',
                    'game' => $game,
                    'input' => [
                        'disabled' => 'disabled',
                    ],
                    'default' => 0,
                ],
            ];
        } else {
            return [
                'game_id' => [
                    'type' => 'text',
                    'label' => 'Game ID',
                    'default' => '',
                ],
            ];
        }
    }

    /**
     * @return $this
     */
    public function handleSubmit(): BaseForm
    {
        $gameId = $this->getValue('game_id');
        try {
            $game = $this->gamesService->find($gameId);
        } catch (\Exception $e) {
            $this->setNotice("Game not found with ID: $gameId");
            return $this;
        }

        $result = $this->backupService->create($game);
        if ($result->successful()) {
            $this->redirectToSelf();
        } else {
            $this->setNotice('Failed to backup game: '.$result->getValue()->getMessage());
        }
        return $this;
    }
}

