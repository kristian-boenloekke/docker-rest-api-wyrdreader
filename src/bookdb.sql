-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2025 at 05:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`) VALUES
(1, 'Ever Dundas'),
(2, 'Reza Negarestani'),
(3, 'Charlie Human'),
(4, 'Robin Mackay'),
(5, 'Keith Tilford'),
(6, 'S.T. Gibson'),
(7, 'Kylie Leane '),
(8, 'Autumn Christian'),
(9, 'Robert Shea'),
(10, 'Robert Anton Wilson'),
(11, 'Ryan Madej'),
(12, 'Nick Mamatas'),
(13, 'Michael Moorcock');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `first_published` date DEFAULT NULL,
  `pages` int(11) NOT NULL,
  `image` varchar(40) DEFAULT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `first_published`, `pages`, `image`, `description`) VALUES
(1, 'Cyclonopedia: Complicity with Anonymous Materials', '2008-08-30', 268, NULL, 'At once a horror fiction, a work of speculative theology, an atlas of demonology, a political samizdat and a philosophic grimoire, CYCLONOPEDIA is work of theory-fiction on the Middle East, where horror is restlessly heaped upon horror. Reza Negarestani bridges the appalling vistas of contemporary world politics and the War on Terror with the archeologies of the Middle East and the natural history of the Earth itself. CYCLONOPEDIA is a middle-eastern Odyssey, populated by archeologists, jihadis, oil smugglers, Delta Force officers, heresiarchs, corpses of ancient gods and other puppets. The journey to the Underworld begins with petroleum basins and the rotting Sun, continuing along the tentacled pipelines of oil, and at last unfolding in the desert, where monotheism meets the Earth\'s tarry dreams of insurrection against the Sun. \'The Middle East is a sentient entity - it is alive!\' concludes renegade Iranian archeologist Dr. Hamid Parsani, before disappearing under mysterious circumstances. The disordered notes he leaves behind testify to an increasingly deranged preoccupation with oil as the \'lubricant\' of historical and political narratives. A young American woman arrives in Istanbul to meet a pseudonymous online acquaintance who never arrives. Discovering a strange manuscript in her hotel room, she follows up its cryptic clues only to discover more plot-holes, and begins to wonder whether her friend was a fictional quantity all along. Meanwhile, as the War on Terror escalates, the US is dragged into an asymmetrical engagement with occultures whose principles are ancient, obscure, and saturated in oil. It is as if war itself is feeding upon the warmachines, leveling cities into the desert, seducing the aggressors into the dark heart of oil ...'),
(2, 'Apocalypse Now Now', '2013-06-08', 304, NULL, 'Neil Gaiman meets Tarantino in this madcap, wildly entertaining journey into Cape Town\'s supernatural underworld.\r\n\r\nBaxter Zevcenko\'s life is pretty sweet. As the 16-year-old kingpin of the Spider, his smut-peddling schoolyard syndicate, he\'s making a name for himself as an up-and-coming entrepreneur. Profits are on the rise, the other gangs are staying out of his business, and he\'s going out with Esme, the girl of his dreams.\r\n\r\nBut when Esme gets kidnapped, and all the clues point towards strange forces at work, things start to get seriously weird. The only man drunk enough to help is a bearded, booze-soaked, supernatural bounty hunter that goes by the name of Jackson \'Jackie\' Ronin.\r\n\r\nPlunged into the increasingly bizarre landscape of Cape Town\'s supernatural underworld, Baxter and Ronin team up to save Esme. On a journey that takes them through the realms of impossibility, they must face every conceivable nightmare to get her back, including the odd brush with the Apocalypse.'),
(3, 'HellSans', '2022-10-11', 456, NULL, 'When every word you read, whatever it says, fills you with euphoric calm - that\'s HellSans; a typeface used everywhere by the government. To keep people happy. Blissful. And controlled.\r\n\r\nUnless you\'re allergic. And then every word is agony. Then HellSans is hell, and reading it will slowly kill you.\r\n\r\nHellSans is the story of two women.\r\n\r\nCEO Jane Ward is famous and successful, until she falls ill with the allergy and her life falls apart, dumping her in the ghetto with the other HSAs (HellSans Allergic). Where she meets...\r\n\r\nDr Icho Smith, a scientist who has a cure for the allergy. But she\'s on the run from the government, and the Seraphs, a terrorist group with their own plan for the HSAs...\r\n\r\nHellSans innovative structure allows you to read either Jane or Icho\'s story first, before their lives meet in the terrifying finale.\r\n\r\nHellSans is dystopia writ large. A novel where words can kill.'),
(4, 'Chronosis', '2019-11-12', 136, NULL, 'A unique fusion of comics culture and philosophical cogitation takes readers on a ride through time, space, and thought.\r\n\r\nApproaching the comic medium as a supercollider for achieving maximum abstraction, in Chronosis artist Keith Tilford and philosopher Reza Negarestani (author of Cyclonopedia and Intelligence and Spirit) create a graphically stunning and conceptually explosive universe in which the worlds of pop culture, modern art, philosophy, science fiction, and theoretical physics crash into one another.\r\n\r\nStalking the multiverse, a strange entity manifests itself in different guises, visiting trauma upon whoever it manifests to—whether Jeremy Charles, earthbound hawker of paranoid cosmic visions, or the interplanetary order of the Lazars, intent on extending their galactic empire to planet Earth. This is the figure of Time itself, with whose birth the story of Chronosis begins.\r\n\r\nDwelling nowhere and nowhen, the monk-like order of the Monazzeins are the only ones in the multiverse to have mastered Time. Chronosis narrates the story of a sprawling multiverse at the center of which their esoteric time-cult attempts to build bridges between the many fragmented tribes and histories of multiple possible worlds.\r\n\r\nA unique fusion of comics culture and philosophical cogitation, this conceptually and visually mind-expanding tale takes the reader on a dizzying rollercoaster ride through time, space, and thought.'),
(5, 'A Dowry of Blood', '2025-01-31', 292, NULL, 'S.T. Gibson\'s sensational novel is the darkly seductive tale of Dracula\'s first bride, Constanta.\r\n\r\nThis is my last love letter to you, though some would call it a confession. . .\r\n\r\nSaved from the brink of death by a mysterious stranger, Constanta is transformed from a medieval peasant into a bride fit for an undying king. But when Dracula draws a cunning aristocrat and a starving artist into his web of passion and deceit, Constanta realizes that her beloved is capable of terrible things.\r\n\r\nFinding comfort in the arms of her rival consorts, she begins to unravel their husband\'s dark secrets. With the lives of everyone she loves on the line, Constanta will have to choose between her own freedom and her love for her husband. But bonds forged by blood can only be broken by death.'),
(6, 'Key: The End of the Age of the Dragon\'s Conquest', '2013-10-31', 484, NULL, 'A shattered world\'s hope lies within a family and their echo through time... \r\nTheir world is collapsing, slowly, through Time, Space and all Realms. They have but one hope--a Key.\r\nZinkx Maz, a young battle-weary Messenger, is searching for the Key. No one knows what they Key is; a guide or a weapon. The only thing known for certain is that it is a chance for survival in the war against the Dragon. Along his journey Zinkx stumbles across a strange Kelib woman and together they are cast upon a voyage over the magnificent expanses of their land and beyond to uncover the secrets of an ancient race.\r\nWithin the metropolis of Palace-Town the Starborn Prince of Pennadot struggles to restore order to the Emerald Court. Slowly he is losing power to the Lords of the Provinces. He is opposed by the Dragon\'s Overlord who seeks to complete a plan that will change Pennadot forever. It is up to the Overlord\'s son to save the young Prince, and his quest leads him to something he had never dreamed was possible.'),
(7, 'The Crooked God Machine', '2010-12-23', 276, NULL, 'Charles lives on the black planet, a place where plague machines terrorize citizens with swarms of locusts and rivers of blood, salesmen sell sleep in the form of brain implants, and God appears on the television every night to warn of the upcoming apocalypse. When Charles meets Leda, a woman who claims to have escaped from hell, he begins to suspect that the black planet is not at all what it appears to be. After Leda disappears, Charles sets out to find her with help from his stripper ex-girlfriend, the deadhead Jeanine. Along the way he will uncover the truth of the origins of the black planet, and discover the source of the mysterious voice that calls to Leda from the ocean waves.'),
(8, 'The Illuminatus! Trilogy', '1983-12-01', 805, NULL, 'It was a deadly mistake. Joseph Malik, editor of a radical magazine, had snooped into rumors about an ancient secret society that was still alive and kicking. Now his offices have been bombed, he\'s missing, and the case has landed in the lap of a tough, cynical, streetwise New York detective. Saul Goodman knows he\'s stumbled onto something big—but even he can\'t guess how far into the pinnacles of power this conspiracy of evil has penetrated.\r\n\r\nFilled with sex and violence—in and out of time and space—the three books of The Illuminatus! Trilogy are only partly works of the imagination. They tackle all the cover-ups of our time—from who really shot the Kennedys to why there\'s a pyramid on a one-dollar bill—and suggest a mind-blowing truth.'),
(9, 'Assassin', '2021-09-22', 122, NULL, 'Ryan Madej\'s Assassin is an experimental novella with a deep esoteric background. In a dead city, a woman with a weapon that can erase its victims from time searches for prey. Lifetimes away, a man searches for a lost manuscript that will give him power over her. In an untouched paradise, an acolyte must choose to walk the path of enlightenment or destruction. Outside the linearity of time, their paths converge and threaten to destroy each other. Read an Equus interview with the author here.'),
(10, 'Move Under Ground', '2004-01-01', 160, NULL, 'The year is nineteen-sixty-something, and after endless millennia of watery sleep, the stars are finally right. Old R\'lyeh rises out of the Pacific, ready to cast its damned shadow over the primitive human world. The first to see its peaks: an alcoholic, paranoid, and frightened Jack Kerouac, who had been drinking off a nervous breakdown up in Big Sur. Now Jack must get back on the road to find Neal Cassady, the holy fool whose rambling letters hint of a world brought to its knees in worship of the Elder God Cthulhu. Together with pistol-packin\' junkie William S. Burroughs, Jack and Neal make their way across the continent to face down the murderous Lovecraftian cult that has spread its darkness to the heart of the American Dream. But is Neal along for the ride to help save the world, or does he want to destroy it just so that he\'ll have an ending for his book?'),
(11, 'Elric of Melniboné', '1972-01-01', 160, NULL, 'For ten thousand years Melniboné ruled the world. Elric, the 428th Emperor, seemed destined to see that era come to an end. An albino, sustained by rare drugs, it fell to him to confront the rise of the Young Kingdoms, of the monsters and sorceries which were threatening to overwhelm him and his ancient crown.');

-- --------------------------------------------------------

--
-- Table structure for table `book_authors`
--

CREATE TABLE `book_authors` (
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_authors`
--

