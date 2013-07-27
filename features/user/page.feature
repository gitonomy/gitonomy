Feature: User page

    Scenario: Anonymous can't see user page
        Given I am on "/user/alice"
         Then I should see "Login"

    Scenario Outline: User can see a collatorator is working on project
      Given I am connected as "<from>"
        And I am on "/user/<to>"
       Then I should see "<project>"

      Scenarios:
        | from    | to    | project |
        | alice   | alice | Foobar  |
        | alice   | alice | Barbaz  |
        | alice   | alice | Empty   |
        | bob     | alice | Foobar  |
        | alice   | bob   | Foobar  |

    Scenario Outline: User cannot see private projects
      Given I am connected as "<from>"
       And I am on "/user/<to>"
      Then I should not see "<project>"

      Scenarios:
        | from    | to    | project |
        | alice   | bob   | Barbaz  |
        | bob     | alice | Barbaz  |
