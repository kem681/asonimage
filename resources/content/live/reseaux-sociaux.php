<?php

/*
|--------------------------------------------------------------------------
| Modele de sondage : atelier « Reseaux sociaux : comment gerer pour grandir ? »
|--------------------------------------------------------------------------
| Les treize questions de l'atelier (v2 du 18.09.2026), dans l'ordre de
| P:\ASI\ateliers\reseaux-sociaux\atelier-parle.md.
| Types : choice (choix unique), words (nuage de mots), text (texte libre).
| notes : ce que l'animateur voit dans la vue presentateur (quand lancer, quoi dire). Jamais montre a la salle.
*/

return [
    'title' => 'Réseaux sociaux : comment gérer pour grandir ?',
    'questions' => [
        [
            'type' => 'words',
            'prompt' => "Aujourd'hui, à ton avis, qui fixe les critères de beauté ? Un mot.",
            'notes' => "Bloc 1, l'article, 3 min. Lancer juste après avoir lu le titre du journal et décrit Omoggle. Une minute.\n\nEn affichant : « Regardez ce qui est gros. Personne n'a écrit \"nous\". » Puis : l'IA ne fixe rien, elle fait la moyenne de nos regards, c'est le monde qui fixe les critères, et le monde c'est nous. Lire Romains 12,2 en entier. Poser la question de l'atelier : d'où vient un critère, si ce n'est pas de la moyenne ? On va le trouver ensemble.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Lequel des deux visages est beau ?",
            'options' => [
                'Celui de gauche',
                'Celui de droite',
                'Les deux',
                'Aucun des deux',
            ],
            'notes' => "Cas 1, bloc 2. Les deux photos côte à côte sur le beamer : à gauche le portrait lissé, à droite le visage âgé qui rit. Ne rien dire dessus. Une minute, afficher.",
        ],
        [
            'type' => 'words',
            'prompt' => "Pourquoi, en un mot ?",
            'notes' => "Cas 1, tout de suite après le vote. Une minute, afficher et LAISSER À L'ÉCRAN.\n\nNe pas conclure. Lire les gros mots à voix haute (vivant, vrai, joie / joli, parfait, froid). Dire seulement : « Omoggle donnerait 9 à gauche et 3 à droite. Vous venez de voter autrement. Retenez ça. » Puis lire 1 Samuel 16,7 en entier.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Laquelle des deux versions est belle ?",
            'options' => [
                'La première',
                'La seconde',
                'Les deux',
                'Aucune',
            ],
            'notes' => "Cas 2, bloc 2. La louange de l'église brute (20 s), puis la même au format brainrot (1,5×, sous-titres jaunes, Subway Surfers). Ne pas dire d'où vient la vidéo avant le vote. Afficher.",
        ],
        [
            'type' => 'words',
            'prompt' => "Pourquoi, en un mot ?",
            'notes' => "Cas 2, après le vote. Afficher.\n\n« Vous avez reconnu ? C'est nous, dimanche passé. » Lire les mots. « Le même contenu, dans le mauvais format, n'est plus beau, et rien n'a été enlevé. » Lire Ecclésiaste 3,11 en entier : belle au moment voulu.\n\nENSUITE LE RAMASSAGE (bloc 3, 5 min, sans sondage) : joli contre beau, le beau c'est quand une chose s'aligne avec ce pour quoi elle a été faite. Lire Jean 1,1-5 et 14. Logos défini. Le logos est une Personne. Image, pas fonction. Genèse 1,31, tov.",
        ],
        [
            'type' => 'text',
            'prompt' => "Un contenu vu sur ton feed cette semaine que tu as trouvé beau. Décris-le en une ligne.",
            'notes' => "Bloc 4, les cas de la salle, 5 min. Une minute. Afficher, lire, en choisir TROIS différents (une photo, une vidéo, un texte, un truc chrétien si possible). Puis un vote par cas avec les trois questions suivantes.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ce contenu : aligné avec ce qu'il est ?",
            'options' => [
                'Oui',
                'Non',
                'Je ne sais pas',
            ],
            'notes' => "Cas de la salle 1. Lire la description à voix haute avant d'ouvrir. Afficher. Demander à celui qui l'a décrit, s'il se signale, ce qu'il en pense. Ne pas trancher à sa place : poser la question « est-ce que ça montre, ou est-ce que ça cache ? ».",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ce contenu : aligné avec ce qu'il est ?",
            'options' => [
                'Oui',
                'Non',
                'Je ne sais pas',
            ],
            'notes' => "Cas de la salle 2. Même déroulé. Si le temps manque, sauter directement à la grille de Paul.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ce contenu : aligné avec ce qu'il est ?",
            'options' => [
                'Oui',
                'Non',
                'Je ne sais pas',
            ],
            'notes' => "Cas de la salle 3. Même déroulé. Après : lire Philippiens 4,8 en entier, la grille de Paul. « Paul ne dit pas pensez à ce qui vous plaît, il donne une liste. Certains viennent de découvrir que ce qu'ils ont aimé était joli, pas beau. Ce n'est pas grave, c'est le but. »",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ce matin, sur ton téléphone, la troisième vidéo que tu as vue. Pas la première, la troisième. Tu t'en souviens ?",
            'options' => [
                'Oui, je sais exactement laquelle',
                'Vaguement, je vois le genre',
                'Aucune idée',
                "Je n'ai pas regardé de vidéo ce matin",
            ],
            'notes' => "Bloc 5, le monde aplati, 4 min. Insister : « pas à voix haute, dans le sondage ». Afficher.\n\n« Regardée il y a moins de trois heures, et disparue le matin même. » Brainrot, mot de l'année Oxford 2024, je ne suis pas au-dessus [TON VÉCU]. Le supermarché sous le même néon : le monde aplati. Quand tout est aplati, l'œil ne voit plus l'alignement. Objection « c'est juste un outil » : un algorithme a une direction. Qui tient le volant ?",
        ],
        [
            'type' => 'words',
            'prompt' => "D'après ces cinq vidéos, l'algo pense que ton cœur désire… Un mot.",
            'notes' => "Bloc 6, le miroir, 11 min. D'abord lire Matthieu 6,21-22 en entier. Puis trois minutes seul : les cinq premières vidéos de son feed. Ils répondent à la fin des trois minutes.\n\nAfficher et LAISSER À L'ÉCRAN pendant tout le binôme (quatre minutes, trois questions : choisi / jamais choisi / à garder). Au retour : nommer ce qui est gros sans juger, nommer ce qui est petit et qui devrait être gros. Annoncer le document pour la fin.",
        ],
        [
            'type' => 'text',
            'prompt' => "Ce que je pourrais poster cette semaine qui soit beau. Pas joli, beau.",
            'notes' => "Bloc 7, contribuer, à la fin du binôme « qu'est-ce que tu pourrais poster ». Une minute. Ne pas tout afficher : lire deux ou trois réponses à voix haute.\n\nAvant ce sondage, dans le bloc : Jean 2 le temple, consacrer = rendre à un lieu son logos, Charleston, les espaces [TON VÉCU], Exode 31,1-5 Betsaleel, les trois pièges, Jean 10,11 le beau berger, Ésaïe 53,2 la croix, l'abbé du mont Athos = le visage de droite.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ma décision pour cette semaine, une seule.",
            'options' => [
                'Je retire quelque chose de mon feed',
                'Je consacre un espace ou un moment',
                'Je partage quelque chose de beau',
            ],
            'notes' => "Bloc 8, atterrissage. Après le retour sur l'article et les deux questions à emporter. Une minute. Afficher et laisser à l'écran pendant que le QR du formulaire apparaît. Puis Psaume 27,4 lu en entier, et la prière.",
        ],
    ],
];
