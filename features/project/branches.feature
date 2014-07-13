Feature: Branches

    Scenario: Can see branches
     Given I am connected as "alice"
      When I am on "/projects/foobar/branches"
      Then I should see "Add element_100"

    Scenario: I can delete branch
    Given I run in project "foobar" as "lead":
        """
        git push origin master:to-delete
        """
      And I am connected as "lead"
     When I am on "/projects/foobar/branches"
     Then I should see "to-delete"
     When I click on button with tooltip "Delete branch to-delete"
      And I click on "Are you sure?"
     Then I should see "Branch to-delete deleted"
     When I refresh
     Then I should not see "to-delete"

    Scenario: I can't delete branch if I'm not allowed
    Given I am connected as "visitor"
     When I am on "/projects/foobar/branches"
     Then I should not see a button with tooltip "Delete branch"
