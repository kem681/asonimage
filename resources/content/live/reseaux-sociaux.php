<?php

/*
|--------------------------------------------------------------------------
| Modele de sondage : atelier « Reseaux sociaux : comment gerer pour grandir ? »
|--------------------------------------------------------------------------
| Les sept questions de l'atelier, dans l'ordre de P:\ASI\ateliers\reseaux-sociaux\slido.md.
| Types : choice (choix unique), words (nuage de mots), text (texte libre).
| notes : ce que l'animateur voit dans la vue presentateur (quand lancer, quoi dire). Jamais montre a la salle.
*/

return [
    'title' => 'Réseaux sociaux : comment gérer pour grandir ?',
    'questions' => [
        [
            'type' => 'words',
            'prompt' => "Aujourd'hui, à ton avis, qui fixe les critères de beauté ? Un mot.",
            'notes' => "Bloc 1, 6 min. Lancer juste après avoir lu le titre du journal et raconté Omoggle, avant de dire que le titre est trompeur. Une minute.\n\nEn affichant : « Regardez ce qui est gros. Personne n'a écrit \"nous\". » Puis la démonstration : l'IA n'invente rien, elle fait la moyenne de nos regards. Le nuage est la preuve, la salle vient de répondre comme le journal.",
        ],
        [
            'type' => 'text',
            'prompt' => "Un truc beau que tu as vu cette semaine. Pas sur un écran.",
            'notes' => "Bloc 2, début. Remplace le tour de salle au tableau. Deux minutes.\n\nLire trois ou quatre réponses à voix haute, volontairement très différentes (un paysage, un geste, une personne, un plat). Puis : « Est-ce que tout le monde ici est d'accord que c'est beau ? » et enchaîner sur S3.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Le beau, c'est…",
            'options' => [
                'Subjectif, chacun le sien',
                'Une norme partagée par une époque',
                'Quelque chose qui existe indépendamment de nous',
            ],
            'notes' => "Bloc 2, juste après S2. Une minute. A va gagner, c'est voulu.\n\nEn affichant : « La majorité a répondu A. C'est la réponse polie, et je vais essayer de vous montrer pourquoi elle ne tient pas. » Puis les musées, la perte de confiance, la solitude, et : si c'était chacun sa beauté, Omoggle ne pourrait pas exister. Ceux qui ont répondu B ont vu le problème, ceux qui ont répondu C ont la réponse de la Bible.",
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
            'notes' => "Bloc 3, au début. Une minute. Insister : « pas à voix haute, dans le sondage ».\n\nEn affichant : « La plupart d'entre vous l'ont regardée il y a moins de trois heures, et elle a disparu. Pas oubliée au bout d'une semaine, disparue le matin même. » Puis nommer le brainrot, puis Babel.",
        ],
        [
            'type' => 'words',
            'prompt' => "D'après ces cinq vidéos, l'algo pense que ton cœur désire… Un mot.",
            'notes' => "Bloc 4, à la fin des trois minutes seul (chacun regarde les cinq premières vidéos de son feed). Le moment le plus fort de l'atelier.\n\nAfficher le nuage tout de suite et le laisser à l'écran pendant tout le binôme (quatre minutes). Au retour : nommer ce qui est gros sans juger, nommer ce qui est petit et qui devrait être gros. Annoncer le document pour réinitialiser son algorithme, pour la fin.",
        ],
        [
            'type' => 'text',
            'prompt' => "Ce que je pourrais poster cette semaine qui soit beau. Pas joli, beau.",
            'notes' => "Bloc 5, à la fin du binôme « qu'est-ce que tu pourrais poster cette semaine ». Une minute.\n\nNe pas tout afficher : lire deux ou trois réponses à voix haute. Les réponses restent dans l'export CSV pour le message de suivi.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ma décision pour cette semaine, une seule.",
            'options' => [
                'Je retire quelque chose de mon feed',
                'Je consacre un espace ou un moment',
                'Je partage quelque chose de beau',
            ],
            'notes' => "Bloc 6, après les deux questions à emporter. Une minute.\n\nAfficher le résultat et le laisser à l'écran pendant que le QR du formulaire apparaît. La décision reste privée dans le détail, mais tout le monde voit que la salle entière a choisi quelque chose. Puis le formulaire (adresse mail contre le document) et la prière sur le psaume 27.",
        ],
    ],
];
