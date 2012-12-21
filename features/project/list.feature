Feature: Project list

    Scenario: Alice has limited access to project
        Given I am connected as "alice"
          And I am on "/"
         Then I should see "Foobar"
          And I should see "Empty"
          And I should see "Barbaz"
          And I should not see "Secret"

    Scenario: Admin can see all project
        Given I am connected as "admin"
          And I am on "/"
         Then I should see "Foobar"
          And I should see "Empty"
          And I should see "Barbaz"
          And I should see "Secret"

    Scenario: Bob only has one project
        Given I am connected as "bob"
          And I am on "/"
         Then I should see "Foobar"
          And I should not see "Empty"
          And I should not see "Barbaz"
          And I should not see "Secret"
