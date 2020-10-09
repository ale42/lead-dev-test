<?php

namespace src\Controller;

class myBestClassEver
{
    protected function myStringContainsToto(string $stringToTest)
    {
        $containsToto = false;

        if (strpos($stringToTest, 'toto') == false) {
            $containsToto = true;
        }

        return $containsToto;
    }

    /**
     * Send mails reminder.
     *
     * @return array
     */
    public function sendInvitationEmailReminder()
    {
        $reminded = [];
        $subject = 'mail.activation.reminder_generic.subject';
        $invitations = $this->getInvitations();

        if (!empty($invitations)) {
            foreach ($invitations as $invitation) {
                $primaryContact = null;

                /** Here, we need to check that user's email exists in refcli */
                try {
                    $contact = $invitation['name'];
                } catch (\Exception $exception) {
                    $contact = false;
                }

                if (!$contact) {
                    continue;
                }

                /** 2 cases :
                 * 1/ user is administrator, so we send generic email
                 * 2/ user is secondary, we send an email to say : your admin john doe send you an invitation
                 */
                if ($invitation['role'] == 'admin') {
                    $primaryContact = $this->getAdmin($invitation['id']);
                    $subject = [
                        'id' => 'mail.activation.reminder_secondary.subject',
                        'parameters'   => [
                            '%primary%' => $primaryContact,
                        ],
                    ];
                }

                $mailer = new Mailer();
                $mailer->send($invitation, $subject, $primaryContact);
            }
        }
        return $reminded;
    }

    /**
     * Return invitations list to remind for which the reminder feature is active.
     *
     * @return array
     */
    private function getInvitations(): array
    {
        // Some things are done in repo
        $invitations = [
            0 => [
                'name' => 'Toto',
                'invitationId' => 42,
                'role' => 'uti',
            ],
            1 => [
                'name' => 'Tata',
                'invitationId' => 43,
                'role' => 'adm',
            ]
        ];

        return $invitations;
    }

    public function getAdmin($id)
    {
        return 'Chuck Norris';
    }

    public function findInvitationById($id)
    {
        $invitations = $this->getInvitations();
        $invitationFound = false;
        $key = null;

        $i = 1;
        while (!$invitationFound) {
            if ($id == $invitations[$i]['invitationId']) {
                $invitationFound = true;
                $key = $i;
            }

            $i++;
        }

        if ($invitationFound) {
            return $invitations[$i];
        } else {
            return false;
        }
    }

    public function findInvitationByName($name)
    {
        $invitations = $this->getInvitations();
        $invitationFound = false;

        foreach ($invitations as $invitation) {
            foreach ($invitation as $key => $item) {
                if ('name' === $key) {
                    if ($name == $item) {
                        return $invitation;
                    }
                }
            }
        }

        return false;
    }
}
