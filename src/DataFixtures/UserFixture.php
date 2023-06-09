<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Discover;
use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Article;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
  //pour haché les mdp on a besoin d'un constructeur
  public function __construct(
    private UserPasswordHasherInterface $passwordEncoder
  ) {
  }

  public function load(ObjectManager $manager): void
  {
    $admin = new User();
    $admin->setEmail('contact@blog-participatif.tech');
    $admin->setName('Admin');
    $admin->setPseudo('Admin');
    $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'azerty'));
    $admin->setRoles(['ROLE_ADMIN']);
    $admin->setIsVerified(1);
    $manager->persist($admin);

    $user1 = new User();
    $user1->setEmail('michellouis@orange.fr');
    $user1->setName('Louis');
    $user1->setPseudo('Michel');
    $user1->setPassword($this->passwordEncoder->hashPassword($user1, 'azerty'));
    $user1->setRoles(['ROLE_USER']);
    $user1->setCreatedAt(new \DateTimeImmutable('2021-06-06 11:00'));
    $user1->setIsVerified(1);
    $manager->persist($user1);
    $this->addReference('Michel', $user1);

    $user2 = new User();
    $user2->setEmail('myriambetzy@tel.fr');
    $user2->setName('Betzy');
    $user2->setPseudo('Myriam');
    $user2->setPassword($this->passwordEncoder->hashPassword($user2, 'azerty'));
    $user2->setRoles(['ROLE_USER']);
    $user2->setCreatedAt(new \DateTimeImmutable('2021-05-05 11:00'));
    $user2->setIsVerified(1);
    $manager->persist($user2);
    $this->addReference('Myriam', $user2);

    $user3 = new User();
    $user3->setEmail('jacquesandré@goo.fr');
    $user3->setName('Jacques');
    $user3->setPseudo('André');
    $user3->setPassword($this->passwordEncoder->hashPassword($user3, 'azerty'));
    $user3->setRoles(['ROLE_USER']);
    $user3->setCreatedAt(new \DateTimeImmutable('2021-04-04 11:00'));
    $user3->setIsVerified(1);
    $manager->persist($user3);
    $this->addReference('André', $user3);

    $category = new Category();
    $category->setName('Amérique centrale');
    $manager->persist($category);


    $article1 = new Article();
    $article1->setTitle('Costa Rica');
    $article1->setContent('Lors de ma visite au Costa Rica en 2020, j\'ai été émerveillé par la beauté naturelle exceptionnelle de ce pays d\'Amérique centrale. Niché entre l\'océan Pacifique et la mer des Caraïbes, le Costa Rica offre une diversité incroyable de paysages, de faune et de flore, ainsi qu\'une culture riche et accueillante.
    
    L\'un des aspects les plus frappants du Costa Rica est sa végétation luxuriante. Des forêts tropicales denses, abritant une incroyable variété d\'espèces animales et végétales, aux volcans majestueux recouverts de brume, chaque coin du pays offre une expérience unique. Lors de mes explorations, j\'ai eu la chance de découvrir des parcs nationaux exceptionnels tels que le parc national de Tortuguero, où j\'ai pu observer les tortues marines lors de leur période de ponte, ainsi que le parc national Manuel Antonio, avec ses plages de sable blanc et sa biodiversité incroyable.
    
    En parlant de biodiversité, le Costa Rica est réputé pour être l\'un des pays les plus riches en termes de faune. J\'ai été émerveillé par la variété des espèces que j\'ai pu observer, allant des singes hurleurs aux paresseux suspendus aux arbres, en passant par les toucans aux couleurs vives et les grenouilles colorées. Les amateurs de nature et d\'écotourisme trouveront leur bonheur dans ce paradis tropical, où la préservation de l\'environnement est une priorité.
    
    Outre sa nature extraordinaire, le Costa Rica possède également une culture vibrante et chaleureuse. J\'ai été accueilli à bras ouverts par les habitants, connus sous le nom de "Ticos", qui se sont révélés être des hôtes exceptionnels. J\'ai eu l\'occasion de goûter à la délicieuse cuisine costaricaine, avec des plats traditionnels tels que le gallo pinto (mélange de riz et de haricots), le ceviche (plat à base de fruits de mer) et les délicieux fruits tropicaux frais.
    
    En parcourant le pays, j\'ai également pu apprécier l\'engagement du Costa Rica envers la durabilité et la préservation de l\'environnement. Le pays est un leader mondial dans les énergies renouvelables, avec une grande partie de son électricité provenant de sources propres telles que l\'hydroélectricité et l\'énergie éolienne. De plus, de nombreux établissements touristiques adoptent des pratiques écologiques et respectueuses de la nature, ce qui renforce le sentiment d\'être en harmonie avec l\'environnement.
    
    Ma visite au Costa Rica en 2020 restera à jamais gravée dans ma mémoire. Ce pays magnifique m\'a offert des expériences inoubliables, des rencontres enrichissantes et une profonde connexion avec la nature. Que ce soit en explorant ses parcs nationaux, en se relaxant sur ses plages idylliques ou en découvrant sa culture fascinante, le Costa Rica est une destination de voyage qui ne manquera pas de combler les amoureux de la nature et les aventuriers en quête de découvertes uniques.');
    // $category = $this->addReference('Amérique centrale', $category);
    $article1->setCategory($category);
    $article1->setUser($admin);
    $manager->persist($article1);
    $this->addReference('article1', $article1);

    $post1 = new post();
    $post1->setTitle('Une expérience naturelle inoubliable');
    $post1->setContent("Au Costa Rica, j'ai vécu une expérience naturelle inoubliable. Ce pays regorge de richesses naturelles exceptionnelles, des forêts tropicales luxuriantes aux volcans majestueux. J'ai eu la chance d'explorer les parcs nationaux et de me perdre au milieu d'une biodiversité époustouflante. Les singes qui se balançaient dans les arbres, les toucans aux couleurs vives et les paresseux paisibles ont rendu mes randonnées encore plus magiques. Les plages immaculées et préservées ont également ajouté une touche de beauté naturelle à mon voyage. Le Costa Rica est vraiment un paradis pour les amoureux de la nature.");
    $post1->setPublishedAt(new \DateTime('2021-06-06 12:00'));
    $user = $this->getReference('Michel');
    $post1->setUser($user);
    $manager->persist($post1);
    $this->addReference('post1', $post1);

    $post2 = new post();
    $post2->setTitle('L\'accueil chaleureux des Ticos');
    $post2->setContent("Mon séjour au Costa Rica a été marqué par l'accueil chaleureux des Ticos, les habitants du pays. Dès mon arrivée, j'ai été accueilli avec un sourire chaleureux et une hospitalité sans pareille. Les Ticos sont fiers de leur pays et de leur culture, et ils sont toujours prêts à partager leurs traditions et leur savoir avec les voyageurs. J'ai été invité à goûter des plats typiques, à participer à des festivals locaux et à découvrir leur mode de vie paisible. Les échanges avec les habitants ont ajouté une dimension humaine et authentique à mon voyage, faisant du Costa Rica une destination où l'on se sent véritablement le bienvenu.");
    $post2->setPublishedAt(new \DateTime('2021-05-05 19:00'));
    $user = $this->getReference('Myriam');
    $post2->setUser($user);
    $manager->persist($post2);
    $this->addReference('post2', $post2);


    $post3 = new post();
    $post3->setTitle('Un engagement remarquable envers la durabilité');
    $post3->setContent("Ce qui m'a le plus impressionné lors de mon voyage au Costa Rica, c'est l'engagement remarquable du pays envers la durabilité et la préservation de l'environnement. Des parcs nationaux bien gérés aux initiatives écologiques. J'ai été impressionné par la quantité d'énergies renouvelables utilisées dans le pays et par les efforts pour réduire l'empreinte écologique du tourisme. Les hébergements respectueux de l'environnement, les pratiques agricoles durables et les programmes de conservation témoignent de la volonté du Costa Rica de préserver son patrimoine naturel pour les générations futures.");
    $post3->setPublishedAt(new \DateTime('2021-04-04 18:00'));
    $user = $this->getReference('André');
    $post3->setUser($user);
    $manager->persist($post3);
    $this->addReference('post3', $post3);


    $discover1 = new Discover();
    $discover1->setCountry('Costa Rica');
    $discover1->setCapital('San José');
    $discover1->setLanguage('Espagnol');
    $discover1->setCurrency('Colón costaricien');
    $discover1->setArea('51 100 km²');
    $discover1->setPopulation('5,1 millions');
    $discover1->setContentIntro("Le Costa Rica, petit joyau d'Amérique centrale, est un pays qui évoque des images de plages tropicales, de jungles luxuriantes et de volcans majestueux. Niché entre la mer des Caraïbes et l'océan Pacifique, ce pays fascinant offre aux voyageurs une expérience inoubliable au cœur de la nature. Mais le Costa Rica est bien plus qu'une simple destination de vacances exotique. C'est un pays qui met en avant des valeurs telles que la préservation de l'environnement, l'éducation et la paix.");
    $discover1->setContent("Le Costa Rica est réputé pour sa biodiversité extraordinaire, abritant une multitude d'espèces animales et végétales uniques au monde. Ses parcs nationaux et ses réserves naturelles offrent des opportunités incroyables d'explorer des écosystèmes variés, allant des forêts tropicales humides aux mangroves en passant par les récifs coralliens. Les amoureux de la nature peuvent observer des singes hurleurs, des toucans colorés, des paresseux tranquilles et même des tortues marines venant pondre sur les plages.

    Le Costa Rica est également connu pour sa politique environnementale progressiste. Le pays s'est fixé pour objectif d'être neutre en carbone d'ici 2021, en favorisant les énergies renouvelables et en protégeant ses ressources naturelles. Les initiatives de conservation sont mises en place pour préserver les habitats fragiles et promouvoir le tourisme durable. Les visiteurs ont la possibilité de participer à des activités respectueuses de l'environnement, telles que l'observation des oiseaux, la randonnée dans la canopée et l'exploration des parcs nationaux.

    Le peuple costaricien, appelé les 'Ticos', est réputé pour sa convivialité et sa joie de vivre. Leur devise nationale, 'Pura Vida', résume leur façon de voir la vie : profiter de l'instant présent, être reconnaissant pour les petites choses et trouver le bonheur dans la simplicité. Les traditions et la culture costariciennes sont profondément enracinées, et les visiteurs peuvent découvrir la cuisine locale, la musique traditionnelle et les danses folkloriques lors de festivals colorés.");
    $discover1->setContentFooter("En conclusion, le Costa Rica est une destination de voyage extraordinaire qui offre une combinaison parfaite de nature préservée, d'aventure, de culture et de détente. Que vous soyez un passionné de plein air à la recherche de nouvelles découvertes ou que vous souhaitiez simplement vous détendre sur des plages de sable blanc, le Costa Rica saura vous séduire.
    
    En explorant ce pays enchanteur, vous serez émerveillé par la richesse de sa biodiversité, la beauté de ses paysages et l'hospitalité chaleureuse de ses habitants. Que vous choisissiez de vous aventurer dans les profondeurs de la forêt tropicale, de vous détendre sur une plage isolée ou d'admirer les volcans actifs, chaque instant au Costa Rica sera une expérience inoubliable.");
    $manager->persist($discover1);
    $this->addReference('decouverte1', $discover1);

    $image1 = new Image();
    $user = $this->getReference('post1');
    $image1->setPost($post1);
    $image1->setPath('1e2ad86c1f353af825305d624bb66e07.webp');
    $manager->persist($image1);

    $image2 = new Image();
    $user = $this->getReference('post2');
    $image2->setPost($post2);
    $image2->setPath('2c96d7031a6f310169cc33add733cff0.webp');
    $manager->persist($image2);

    $image3 = new Image();
    $user = $this->getReference('post3');
    $image3->setPost($post3);
    $image3->setPath('3d90ef11090aea74d62bd266f795f7b3.webp');
    $manager->persist($image3);

    $image4 = new Image();
    $user = $this->getReference('article1');
    $image4->setArticle($article1);
    $image4->setPath('1c9cf1c7a50e8e814b8daf61fb005795.webp');
    $manager->persist($image4);
    
    $image5 = new Image();
    $user = $this->getReference('decouverte1');
    $image5->setDiscover($discover1);
    $image5->setPath('2c96d7031a6f310169cc33add733cff0.webp');
    $manager->persist($image5);
    
    $image6 = new Image();
    $user = $this->getReference('decouverte1');
    $image6->setDiscover($discover1);
    $image6->setPath('1c9cf1c7a50e8e814b8daf61fb005795.webp');
    $manager->persist($image6);
    
    $image6 = new Image();
    $user = $this->getReference('decouverte1');
    $image6->setDiscover($discover1);
    $image6->setPath('3d90ef11090aea74d62bd266f795f7b3.webp');
    $manager->persist($image6);

    $manager->flush();
  }
}
