<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Message;
use Diplomacy\Models\Entities\Games\UnassignedMember;
use Diplomacy\Services\Games\MembersService;
use Diplomacy\Services\Games\MessagesService;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;
use Diplomacy\Services\Request;
use Diplomacy\Views\Components\BaseComponent;
use Diplomacy\Views\Components\Games\Members\BarComponent as MemberBarComponent;

/**
 * The chat-box for the board. From the tabs to the messages to the send-box, also
 * takes responsibility for sending any messages it receives.
 *
 * @package \Diplomacy\Views\Components\Games\ChatBox
 */
class ChatBoxComponent extends BaseComponent
{
    protected string $template = 'games/chatbox/chatbox.twig';

    protected Game $game;
    protected ?Member $currentMember;
    protected int $targetCountryId;
    protected ?Member $targetMember;
    protected bool $currentMemberIsTargetCountry;
    protected bool $isGlobal;
    protected bool $isAll;
    protected bool $isAuthenticated;
    protected MessagesService $messagesService;
    protected MembersService $membersService;

    /**
     * @param Game $game
     * @param Member $currentMember
     * @param int $targetCountryId
     */
    public function __construct(Game $game, Member $currentMember, int $targetCountryId = 0)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->targetCountryId = $this->findTargetCountry($targetCountryId);
        $this->isAuthenticated = $this->currentMember->isAuthenticated();
        $this->targetMember = $this->game->members->byCountryId($this->targetCountryId);
        $this->currentMemberIsTargetCountry = $this->isAuthenticated && $this->currentMember->isCountry($this->targetCountryId);
        $this->isGlobal = $this->targetCountryId == Country::GLOBAL;
        $this->isAll = $this->targetCountryId == Country::ALL;
        $this->messagesService = new MessagesService();
        $this->membersService = new MembersService();
    }

    /**
     * Do all the pre-render actions
     */
    public function beforeRender(): void
    {
        $this->markMessagesAsRead();
        $this->handleNewMessage();
        $this->handleMarkUnread();
    }

    /**
     * Find the tab which the user has requested to see, and return the country name. (Can also be 'Global')
     *
     * Should set the countryID as a session var, so will remember the countryID selected once even if not specified afterwards.
     *
     * @param int $targetCountryId
     * @return string
     */
    public function findTargetCountry(int $targetCountryId = 0)
    {
        // Ensure in proper range
        $this->targetCountryId = $targetCountryId < -1 && $targetCountryId < $this->game->getCountryCount() ? $targetCountryId : Country::GLOBAL;

        // Enforce Global and Notes tabs when its not Regular or Rulebook press game, and not looking at
        // own messages
        $isMemberBanned = $this->currentMember->isBanned();
        if ((!$this->game->pressType->allowPrivateMessages() || $isMemberBanned) && !$this->currentMemberIsTargetCountry) {
            $this->targetCountryId = Country::GLOBAL;
        }
        return $this->targetCountryId;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'tabs' => $this->isAuthenticated ? $this->getTabs() : '',
            'game' => $this->game,
            'isGlobal' => $this->isGlobal,
            'isAll' => $this->isAll,
            'memberNames' => $this->getMemberNames(),
            'messages' => $this->getMessages(),
            'form' => $this->isAuthenticated ? $this->getForm() : '',
        ];
    }

    /**
     * Get the tabs HTML
     *
     * @return string
     */
    protected function getTabs(): string
    {
        return (string)(new TabsComponent($this->game, $this->currentMember, $this->targetCountryId));
    }

    /**
     * Get the form HTML
     *
     * @return string
     */
    protected function getForm(): string
    {
        return (string)(new FormComponent($this->game, $this->targetMember, $this->targetCountryId));
    }

    /**
     * Get a list of member names, properly rendered to respect anonymous settings
     *
     * @return string
     */
    protected function getMemberNames(): string
    {
        return (string)(new MemberNamesComponent($this->game, $this->currentMember, $this->targetCountryId));
    }

    /**
     * Get the messages HTML
     *
     * @return string
     */
    protected function getMessages(): string
    {
        $targetCountryId = !$this->isAuthenticated ? -1 : $this->targetCountryId;
        $currentMemberCountryId = !$this->isAuthenticated ? -1 : $this->currentMember->country->id;

        $messages = $this->messagesService->forChatBox($this->game, $targetCountryId, $currentMemberCountryId);

        $output = [];
        $alternate = false;
        /** @var Message $message */
        foreach ($messages as $message)
        {
            $showAuthors = $this->targetCountryId == Country::ALL && $this->isAuthenticated;
            $output[] = (string)(new MessageComponent($message, $this->game, $this->currentMember, $showAuthors, $alternate));
            $alternate = !$alternate;
        }
        return join("\n", $output);
    }

    /**
     * Handle marking the new messages as read
     *
     * @return Result
     */
    public function markMessagesAsRead()
    {
        if (!in_array($this->targetCountryId, $this->currentMember->newMessagesFrom)) return new Success();

        return $this->messagesService->markCountryMessageSeen($this->currentMember, $this->targetCountryId);
    }

    /**
     * Handle posting a new message
     *
     * @return Result
     */
    protected function handleNewMessage(): Result
    {
        // Handle new message submission
        $messageBody = trim($this->getRequest()->get('newMessage', '', Request::TYPE_POST));
        if (empty($messageBody)) return new Success();

        try {
            return $this->messagesService->sendToCountry($this->game, $this->currentMember, $this->targetCountryId, $messageBody);
        } catch (\Exception $e) {
            return Failure::withError('internal', $e->getMessage());
        }
    }

    /**
     * Handle marking messages as unread for a Country
     *
     * @return Result
     */
    protected function handleMarkUnread(): Result
    {
        if ($this->getRequest()->isEmpty('MarkAsUnread', Request::TYPE_POST)) return (new Success());

        return $this->messagesService->markCountryMessageUnseen($this->currentMember, $this->targetCountryId);
    }
}