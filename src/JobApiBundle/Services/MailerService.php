<?php

namespace JobApiBundle\Services;


use Psr\Container\ContainerInterface;

class MailerService
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * MailerService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function send(string $mailTo, string $html)
    {
        $msg = \Swift_Message::newInstance()
            ->setSubject('Confirmação')
            ->setFrom('send@gmail.com')
            ->setTo($mailTo)
            ->setBody($html)
            ->setContentType('text/html');

        $this->container->get('mailer')->send($msg);
    }
}