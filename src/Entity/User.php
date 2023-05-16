<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
//une entité User doit avoir un email unique
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  #[Assert\NotBlank(message: 'L\'adresse email est obligatoire')]
  #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
  private ?string $email = null;

  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Assert\Length(min: 2, max: 100, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères', maxMessage: 'Le nom doit contenir au maximum {{ limit }} caractères')]
  private ?string $name = null;

  #[ORM\Column(length: 255)]
  #[Assert\Length(min: 2, max: 80, minMessage: 'Le pseudo doit contenir au moins {{ limit }} caractères', maxMessage: 'Le pseudo doit contenir au maximum {{ limit }} caractères')]
  private ?string $pseudo = null;

  #[ORM\Column(type: 'boolean')]
  private $is_verified = false;

  #[ORM\Column(length: 100, nullable: true)]
  private ?string $resetToken = null;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Article::class)]
  private Collection $articles;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class)]
  private Collection $posts;

  // #[ORM\OneToMany(mappedBy: 'users', targetEntity: Booking::class)]
  // private Collection $bookings;

  public function __construct()
  {
    // $this->bookings = new ArrayCollection();
    $this->articles = new ArrayCollection();
    $this->posts = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }


  public function getPseudo(): ?string
  {
    return $this->pseudo;
  }

  public function setPseudo(string $pseudo): self
  {
    $this->pseudo = $pseudo;

    return $this;
  }

  public function getIsVerified(): ?bool
  {
    return $this->is_verified;
  }

  public function setIsVerified(bool $is_verified): self
  {
    $this->is_verified = $is_verified;

    return $this;
  }

  public function getResetToken()
  {
    return $this->resetToken;
  }

  public function setResetToken($resetToken)
  {
    $this->resetToken = $resetToken;

    return $this;
  }

  // /**
  //  * @return Collection<int, Booking>
  //  */
  // public function getBookings(): Collection
  // {
  //   return $this->bookings;
  // }

  // public function addBooking(Booking $booking): self
  // {
  //   if (!$this->bookings->contains($booking)) {
  //     $this->bookings->add($booking);
  //     $booking->setUsers($this);
  //   }

  //   return $this;
  // }

  // public function removeBooking(Booking $booking): self
  // {
  //   if ($this->bookings->removeElement($booking)) {
  //     // set the owning side to null (unless already changed)
  //     if ($booking->getUsers() === $this) {
  //       $booking->setUsers(null);
  //     }
  //   }

  //   return $this;
  // }

  /**
   * @return Collection<int, Article>
   */
  public function getArticles(): Collection
  {
      return $this->articles;
  }

  public function addArticle(Article $article): self
  {
      if (!$this->articles->contains($article)) {
          $this->articles->add($article);
          $article->setUser($this);
      }

      return $this;
  }

  public function removeArticle(Article $article): self
  {
      if ($this->articles->removeElement($article)) {
          // set the owning side to null (unless already changed)
          if ($article->getUser() === $this) {
              $article->setUser(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Post>
   */
  public function getPosts(): Collection
  {
      return $this->posts;
  }

  public function addPost(Post $post): self
  {
      if (!$this->posts->contains($post)) {
          $this->posts->add($post);
          $post->setUser($this);
      }

      return $this;
  }

  public function removePost(Post $post): self
  {
      if ($this->posts->removeElement($post)) {
          // set the owning side to null (unless already changed)
          if ($post->getUser() === $this) {
              $post->setUser(null);
          }
      }

      return $this;
  }
}