INSERT INTO `book_authors` (`book_id`, `author_id`) VALUES
(1, 2),
(2, 3),
(3, 1),
(4, 2),
(4, 4),
(4, 5),
(5, 6),
(6, 7),
(7, 8),
(8, 9),
(8, 10),
(9, 11),
(10, 12),
(11, 13);

-- --------------------------------------------------------

--
-- Table structure for table `book_genres`
--

CREATE TABLE `book_genres` (
  `book_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_genres`
--

INSERT INTO `book_genres` (`book_id`, `genre_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3),
(2, 4),
(3, 4),
(3, 5),
(3, 6),
(1, 7),
(4, 1),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(5, 1),
(5, 3),
(5, 11),
(5, 12),
(6, 3),
(7, 1),
(7, 3),
(7, 4),
(7, 6),
(8, 3),
(8, 4),
(8, 1),
(8, 7),
(9, 3),
(9, 4),
(10, 1),
(10, 3),
(10, 4),
(11, 3);

-- --------------------------------------------------------

--
-- Table structure for table `book_ratings`
--

CREATE TABLE `book_ratings` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_ratings`
--

INSERT INTO `book_ratings` (`id`, `book_id`, `rating`, `created_at`, `user_id`) VALUES
(1, 1, 5, '2025-01-04', 3),
(2, 2, 3, '2025-01-04', 3),
(3, 3, 4, '2025-01-04', 3),
(5, 1, 4, '2025-01-04', 4),
(6, 2, 4, '2025-01-04', 4),
(7, 3, 3, '2025-01-04', 4),
(8, 4, 2, '2025-01-04', 4),
(9, 5, 3, '2025-01-04', 5),
(10, 4, 4, '2025-01-05', 5);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`, `description`) VALUES
(1, 'Horror', 'Horror fiction is fiction in any medium intended to scare, unsettle, or horrify the audience. Historically, the cause of the \"horror\" experience has often been the intrusion of a supernatural element into everyday human experience. Since the 1960s, any work of fiction with a morbid, gruesome, surreal, or exceptionally suspenseful or frightening theme has come to be called \"horror\". Horror fiction often overlaps science fiction or fantasy, all three of which categories are sometimes placed under the umbrella classification speculative fiction.'),
(2, 'Theory-Fiction', 'Theory-fiction is the intersection of theory and fiction, and also the “dissolution of the opposition itself”. In this hybrid style, theory is torn down from its pedestal, the real power of fiction is affirmed, and both are released from the high forms of the academy.'),
(3, 'Fantasy', 'Fantasy is a genre that uses magic and other supernatural forms as a primary element of plot, theme, and/or setting. Fantasy is generally distinguished from science fiction and horror by the expectation that it steers clear of technological and macabre themes, respectively, though there is a great deal of overlap between the three (collectively known as speculative fiction or science fiction/fantasy)\r\n\r\nIn its broadest sense, fantasy comprises works by many writers, artists, filmmakers, and musicians, from ancient myths and legends to many recent works embraced by a wide audience today, including young adults, most of whom are represented by the works below.'),
(4, 'Science Fiction', 'Science fiction (abbreviated SF or sci-fi with varying punctuation and capitalization) is a broad genre of fiction that often involves speculations based on current or future science or technology. Science fiction is found in books, art, television, films, games, theatre, and other media. In organizational or marketing contexts, science fiction can be synonymous with the broader definition of speculative fiction, encompassing creative works incorporating imaginative elements not found in contemporary reality; this includes fantasy, horror and related genres.\r\n\r\nAlthough the two genres are often conflated as science fiction/fantasy, science fiction differs from fantasy in that, within the context of the story, its imaginary elements are largely possible within scientifically established or scientifically postulated laws of nature (though some elements in a story might still be pure imaginative speculation). Exploring the consequences of such differences is the traditional purpose of science fiction, making it a \"literature of ideas\". Science fantasy is largely based on writing entertainingly and rationally about alternate possibilities in settings that are contrary to known reality.'),
(5, 'Thriller', 'Thrillers are characterized by fast pacing, frequent action, and resourceful heroes who must thwart the plans of more-powerful and better-equipped villains. Literary devices such as suspense, red herrings and cliffhangers are used extensively.\r\n\r\nThrillers often overlap with mystery stories, but are distinguished by the structure of their plots. In a thriller, the hero must thwart the plans of an enemy, rather than uncover a crime that has already happened. Thrillers also occur on a much grander scale: the crimes that must be prevented are serial or mass murder, terrorism, assassination, or the overthrow of governments. Jeopardy and violent confrontations are standard plot elements. While a mystery climaxes when the mystery is solved, a thriller climaxes when the hero finally defeats the villain, saving his own life and often the lives of others.'),
(6, 'Dystopia', 'Dystopia is a form of literature that explores social and political structures. It is a creation of a nightmare world - unlike its opposite, Utopia, which is an ideal world.\r\n\r\nDystopia is often characterized by an authoritarian or totalitarian form of government. It often features multiple kinds of repressive social control systems, a lack or total absence of individual freedoms and expressions, and a state of constant warfare or violence.\r\n\r\nMany novels combine Dystopia and Utopia, often as a metaphor for the different directions humanity can take in its choices, ending up with one of the two possible futures.\r\n\r\nDystopia is very similar to False Utopia, but instead of the often visibly oppressive and/or anarchic \"true\" Dystopia, a False Utopia appears inviting at first and may well be a nice place to live in but hides a dark and often terrible secret beneath its innocent exterior.'),
(7, 'Philosophy', 'Philosophy is the study of general problems concerning matters such as existence, knowledge, truth, beauty, justice, validity, mind, and language. Philosophy is distinguished from other ways of addressing these questions (such as mysticism or mythology) by its critical, generally systematic approach and its reliance on reasoned argument. The word philosophy is of Ancient Greek origin: φιλοσοφία (philosophía), meaning \"love of wisdom.\"'),
(8, 'Graphic Novels', 'A graphic novel is a narrative work in which the story is conveyed to the reader using sequential art in either an experimental design or in a traditional comics format. The term is employed in a broad manner, encompassing non-fiction works and thematically linked short stories as well as fictional stories across a number of genres.'),
(9, 'Comics', 'A comic book or comicbook, also called comic magazine or simply comic, is a publication that consists of comic art in the form of sequential juxtaposed panels that represent individual scenes. Panels are often accompanied by brief descriptive prose and written narrative, usually dialog contained in word balloons emblematic of the comics art form.'),
(10, 'Comix', 'Underground comix are small/specialty press or self-published comic books which are often socially relevant or satirical in nature. They differ from mainstream comics in depicting content forbidden to mainstream publications by the Comics Code Authority, including explicit drug use, sexuality, and violence.'),
(11, 'Gothic', NULL),
(12, 'Romance', NULL),
(13, 'Urban Fantasy', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `review` text NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `review`, `book_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Read it twice and still have no idea what this book is about. 5/5, brilliant', 1, 4, '2025-01-04', NULL),
(3, 'A great mix of Harry Potter and Ghostbusters, if Harry was the teenage kingpin of a Hogwarts porn-ring, coming of age to the grim realization that his collection of creature-porn might actually not be the product of CGI', 2, 4, '2025-01-04', NULL),
(5, 'My sense of self and the world got butchered open by this rare work of mindfuck. Never again will I be able to perceive the so-called \'natural\' phenomena of this planet as other than the evil avatars and eldritch weapons of the tellurian Insider', 1, 5, '2025-01-04', NULL),
(6, 'Pretty good! Great take on the future AI assistent, but I had a hard time rallying up much sympathy for Jane Ward though', 3, 5, '2025-01-04', NULL),
(7, 'An absolutely thrilling opening chapter of the book, scaringly seductive in its violent and lush gothic prose, this opening lingers like intoxicating perfume throughout, but a bit to my disappointment, I didn\'t feel like I really got that same scent  back in full in the rest of the book', 5, 5, '2025-01-04', '2025-01-04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `salt` varchar(50) NOT NULL,
  `books` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `reviews` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `salt`, `books`, `reviews`) VALUES
(1, 'test', 'mail@mail.dk', '$2y$10$BiVN5QHs/TrJOmehfGwZGuibtUIlBwMCi867mRpFTUAvWnDJQjynm', 'c30c9d4868048fda6a32bc31b3e184c5', NULL, NULL),
(2, 'user2', 'mail2@mail.com', '$2y$10$SWXWC5BR2X8FLSoEqyuCb.M75Y.okUvi7vTqwPodO6vroCVfgIap.', 'd5f47489035467806fd815e2e4b0c82a', NULL, NULL),
(3, 'gnommi', 'gnommi@mail.com', '$2y$10$7dNfns2P2LOpg6PB6KWoAeHmqakHTNoJE8HaP6L4FSD7J1929/UNC', 'ee25d7e96d011259d33ac57021220483', NULL, NULL),
(4, 'Loïc Dumat', 'loic@mail.com', '$2y$10$6Nr77hxBJkYAdQ/6Uqyt0uu5VPNpzutjFzc3ztBvp1NL6fNPQB/W6', 'a77813cd6a20a94ba7619e76eaecfa65', NULL, NULL),
(5, 'Havok Fairhair', 'havok@mail.com', '$2y$10$eas3VMJpX.qudWpD.05beukM6Y9NnNXPDVdc7WjCbnfhbeu3O23xa', 'dbc9b7c0ede0c944d3dfc8b45132bda9', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_books`
--

CREATE TABLE `user_books` (
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_books`
--

INSERT INTO `user_books` (`user_id`, `book_id`) VALUES
(1, 1),
(1, 4),
(1, 2),
(1, 3),
(3, 1),
(3, 2),
(3, 3),
(5, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_ratings`
--
ALTER TABLE `book_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `book_ratings`
--
ALTER TABLE `book_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
