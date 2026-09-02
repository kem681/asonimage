<?php

/*
|--------------------------------------------------------------------------
| Contenu du module 3x30 (modele Jean-Baptiste)
|--------------------------------------------------------------------------
|
| Tout le texte du modele vit ici, separe du code : les trois axes, les
| 24 affirmations du diagnostic, l'echelle de reponse, les exemples de
| gestes et les textes des ecrans. L'equipe peut corriger ce fichier sans
| toucher a la logique.
|
| STATUT : premiere version redigee a partir des notes de la reunion
| d'equipe. A VALIDER par l'equipe (Marc, Jonathan, Flo) avant le premier
| atelier. Chaque affirmation est formulee pour qu'un accord fort revele un
| manquement sur l'axe concerne.
|
| Vocabulaire proscrit dans toute l'interface : systeme, gagner, devenir,
| command center, streak, score, performance.
|
*/

return [

    'axes' => [

        'filiation' => [
            'label' => 'Filiation',
            'title' => "S'inscrire dans une histoire",
            'summary' => "Jean-Baptiste ne sort pas de nulle part : il naît d'une lignée, d'une promesse, d'un père rendu muet. Il reçoit un héritage, il en brise une part, il en poursuit une autre.",
            'body' => [
                "Personne ne se fait tout seul. Avant même de choisir quoi que ce soit, tu as reçu : un nom, une langue, des gestes, des blessures, une manière de te tenir. Jean-Baptiste hérite d'un père prêtre, d'une mère stérile qui reçoit un enfant, d'une parole prononcée sur lui avant sa naissance.",
                "S'inscrire dans une histoire, ce n'est pas tout accepter. C'est regarder ce qu'on a reçu, nommer ce qui doit être brisé (les mauvais héritages, ce qu'on répète sans l'avoir choisi), et couvrir ce qui doit l'être. Couvrir la nudité de son père, ce n'est pas la nier, c'est contenir pour éviter que ça se répande.",
                "Et parce que le masculin est lié au souvenir, la filiation demande du concret : un mémorial, quelque chose que tes enfants pourront toucher pour se souvenir. Ce que tu poses aujourd'hui est ce qu'ils recevront.",
            ],
            'question' => "Qu'est-ce que je reçois, qu'est-ce que je brise, qu'est-ce que je transmets ?",
            'gestures' => [
                "Chaque soir, une phrase : ce que j'ai reçu aujourd'hui, et de qui.",
                'Chaque matin, un verset lu à voix haute avant de regarder mon téléphone.',
                'Une fois par semaine, un appel à mon père, à un frère ou à un aîné.',
            ],
        ],

        'desert' => [
            'label' => 'Désert',
            'title' => 'Le dépouillement',
            'summary' => "Avant de parler, Jean-Baptiste se tait. Des années au désert, seul, sans structure, sans public. C'est là que l'orgueil tombe, parce qu'il n'y a personne pour le nourrir.",
            'body' => [
                "Le désert n'est pas une punition, c'est un passage. L'isolement, la solitude, le doute, l'absence de cadre : tout ce que nous fuyons par les écrans et l'agitation est exactement ce que Jean-Baptiste traverse avant d'être prêt.",
                "Le désert désamorce l'orgueil. Là où personne ne te regarde, tu découvres ce que tu fais pour être vu. Là où rien ne te distrait, tu découvres ce que tu fuis. « Il faut qu'il croisse et que je diminue » : cette phrase ne se dit pas depuis une estrade, elle se prépare dans le silence.",
                "Se dépouiller, ce n'est pas se détruire. C'est enlever ce qui n'est pas à toi pour garder l'essentiel, comme un vêtement de poils de chameau : rude, simple, suffisant.",
            ],
            'question' => "Qu'est-ce que je fuis, et qu'est-ce que je garde quand tout le reste tombe ?",
            'gestures' => [
                'Cinq minutes seul, sans écran, une fois par jour.',
                "Chaque soir, une phrase : une chose que je n'ai pas contrôlée aujourd'hui.",
                'Le téléphone dort hors de la chambre.',
            ],
        ],

        'appel' => [
            'label' => "Réponse à l'appel",
            'title' => 'Répondre',
            'summary' => "Jean-Baptiste ne s'est pas nommé prophète. Il a répondu. Il a pointé vers un autre que lui, et il a tenu sa place jusqu'au bout, y compris quand ça lui a coûté.",
            'body' => [
                "Tu sais ce que tu veux, mais tu ne sais pas toujours ce dont tu as besoin. L'appel n'est pas un projet personnel, c'est une réponse à quelqu'un. Il passe par le discernement (nommer le combat que je mène), par une responsabilité concrète (m'inscrire dans quelque chose de plus grand que moi), et par la soumission à Christ (pointer vers lui, pas vers moi).",
                "Élisée demande une double portion de l'esprit d'Élie : il ne se l'attribue pas, il la reçoit d'un autre, en restant près de lui. Josué reçoit son autorité de Moïse. Répondre à l'appel, c'est accepter d'être placé, et de recevoir avant de donner.",
                "C'est aussi un vrai combat, et il est beau. Le gars qui se bat contre la luxure, l'orgueil ou la paresse en s'abandonnant à Christ est plus solide que celui qui se bat pour son image. Prends le Christ à la lettre : c'est là que la vie devient une quête pour laquelle tu es armé.",
            ],
            'question' => "Quelle est ma croisade aujourd'hui, et devant qui je me place pour la mener ?",
            'gestures' => [
                "Chaque matin, une phrase : à quoi Dieu m'appelle aujourd'hui, là où je suis.",
                'Une responsabilité précise, petite, tenue chaque semaine dans mon église.',
                "Une décision prise le matin, dite à quelqu'un avant midi.",
            ],
        ],

    ],

    'scale' => [
        1 => 'Pas du tout',
        2 => 'Un peu',
        3 => 'Assez',
        4 => 'Tout à fait',
    ],

    // 24 affirmations, 8 par axe. Un accord fort ("tout a fait") revele un
    // manquement sur l'axe. L'ordre d'affichage melange les axes.
    'statements' => [
        ['axis' => 'filiation', 'text' => 'Je ne sais pas vraiment de qui je tiens ce que je suis.'],
        ['axis' => 'desert', 'text' => "Rester seul en silence, sans écran, m'est presque insupportable."],
        ['axis' => 'appel', 'text' => "Je sais ce que je veux, mais je ne sais pas ce dont j'ai besoin."],
        ['axis' => 'filiation', 'text' => "Je répète des schémas de mon père (ou de ceux qui m'ont élevé) sans l'avoir choisi."],
        ['axis' => 'desert', 'text' => 'Je remplis chaque temps vide (transport, attente, soirée) avec mon téléphone.'],
        ['axis' => 'appel', 'text' => "Je repousse les décisions qui m'engageraient vraiment."],
        ['axis' => 'filiation', 'text' => 'Quand je pense à mon héritage familial, je ressens surtout de la colère ou de la honte.'],
        ['axis' => 'desert', 'text' => "Quand une chose m'échappe, je préfère la contrôler que la lâcher."],
        ['axis' => 'appel', 'text' => 'Personne ne peut compter sur moi pour une responsabilité précise dans mon église ou ma communauté.'],
        ['axis' => 'filiation', 'text' => "Je préfère ne pas parler de ce que mes parents m'ont transmis."],
        ['axis' => 'desert', 'text' => "J'ai du mal à reconnaître devant quelqu'un que je me suis trompé."],
        ['axis' => 'appel', 'text' => "Je n'ai jamais nommé clairement le combat spirituel que je mène en ce moment."],
        ['axis' => 'filiation', 'text' => "Je ne me vois pas comme le maillon d'une histoire plus grande que moi."],
        ['axis' => 'desert', 'text' => 'Je fuis les périodes de doute au lieu de les traverser.'],
        ['axis' => 'appel', 'text' => "Quand Dieu me demande quelque chose de concret, je négocie plutôt que d'obéir."],
        ['axis' => 'filiation', 'text' => "Je n'ai aucun repère concret (lieu, objet, rituel) qui me rappelle d'où je viens."],
        ['axis' => 'desert', 'text' => 'Je supporte mal de ne pas être vu ou reconnu pour ce que je fais.'],
        ['axis' => 'appel', 'text' => 'Je vis ma foi surtout pour moi, sans que ça engage les autres autour de moi.'],
        ['axis' => 'filiation', 'text' => 'Je ne saurais pas dire ce que je veux transmettre à ceux qui viennent après moi.'],
        ['axis' => 'desert', 'text' => "Je n'ai aucun temps fixe dans ma semaine réservé à Dieu seul."],
        ['axis' => 'appel', 'text' => 'Je ne saurais pas dire vers quoi, ou vers qui, ma vie pointe.'],
        ['axis' => 'filiation', 'text' => "Je porte seul des choses de mon histoire que je n'ai jamais dites à personne."],
        ['axis' => 'desert', 'text' => 'Diminuer pour laisser la place à un autre me semble une défaite.'],
        ['axis' => 'appel', 'text' => "Je n'ai personne (aîné, mentor) devant qui je me place pour recevoir ce que je dois apprendre."],
    ],

    'texts' => [
        'diagnostic_intro' => "Vingt-quatre affirmations, trois à quatre minutes. Réponds à chaud, sans chercher la bonne réponse : il n'y en a pas. Le résultat ne te donne pas une note, il te dit où ça tire le plus aujourd'hui.",
        'result_intro' => "Voici où tu te situes sur les trois axes. Plus la barre est haute, plus le manquement est marqué. Un axe est mis en avant : c'est ton axe phare, celui par lequel commencer. Les deux autres restent à lire, pas à travailler tout de suite.",
        'anchor_intro' => 'Un seul geste. Minuscule. Tenable même un mauvais jour. Pas un plan sur un an tenu trois jours : un acte qui ne peut pas échouer pour cause de fatigue.',
        'anchor_confidant' => "Écris un prénom réel. La carte ne se remplit pas seul dans son coin : ce geste, quelqu'un doit le savoir.",
        'checkin_missed' => "Un jour raté n'est pas une rupture. Les compassions de Dieu se renouvellent chaque matin. On reprend.",
        'friction_intro' => "Chaque semaine, une phrase : où la résistance s'est-elle manifestée ? L'orgueil précis, la fuite précise. Puis à qui tu l'as dit. Pas un rapport, un aveu à un frère.",
        'review_intro' => 'Quatre semaines ont passé. Cette revue ne mesure rien, elle reconnaît ce qui a été tenu, et par qui. Regarde en arrière, pas en avant.',
        'memorial_intro' => 'Chaque revue laisse une trace datée. Comme les pierres dressées en Israël : pour que toi, et ceux qui viennent après toi, se souviennent de ce qui a été donné.',
        'group_intro' => "Un petit groupe, ce sont des gars qui savent où tu en es. Pas de fil de discussion ici : vous vous parlez en vrai, l'outil se contente de rappeler quand ça fait trop longtemps.",
    ],

];
