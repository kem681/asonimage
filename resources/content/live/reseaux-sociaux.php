<?php

/*
|--------------------------------------------------------------------------
| Modele de sondage : atelier « Reseaux sociaux : comment gerer pour grandir ? »
|--------------------------------------------------------------------------
| Les treize questions de l'atelier (v3 du 19.09.2026), dans l'ordre de
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
            'notes' => "Bloc 1, l'article, 3 min. Lancer juste après avoir lu le titre du journal et décrit Omoggle. Une minute.\n\nEn affichant : « Regardez ce qui est gros. » Puis le titre est trop simple : l'IA n'a pas découvert une définition objective de la beauté, elle apprend à partir d'images et de préférences humaines, elle reprend et amplifie les standards de notre culture. Elle ne crée pas le problème, elle nous le renvoie. Lire Romains 12,2 en entier. La question de l'atelier : comment on apprend à regarder ?",
        ],
        [
            'type' => 'choice',
            'prompt' => "Lequel attirerait probablement le plus de likes ?",
            'options' => [
                'Celui de gauche',
                'Celui de droite',
            ],
            'notes' => "Cas 1, temps 1. Les deux photos côte à côte : gauche le portrait lissé, droite le visage âgé qui rit. Ne rien dire. SURTOUT PAS « lequel est beau ». Une minute, afficher, puis Q3 tout de suite.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Lequel avez-vous envie de regarder plus longtemps ?",
            'options' => [
                'Celui de gauche',
                'Celui de droite',
            ],
            'notes' => "Cas 1, temps 2. Une minute, afficher à côté de Q2. Demander : « Est-ce que c'est la même réponse ? Pourquoi ? » Puis Q4.",
        ],
        [
            'type' => 'words',
            'prompt' => "Pourquoi, en un mot ?",
            'notes' => "Cas 1, temps 3. Une minute, afficher et LAISSER À L'ÉCRAN. Ne pas conclure. Lire les gros mots. « Ce qui attire le clic et ce qui retient le regard, ce n'est pas la même chose. Le premier correspond à un standard, le second n'en a pas et pourtant vous restez. »\n\nPuis 1 Samuel 16,7 en entier, et ce que le texte NE dit PAS : le corps compte, l'apparence ne suffit pas. « Le cœur est le centre, mais le corps peut en devenir le langage. »",
        ],
        [
            'type' => 'words',
            'prompt' => "Qu'est-ce que la deuxième version change dans votre manière d'écouter ? Un mot.",
            'notes' => "Cas 2. La louange de l'église brute (20 s), puis la même au format brainrot. Ne pas dire d'où vient la vidéo avant le nuage. Une minute, afficher.\n\n« Vous avez reconnu ? C'est nous, dimanche passé. » Lire les mots (distraction, rythme, humour, excitation, perte du sens). « Personne n'a dit que le chant était différent. Vous avez dit que vous l'écoutiez différemment. Le contenu est-il identique si la forme change notre manière de le recevoir ? » Ecclésiaste 3,11 en entier.\n\nENSUITE LE RAMASSAGE (bloc 3, 7 min, sans sondage) : joli / beau nuancé, la phrase centrale, Jean 1,1-5 et 14, le Logos (intelligibilité, puis le Christ), tov, le corps compte, Jean 20,20 et 27, la question de discernement.",
        ],
        [
            'type' => 'choice',
            'prompt' => "À quel moment passe-t-on de « mettre en valeur » à « remplacer » ?",
            'options' => [
                'Entre la première et la deuxième photo',
                'Entre la deuxième et la troisième',
                'Jamais, tout est légitime',
                'Dès qu\'on retouche',
            ],
            'notes' => "Bloc 4, l'ornement, 4 min. Trois versions du même portrait sur le beamer : brute, lumière et cadrage avec légère retouche, lourdement transformée. Une minute, afficher.\n\nLaisser le désaccord s'exprimer, deux ou trois avis. Le débat est le but, pas la frontière. « Orner n'est pas forcément mentir » : cadre, parfum, vêtement, maquillage, mise en scène, le tabernacle. « L'ornement devient problématique quand il ne sert plus la réalité mais cherche à la remplacer. Est-ce que la forme révèle, ou est-ce qu'elle remplace ? »",
        ],
        [
            'type' => 'text',
            'prompt' => "Un contenu vu sur ton feed cette semaine que tu as trouvé beau. Décris-le en une ligne.",
            'notes' => "Bloc 5, un cas de la salle, 3 min. Une minute. Afficher, lire, en choisir UN ou deux qui se prêtent au test. Puis Q8.",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ce contenu : il me fait rester fixé dessus, ou rebondir vers quelque chose de plus grand ?",
            'options' => [
                'Rester fixé dessus',
                'Rebondir vers plus grand',
                'Je ne sais pas',
            ],
            'notes' => "Bloc 5. Lire la description à voix haute avant d'ouvrir. Afficher. Demander à celui qui l'a décrit ce qu'il en pense, sans trancher à sa place. Pour un deuxième cas : « Effacer » puis rouvrir.\n\nPuis Philippiens 4,8 en entier : Paul donne un contenu (vrai, honorable, juste, pur), c'est le « plus grand » ; et « digne d'être aimé », le beau est dans la liste.",
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
            'notes' => "Bloc 6, le monde aplati, 3 min. Insister : « pas à voix haute, dans le sondage ». Afficher.\n\n« Regardée il y a moins de trois heures, disparue le matin même. » Brainrot, je ne suis pas au-dessus [TON VÉCU]. Le feed ne classe pas selon l'importance mais selon la capacité à retenir l'attention. Le monde aplati. « Un algorithme est optimisé vers quelque chose. La vraie question : vers quoi ? » Jamais « les réseaux c'est mal ».",
        ],
        [
            'type' => 'words',
            'prompt' => "Si quelqu'un ne me connaissait que par ces cinq contenus, qu'est-ce qu'il penserait important pour moi ? Un mot.",
            'notes' => "Bloc 7, le miroir, 8 min. D'abord Matthieu 6,21-22 en entier : le feed est une carte de tes regards, pas un jugement sur ton cœur. Trois minutes seul : les cinq premières vidéos de son feed. Ils répondent à la fin.\n\nAfficher et LAISSER À L'ÉCRAN pendant le binôme (trois minutes : choisi / jamais choisi / à garder). Au retour : nommer ce qui est gros sans juger, ce qui est petit et qui pourrait être plus grand. Annoncer le document pour la fin.",
        ],
        [
            'type' => 'text',
            'prompt' => "Notre post : le sujet, et ce qu'il rend visible. Deux lignes.",
            'notes' => "Bloc 9, le laboratoire, 5 min. Groupes de trois, un sujet banal par groupe (un café, un match de foot, le trajet en bus, un repas de famille, une paire de chaussures usées, quelqu'un qui travaille, une scène de film). Consigne : « un post qui ne se contente pas d'attirer le regard, mais qui révèle quelque chose de vrai ». Trois minutes, ils répondent à la fin.\n\nAfficher, lire trois ou quatre réponses à voix haute, nommer ce qui rebondit.\n\nAvant ce bloc, dans consacrer et contribuer : Jean 2, consacrer = rendre à un lieu ce pour quoi il est fait, les espaces [TON VÉCU], Exode 31,1-5, la liste de ce qu'un chrétien peut publier (« il y a des traces du Logos partout »), Jean 10,11 kalos et le don de soi, Ésaïe 53,2, « prends soin de la forme, mais que la forme serve quelque chose de plus grand qu'elle ».",
        ],
        [
            'type' => 'choice',
            'prompt' => "Ma décision pour cette semaine, une seule.",
            'options' => [
                'Je retire quelque chose de mon feed',
                'Je consacre un espace ou un moment',
                'Je partage quelque chose de beau',
            ],
            'notes' => "Bloc 10, atterrissage, après les trois questions à emporter (la troisième : « qu'est-ce que je peux ajouter au monde au lieu de demander au monde de me regarder ? »). Une minute, afficher. Puis Q13.",
        ],
        [
            'type' => 'text',
            'prompt' => "Cette semaine, je veux rendre visible…",
            'notes' => "Bloc 10, la dernière phrase à compléter. Une minute. Afficher et laisser à l'écran pendant que le QR du formulaire apparaît. Puis Psaume 27,4 lu en entier, et la prière.",
        ],
    ],
];
