Feature: Manage language of the application

    Scenario: As an administrator, I can configure default application locale
        Given I am on "/"
         Then I should see "Remember me"

        Given I am connected as "admin"
          And I am on "/admin/config"

         When I fill:
            | Language | French |
          And I click on "Save configuration"

         Then I should see "Configuration du projet"

         When I fill:
            | Langue | English |
          And I click on "Sauvegarder"

         Then I should see "Project configuration"

    Scenario: As a user, I can change my personal language
        Given user "alice" has locale "en_US"
        Given I am connected as "alice"
          And I am on "/profile"
         Then I should see "User profile"
         When I fill:
            | Language | French |
         And I click on "Save information"
        Then I should see "Profil utilisateur"
        When I fill:
            | Langue | English |
         And I click on "Sauver ces informations"
        Then I should see "User profile"
