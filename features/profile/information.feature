Feature: Edit profile information
    As a user
    I can configure things in my profile
    In order to customize my experience on Gitonomy

    Scenario: I can change my preferences
        Given I am connected as "alice"
          And I am on "/profile"
         Then I should see "User profile"

         When I fill:
            | Fullname | My new name |
          And I click on "Save information"
         Then I should see "Your information have been changed"
