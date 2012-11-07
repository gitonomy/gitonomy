Feature: Manage informations
    As a user
    In order to customize my experience on Gitonomy
    I should be able to configure things in my profile

    Scenario: I can change my preferences
        Given I am connected as "foobar"
          And I am on "/profile"
         Then I should see "User profile"

         When I fill:
            | Fullname | My new name |
          And I click on "Save informations"
         Then I should see "Your informations have been changed"

