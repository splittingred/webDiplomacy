<?php

namespace Diplomacy\Services\Contact;

use Diplomacy\Models\Game;
use Diplomacy\Services\Request;
use User;

class IssueFactory
{
    /**
     * @param Request $request
     * @return EmergencyIssue|GameIssue|OtherIssue
     * @throws UserNotQualifiedForEmergencyException
     */
    public function build(Request $request, User $user)
    {
        $issueType = $request->get('issueType', 'gameIssue', Request::TYPE_POST);
        $additionalInfo = $request->get('additionalInfo', '', Request::TYPE_POST);
        $additionalInfo = $this->sanitize($additionalInfo);

        switch ($issueType) {
            case Issue::TYPE_GAME:
                $games = $this->getGames($request, $user);
                $typeCode = $request->get('gamesIssue', 'other', Request::TYPE_POST);
                $issue = new GameIssue($typeCode, $additionalInfo, $games);
                break;
            case Issue::TYPE_EMERGENCY:
                if (!$user->qualifiesForEmergency()) {
                    throw new UserNotQualifiedForEmergencyException();
                }
                $games = $this->getGames($request, $user);
                $typeCode = $request->get('emergencyIssue', 'medical', Request::TYPE_POST);
                $issue = new EmergencyIssue($typeCode, $additionalInfo, $games);
                break;
            case Issue::TYPE_OTHER:
            default:
                $typeCode = $request->get('otherIssue', 'other', Request::TYPE_POST);
                $issue = new OtherIssue($typeCode, $additionalInfo, []);
                break;
        }
        return $issue;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function sanitize($str)
    {
        $str = filter_var($str, FILTER_SANITIZE_STRING);
        return strip_tags(html_entity_decode(trim($str)));
    }

    protected function getGames(Request $request, User $user)
    {
        $games = $request->get('games', '', Request::TYPE_POST);
        if ($games == '0') return [0];
        if ($games == '1') return [1];

        $games = explode(',', $games);
        $games = array_map(function($s) { return intval($s); }, $games);

        return Game::joinMembers()->where('wD_Members.userID','=', $user->id)->whereIn('wD_Games.id', $games)->get();
    }
}