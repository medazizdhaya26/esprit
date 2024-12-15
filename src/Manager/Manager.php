<?php

namespace App\Manager;

use App\Service\MailSender;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Manager
{

    public function __construct( private MailSender $mailSender)
    {
        $this->mailSender = $mailSender;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendMail($data): bool
    {
        
        $mail = $data->getEmail(); 
        $container['data'] = $data;
        $container['view'] = 'mail/send.user.twig'; //html de mail
        $container['mailInfo'] = [ // DONNE DE ETUDIANT
            'receiverList' => [
                'Email' => $mail,
                'Name' => $mail,
            ],
            'mailExpeditor' => 'hedi.laater@gmail.com',
            'nameExpeditor' => 'Esprit',
            'subject' => 'Verify Reservation'
        ];

        return $this->mailSender->sendEmail($container);
    }

}
