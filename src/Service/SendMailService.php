<?php
// On créé le namespace
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

// On créé une classe SendMailService
class SendMailService
{
  //cette class va envoyer un mail en privé, propriété mailer que l'on va appeler a plein d'endroit
  private $mailer;

  // On créé un constructeur qui va prendre en paramètre un objet de type MailerInterface
  public function __construct(MailerInterface $mailer){
    // On définit la propriété mailer
    $this->mailer = $mailer;
  }

  // On créé une méthode qui va envoyer un mail, elle prend plusieurs paramètres chaine de caractére $from puis $to puis $subject puis $template et un tableau qui sera les differentes variables que l'on utilisera au niveau de notre mail et il ne renvoi rien void.
  public function send(string $from, string $to, string $subject, string $template, array $context): void{
    // On crée le mail. TemplatedEmail est le composant qui permet de créer un mail
    $email = (new TemplatedEmail())
    // On définit l'expéditeur
    ->from($from)
    // On définit le destinataire
    ->to($to)
    // On définit le sujet
    ->subject($subject)
    // On définit le template
    ->htmlTemplate("emails/$template.html.twig")
    // On définit les variables du template
    ->context($context);
    // On envoie le mail
    $this->mailer->send($email);
  }
}

