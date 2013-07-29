Feature: Change password

    Scenario: Change password form blocks mis-repeated
        Given I am connected as "alice"
          And I am on "/profile/password"
         When I fill:
          | Current password | alice |
          | Password         | new |
          | Confirm password | new2 |
         And I click on "Reset password"
        Then I should see "This value is not valid"

    Scenario: Change password form blocks invalid password
        Given I am connected as "alice"
          And I am on "/profile/password"
         When I fill:
          | Current password | wrong  |
          | Password         | bobino |
          | Confirm password | bobino |
         And I click on "Reset password"
        Then I should see "This value should be the user current password"

    Scenario: I can change my password
        Given I am connected as "alice"
          And I am on "/profile/password"
         When I fill:
          | Current password | alice  |
          | Password         | new |
          | Confirm password | new |
         And I click on "Reset password"
        Then I should see "Your new password was conscientiously saved!"
        When I logout
         And I am connected as "alice" with password "new"
          And I am on "/profile/password"
         When I fill:
          | Current password | new |
          | Password         | alice |
          | Confirm password | alice |
         And I click on "Reset password"
        Then I should see "Your new password was conscientiously saved!"
