Feature: A user can delete a project
    As a user
    I have possibility to delete repositories
    So I can remove old or obsolete projects

    Background:
        Given locale is "en_US"

    Scenario:
        Given project "test-delete" exists
          And user "alice" is "Lead developer" on "test-delete"
          And I am connected as "alice"

         When I am on "/projects/test-delete/admin"
         Then I should see "Yes, I want to delete test-delete"

         When I click on "Yes, I want to delete test-delete"
         Then I should see "Project removed"
