Feature: Project administration
    As a user
    I can administrate a project
    So I easily manage information of my project

    Scenario: I need to have sufficient credentials to administrate project
        Given I am connected as "alice"
          And I am on "/projects/secret/admin"
         Then I should not see "Delete Secret"

    Scenario: User can delete his project
        Given project "todelete" exists
          And user "alice" is "Lead developer" on "todelete"
          And I am connected as "alice"
         When I am on "/projects/todelete/admin"
          And I click on "xpath=//a[contains(@data-confirm, ""Yes, I want to delete Todelete"")]"
         Then I should see "Yes, I want to delete Todelete"
         When I click on "Yes, I want to delete Todelete"
         Then I should see "Project removed"
