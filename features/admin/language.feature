Feature: Manage language of the application

    Background:
        Given locale is "en_US"

    Scenario: As an administrator, I can configure default application locale
        Given I am on "/"
         Then I should see "Remember me"

        Given I am connected as "admin"
          And I am on "/admin/config"

         When I fill:
            | Language | French |
          And I click on "Save configuration"
          And I logout

         Then I should see "Se souvenir de moi"

    Scenario: As a user, I can change my personal language
        Given user "alice" has locale "en_US"
        Given I am connected as "alice"
          And I am on "/profile"
         Then I should see "User profile"
         When I fill:
            | Language | French |
         And I click on "Save informations"
        Then I should see "Profil utilisateur"
