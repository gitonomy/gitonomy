Feature: Project list

    Scenario: Administrator looks at project list
        Given I am connected as "admin"
          And I am on "/"
         Then I should see "Foobar"
          And I should see "Empty"
          And I should see "Barbaz"
          And I should see "Secret"

    Scenario: User has limited access to projects
        Given I am connected as "alice"
          And I am on "/"
         Then I should see "Foobar"
          And I should see "Empty"
          And I should see "Barbaz"
          And I should not see "Secret"

         When I am on "/projects/secret"
         Then I should not see "Secret"

    Scenario: User has few accesses to projects
        Given I am connected as "bob"
          And I am on "/"
         Then I should see "Foobar"
          And I should not see "Empty"
          And I should not see "Barbaz"
          And I should not see "Secret"
