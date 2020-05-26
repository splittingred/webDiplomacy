<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;
use Diplomacy\Services\Contact\Issue;
use Diplomacy\Services\Contact\IssueFactory;
use Diplomacy\Services\Contact\SubmissionService;
use Diplomacy\Services\Contact\UserNotQualifiedForEmergencyException;
use Diplomacy\Services\Request;

class ContactDirectController extends BaseController
{
    public $template = 'pages/help/contact_direct.twig';
    public $pageTitle = 'Contact Us';
    public $pageDescription = 'Directly submit a support request to the moderator team.';

    /** @var IssueFactory */
    protected $issueFactory;
    /** @var SubmissionService */
    protected $submissionService;

    public function setUp()
    {
        $this->issueFactory = new IssueFactory();
        $this->submissionService = new SubmissionService();
        parent::setUp();
    }

    public function call()
    {
        $this->handleSubmit();
        return [
            'games' => $this->getGames(),
        ];
    }

    protected function getGames()
    {
        $query = Game::joinMembers()
            ->gameNotOver()
            ->notPreGame()
            ->where('wD_Members.status', '=', 'Playing')
            ->where('wD_Members.userID', '=', $this->currentUser->id);
        return $query->get();
    }

    protected function handleSubmit()
    {
        if ($this->request->isEmpty('submit', Request::TYPE_POST)) return;

        try {
            $issue = $this->issueFactory->build($this->request, $this->currentUser);
            $this->submissionService->handle($this->currentUser, $issue);
            $this->setPlaceholder('notice', 'Moderators will get back to you shortly about your issue. Thanks!');
        } catch (UserNotQualifiedForEmergencyException $e) {
            $this->setPlaceholder('notice', 'You do not qualify for an emergency request. Only users that have finished at least 10 games and not used an emergency request in the last 6 months qualify.');
        } catch (\Exception $e) {
            $this->setPlaceholder('notice', 'Sorry, but there was a problem sending this message, if this is an emergency contact the moderator team directly at '.\Config::$modEMail);
        }
    }
}