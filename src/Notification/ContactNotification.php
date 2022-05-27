<?php
namespace App\Notification;

use App\Entity\Contact;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ContactNotification
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param Contact $contact
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function notify(Contact $contact)
    {
        $message = (new Email())
            ->from('noreply@agence.fr')
            ->to('contact@agence.fr')
            ->replyTo($contact->getEmail())
            ->subject('email message')
            ->html(
                $this->twig->render('emails/contact.html.twig', [
                    'contact' => $contact
                ]), 'text/html'
            );
        $this->mailer->send($message);
    }
}