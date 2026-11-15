<?php

return [
    'eyebrow' => 'Mentions légales',
    'title' => 'Politique de <span class="accent">confidentialité</span>',
    'lead' => 'Ce que deviennent les informations que vous partagez sur ce site.',
    'updated' => 'Dernière mise à jour : :date',
    'sections' => [
        [
            'heading' => 'Responsable du site',
            'body' => ['Ce site est édité par :name (:email). Pour toute question sur vos données, écrivez à cette adresse.'],
        ],
        [
            'heading' => 'Données collectées par le formulaire de contact',
            'body' => ['Lorsque vous envoyez un message, les informations suivantes sont enregistrées afin que je puisse le lire et y répondre :'],
            'list' => [
                'votre nom et votre adresse e-mail',
                "l'objet, le budget et le message que vous choisissez d'envoyer",
                'la langue de la page utilisée',
                'votre adresse IP et le type de navigateur, uniquement pour bloquer le spam et les abus',
            ],
        ],
        [
            'heading' => 'Pourquoi ces données sont utilisées',
            'body' => ["Votre message sert uniquement à vous répondre et à échanger sur votre demande. Il n'est jamais vendu, jamais utilisé à des fins publicitaires et jamais ajouté à une liste de diffusion sans votre accord."],
        ],
        [
            'heading' => 'Durée de conservation',
            'body' => ["Les messages sont conservés le temps nécessaire au traitement de votre demande, puis supprimés au plus tard 24 mois après le dernier échange. Vous pouvez demander leur suppression plus tôt à tout moment."],
        ],
        [
            'heading' => 'Qui peut y accéder',
            'body' => ["Moi seul. Les messages sont stockés sur le serveur qui héberge ce site et transmis à ma boîte e-mail par un prestataire d'envoi. Ces prestataires traitent les données pour mon compte et uniquement pour fournir ce service."],
        ],
        [
            'heading' => 'Cookies et stockage local',
            'body' => ["Ce site utilise uniquement ce dont il a besoin pour fonctionner :"],
            'list' => [
                'un cookie de session et un jeton de sécurité (XSRF-TOKEN) qui protègent le formulaire contre les requêtes falsifiées',
                'votre thème (sombre ou clair) et votre langue, enregistrés dans votre propre navigateur (stockage local)',
            ],
            'after' => "Il n'y a aucun cookie publicitaire ou de suivi. Si des statistiques de visite sont activées, elles utilisent un outil respectueux de la vie privée (Plausible) qui n'utilise pas de cookies et ne collecte pas de données personnelles.",
        ],
        [
            'heading' => 'Vos droits',
            'body' => ["Vous pouvez demander à consulter, corriger ou supprimer les informations envoyées, ou vous opposer à leur utilisation, en écrivant à :email. Ces droits sont garantis par la loi marocaine 09-08 et, pour les visiteurs de l'Union européenne, par le RGPD."],
        ],
    ],
];
