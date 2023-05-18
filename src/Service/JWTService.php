<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
  // On génère le token
  /**
   * @param array $header // header du token
   * @param array $payload // payload du token
   * @param string $secret // secret du token
   * @param int $validity   // validité du token
   * @return string // retourne le token
   */

public function generate (array $header, array $payload, string $secret, int $validity = 10800): string
// Attention le secret ne doit etre nule part. 10800 secondes = 3 heures durée du token
{
 //On verifie si la validité est plus grande que 0
  if($validity > 0){
// On va chercher l'heure qu'il est
  $now = new DateTimeImmutable();
  // On va chercher la durée d'expiration
  $exp = $now->getTimestamp() + $validity;
  // issued at qui est maintenant
  $payload['iat'] = $now->getTimestamp();
  // On ajoute l'expiration
  $payload['exp'] = $exp;  
}

  

  // On encode en base64 parce que les jwt c'est de l'encodage 64
  $base64Header = base64_encode(json_encode($header));
  $base64Payload = base64_encode(json_encode($payload));

  // Une fois que l'on a encodé on nettoie les valeurs encodées retrait des + / = et on les remplace par des - _ et rien
  $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
  $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

  // On va generer la signature dans le .env.local on genere une clé secrete
  $secret = base64_encode($secret);

  // cette clé secrete est utilisée pour générer la signature sha256 c'est un algorithme de hachage puis on concatene
  // le header et le payload
  $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

  // On encode la signature en base64
  $base64Signature = base64_encode($signature);

  // On remplace les + / = par des - _ et rien
  $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

  // On encode le token en base64
  $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

  // On retourne le token
  return $jwt;
}

  // On vérifie le token, qu'il soit valide et correctement formé avec isValid
  public function isValid(string $token):bool
  {
    return preg_match(
      // Regexp qui vérifie que le token est bien formé
      // Le 1er groupe est le header il verifie qu'il y a des lettre en miniscule ou majuscule avec des 
      // Chiffres de 0 a 9, le 2eme le payload c'est la même chose et le 3eme la signature idem.
      '/^[a-zA-Z0-9_-]+.[a-zA-Z0-9_-]+.[a-zA-Z0-9_-]+$/',
      $token
    ) === 1;
  }

    // On récupère le Payload du token pour récupérer la date d'expiration, il renvoi un tableau
  public function getPayload(string $token): array
  {
      // On démonte le token a chaque fois qu'il y a un point, donc en 3 parties
      $array = explode('.', $token);

      // On décode la partie 2 du token qui est le payload
      $payload = json_decode(base64_decode($array[1]), true);

      return $payload;
  }

    // On récupère le Heager du token, il renvoi un tableau
  public function getHeader(string $token): array
  {
      // On démonte le token a chaque fois qu'il y a un point, donc en 3 parties
      $array = explode('.', $token);

      // On décode la partie 1 du token qui est le header
      $header = json_decode(base64_decode($array[0]), true);

      return $header;
  }

  // On vérifie que le token n'est pas expiré avec un booléen
  public function isExpired(string $token): bool
  {
    // On récupère le payload
    $payload = $this->getPayload($token);

    // On récupère la date actuelle
    $now = new DateTimeImmutable();

    // On récupère la date d'expiration
    // On vérifie que la date d'expiration est supérieure à la date actuelle
    return $payload['exp'] < $now->getTimestamp();  
  }

  // On vérifie que la signure du token est bien valide
  public function check(string $token, string $secret)
  {
    // On récupère le header et le payload
    $header = $this->getHeader($token);
    $payload = $this->getPayload($token);

    // On régère un token / attention 0 pour la validité car on veut vérifier le token et pas le regénérer
    $verifToken = $this->generate($header, $payload, $secret, 0);

    return $token === $verifToken;
  }

}

