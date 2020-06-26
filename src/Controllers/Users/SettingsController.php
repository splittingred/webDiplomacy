<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController;

require_once 'objects/mailer.php';

class SettingsController extends BaseController
{
    protected string $template = 'pages/users/settings.twig';
    protected string $pageTitle = 'User account settings';
    protected string $pageDescription = 'Control settings for your account.';

    protected \Mailer $mailer;
    protected array $noticeMappings = [
        'updated' => '{{ user.username }} successfully updated.',
        'emailUpdated' => 'A validation e-mail was sent to the new address, containing a link which will confirm the e-mail change. If you don\'t see it after a few minutes check your spam folder.',
        'passwordUpdated' => 'Password updated successfully. You have been logged out and will need to login with the new password.'
    ];

    public function setUp(): void
    {
        global $Mailer;
        $this->mailer = new \Mailer();
    }

    public function call(): array
    {
        $variables = [
            'user' => $this->currentUser,
            'user_options' => $this->getUserOptions(),
        ];

        if (!$this->request->isEmpty('userForm')) {
            $values = $this->request->get('userForm', []);
            $errors = [];
            $values = \User::processForm($values, $errors);
            if (count($errors)) {
                $variables['notice'] = implode('. ', $errors);
            } else {
                $this->handleSubmit($values);
            }
        } else {
            $variables['values'] = [
                'email' => $this->currentUser->email,
                'comment' => $this->currentUser->comment,
            ];
        }
        return $variables;
    }

    private function getUserOptions() : array
    {
        $options = [];
        foreach ($this->currentUser->options->value as $name => $val)
        {
            $options[] = [
                'title' => \UserOptions::$titles[$name],
                'name' => $name,
                'value' => $val,
                'possibleValues' => \UserOptions::$possibleValues[$name],
            ];
        }
        return $options;
    }

    /**
     * Carryover from original webdip code. This is all terrible. Rewrite it.
     *
     * @param array $values
     * @return string
     */
    private function handleSubmit(array $values) : string
    {
        $noticeCodes = [];

        $allowed = [
            'email',
            'hideEmail',
            'comment'
        ];

        $this->currentUser->options->set($values);
        $this->currentUser->options->load();

        $set = [];
        foreach ($allowed as $fieldName)
        {
            if (!array_key_exists($fieldName, $values)) continue;

            // handle email change
            if ($fieldName == 'email' && $this->currentUser->email != $values['email']) {
                $userId = \User::findEmail($values['email']);
                if ($userId)
                    throw new \Exception(l_t("The e-mail address '%s', is already in use. Please choose another.",$values['email']));

                    $this->mailer->Send([
                        $values['email'] => $this->currentUser->username
                    ], l_t('Changing your e-mail address'), l_t("Hello %s",$this->currentUser->username).",<br><br>

					".l_t("You can use this link to change your account's e-mail address to this one:")."<br>
					".\libAuth::email_validateURL($values['email'])."<br><br>

					".l_t("If you have any further problems contact the server's admin at %s.",\Config::$adminEMail)."<br>
					".l_t("Regards,<br>webDiplomacy")."<br>
					");

                    $noticeCodes[] = 'emailUpdated';
                    unset($values['email']);
                    continue;
                }
            elseif ($fieldName == 'comment')
            {
                if ($this->currentUser->comment == $this->database->msg_escape($values['comment'])) continue;
            }

            $set[] = $fieldName . " = '" . $values[$fieldName] . "'";
        }

        if (!empty($set))
        {
            $sql = "UPDATE wD_Users SET ".join(', ', $set)." WHERE id = ".$this->currentUser->id;
            $this->database->sql_put($sql);
            $noticeCodes[] = 'updated';
        }

        if (!empty($values['password']))
        {
            $this->database->sql_put("UPDATE wD_Users SET password = ".$values['password']." WHERE id = ".$this->currentUser->id);

            \libAuth::keyWipe();
            header('refresh: 3; url=/users/login');

            $noticeCodes[] = 'passwordUpdated';
        }

        if (!empty($noticeCodes)) {
            $this->redirectRelative('/users/settings?notice=' . join(', ', $noticeCodes));
        }
        return '';
    }
}