<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailSender
{
    private Environment $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array $container
     * @param string|null $url
     * @return bool
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendEmail(array $container): bool
    {
        $mj = new Client( //fonction api
            '8948cf8a466e25b726d175c9438c1b50', //PUBLIC KEY
            '732674cfc547547f6565cd10b2e808bb',// PRIVATE KEY
            true,
            ['version' => 'v3.1']
        );
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $container['mailInfo']['mailExpeditor'],
                        'Name' => $container['mailInfo']['nameExpeditor'],
                    ],
                    'To' => [
                        [
                            'Email' => $container['mailInfo']['receiverList']['Email'], // MANAGER £mail receiver
                            'Name' => $container['mailInfo']['receiverList']['Name'],
                        ]
                    ],
                    'Subject' => $container['mailInfo']['subject'], // VERIFY RESERVATION DANS MANGER 
                    'TextPart' => 'Mailer',
                    'HTMLPart' => $this->twig->render($container['view'], // HTML DE MAIL SEND USER.TWIG
                        ['data'=>$container['data'],]),
                    'CustomID' => 'yosr mailer',
                ],
            ],
        ];
        try {
            $response = $mj->post(Resources::$Email, ['body' => $body]);
            return $response->success();
        } catch (\Exception $e) {
            return false;
        }
    }
}
