# Scénario de la Phase 1 : Création des types de projets et des champs dynamiques par l'administrateur

1.**Accès au panneau d'administration :**
    L'un des administrateurs du département IT, appelons-le **Alex**, se connecte à l'application. Son tableau de bord n'affiche pas encore de projets à créer, mais une section dédiée à l'administration du système.

2.**Création d'un nouveau type de projet :**
    Alex navigue vers le menu `Administration` puis sélectionne `Types de Projets`. Il voit une liste des types de projets existants (s'il y en a) et un bouton `Ajouter un nouveau type de projet`. Il clique sur ce bouton.

3.**Définition des informations de base du type de projet :**
    Un formulaire simple s'affiche. Alex saisit les informations de base pour ce nouveau type de projet :
    ***Nom**: "Projet d'Assistance Humanitaire"
    ***Description**: "Ce modèle est destiné à la création de projets d'aide d'urgence et de développement communautaire."

4.**Ajout des champs dynamiques (`DynamicProjectField`) :**
    Une fois le type de projet enregistré, Alex peut commencer à définir les champs spécifiques qui apparaîtront pour ce type de projet. C'est ici que le modèle `DynamicProjectField` entre en jeu.

    ***Champ 1 (Texte) :**Alex ajoute un premier champ. Il donne un **libellé**: "Région d'Intervention". Il choisit le **type de champ**"Texte" et le marque comme **requis**.
    ***Champ 2 (Sélection) :**Alex ajoute un second champ. Il le nomme "Type d'Urgence" et choisit le **type de champ**"Sélection". Il entre ensuite les différentes **options**possibles, par exemple : "Catastrophe Naturelle", "Conflit Armé", "Crise Sanitaire".
    ***Champ 3 (Date) :**Un troisième champ est ajouté pour collecter une information spécifique, par exemple "Date de Début Prévue". Le **type de champ**est défini sur "Date".
    ***Champ 4 (Zone de texte) :**Un dernier champ pour des informations plus longues, avec un **libellé**"Population Cible" et un **type**"Zone de texte".

5.**Finalisation :**
    Alex valide et enregistre la configuration du type de projet. Il peut revenir à tout moment pour modifier ou ajouter d'autres champs dynamiques.

Le résultat final est un modèle de formulaire personnalisé et flexible. Lorsque, dans la phase suivante, un membre de l'ONG voudra créer un "Projet d'Assistance Humanitaire", le formulaire affichera automatiquement les champs "Région d'Intervention", "Type d'Urgence", "Date de Début Prévue" et "Population Cible", tels que définis par Alex.

---


D'accord, maintenant que la première phase de configuration des types de projets et de leurs champs dynamiques par le département IT est en place, nous pouvons passer aux étapes suivantes de votre scénario.

---

###**Phase 2 : Création et Soumission du Projet par un Membre de l'ONG**📝

Cette phase est celle où un membre de l'ONG (non-admin) initie concrètement un projet en utilisant les modèles que l'IT a définis.

1.**Accès au Formulaire de Création de Projet :**
    *Un membre de l'ONG, par exemple **Marie**, se connecte à l'application.
    *Elle navigue vers une section `Créer un Projet`.
    *L'application lui présente une liste des **types de projets**disponibles (tirés de la table `project_types`). Marie sélectionne, par exemple, le "Projet d'Urgence Humanitaire".

2.**Affichage du Formulaire Dynamique :**
    *Une fois le type de projet sélectionné, l'application utilise les définitions des champs de la table `dynamic_project_fields` (liés à ce `project_type_id`) pour construire dynamiquement le formulaire.
    *Marie voit les champs comme "Quelle est la zone géographique exacte de l'intervention ?", "Quel est le type de catastrophe/crise ?", "Nombre estimé de bénéficiaires directs ?", etc., tels que configurés par l'administrateur IT.

3.**Saisie des Informations du Projet :**
    *Marie remplit le formulaire avec les détails spécifiques à son projet d'urgence (ex: "Zone : Nord du Bénin", "Type de crise : Inondation", "Bénéficiaires : 5000").
    *Les informations saisies sont collectées et formatées. Les valeurs des champs dynamiques seront stockées dans les `target_project_field` correspondants de la table `projects` (par exemple, dans les champs `description` ou `general_objectives`, en utilisant les `delimiter_start` et `delimiter_end` pour les identifier).

4.**Enregistrement Initial du Projet :**
    *Lorsque Marie soumet le formulaire, une nouvelle entrée est créée dans la table `projects`.
    *Le `creator_user_id` est renseigné avec l'ID de Marie.
    *Le `project_type_id` est lié au type de projet qu'elle a choisi.
    *Le `status` du projet est initialisé à "Brouillon" ou "En attente de validation".
    *Des entrées initiales peuvent également être créées dans d'autres tables liées au projet, comme :
        *`project_contexts` (pour le contexte général du projet).
        *`project_documents` (si Marie télécharge des documents initiaux).
        *`logical_frameworks` (pour les objectifs généraux du projet).
        *`specific_objectives` (pour les objectifs spécifiques liés au cadre logique).
        *`results` (pour les résultats attendus).
        *`activities` (pour les activités initiales, même si les responsables et dates précises peuvent être affinés plus tard).

5.**Soumission pour Validation :**
    *Une fois le projet créé, Marie le "soumet" pour validation. Cela peut changer son statut à "En attente de validation" et déclencher une notification aux superviseurs ou au comité de validation.

---

###**Phase 3 : Validation, Interaction et Suivi du Projet**🔄

Cette phase implique la collaboration des membres de l'ONG et le suivi de l'avancement du projet.

1.**Revue et Validation du Projet :**
    *Les superviseurs ou membres du comité de validation (qui ont les rôles appropriés dans la table `roles` et sont des `users`) accèdent à une liste des projets "En attente de validation".
    *Ils examinent les détails du projet créé par Marie.
    *Ils peuvent décider de :
        ***Valider le projet :**Le `status` du projet dans la table `projects` passe à "Actif". C'est à ce moment que le budget et la durée finale peuvent être définis.
        ***Demander des modifications :**Le `status` reste "En attente de validation" ou passe à "Modifications requises", avec des commentaires ajoutés. Marie est notifiée pour apporter les ajustements.
        ***Refuser le projet :**Le `status` passe à "Refusé".

2.**Planification Détaillée (si validé) :**
    *Si le projet est validé et passe au statut "Actif", les équipes financières ou les gestionnaires de projet peuvent commencer à définir le budget détaillé.
    *Des entrées sont créées dans la table `budgets` pour les lignes budgétaires principales du projet.
    *Ces budgets peuvent ensuite être ventilés par trimestre dans la table `quarterly_budgets`.
    *Les `activities` peuvent être affinées, des `responsible_user_id` (liés à la table `users`) sont assignés, et les `start_date` et `end_date` sont précisées.
    *Des `resources` (humaines, matérielles, financières) sont allouées aux `activities`.

3.**Suivi de la Progression :**
    *Tout au long de l'exécution du projet, les responsables d'activités ou les superviseurs mettent à jour l'avancement.
    *La table `progress_trackers` est utilisée pour enregistrer :
        *La `date` du suivi.
        *Le `progress_percentage` de l'activité ou du projet.
        *Un `status_update` (commentaire).
        *Une `justification` en cas de retard.
        *L'utilisateur (`updated_by_user_id` de la table `users`) qui a effectué la mise à jour.
    *Des `qualitative_evaluations` peuvent être réalisées pour évaluer la qualité des activités ou du projet.

4.**Clôture du Projet :**
    *Une fois toutes les activités terminées et les objectifs atteints, le `status` du projet dans la table `projects` passe à "Terminé".

Ce scénario montre comment les tables `project_types`, `dynamic_project_fields`, `projects`, `logical_frameworks`, `specific_objectives`, `results`, `activities`, `budgets`, `quarterly_budgets`, `progress_trackers`, et `qualitative_evaluations` s'articulent pour gérer le cycle de vie complet d'un projet au sein de l'ONG.



Artisan Commande: 
    `php artisan make:migration update_file_type_length_in_project_documents --table=project_documents`