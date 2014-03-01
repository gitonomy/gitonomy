Feature: Project creation
    As a user
    I can create new projects
    So I easily push my code

    Scenario: A user creates a new project
        Given project "test" does not exist
          And I am connected as "alice"
          And I am on "/"

         When I click on "Create a new project"
          And I fill:
            | Name of project | test |
            | Slug of project | test |
            | Default branch | master |
          And I click on "Create"
         Then I should see "Project created"

         When I am on "/projects/test"
         Then I should see "How to make your first commit"
          And user "alice" is "Lead developer" on "test"

         When I click on "Permissions"
         Then I should see "Git accesses"
          And I should see 1 "xpath=//table[contains(@class, 'git-accesses')]/tbody/tr/td[contains(., 'Lead developer')]"
          And I should see 1 "xpath=//table[contains(@class, 'git-accesses')]/tbody/tr/td[contains(., 'Developer')]"
          And I should see 1 "xpath=//table[contains(@class, 'git-accesses')]/tbody/tr/td[contains(., 'Visitor')]"

    Scenario: A user creates a new project with a directory separator in slug
        Given project "foo/bar" does not exist
        And I am connected as "alice"
        And I am on "/"

         When I click on "Create a new project"
          And I fill:
            | Name of project | foo/bar |
            | Slug of project | foo/bar |
            | Default branch | master |
          And I click on "Create"
         Then I should see "Project created"

         When I am on "/projects/foo/bar"
         Then I should see "How to make your first commit"
          And user "alice" is "Lead developer" on "foo/bar"

         When I click on "Permissions"
         Then I should see "Git accesses"
          And I should see 1 "xpath=//table[contains(@class, 'git-accesses')]/tbody/tr/td[contains(., 'Lead developer')]"
          And I should see 1 "xpath=//table[contains(@class, 'git-accesses')]/tbody/tr/td[contains(., 'Developer')]"
          And I should see 1 "xpath=//table[contains(@class, 'git-accesses')]/tbody/tr/td[contains(., 'Visitor')]"

    Scenario: An anonymous cannot create a project
        Given I am on "/create-project"
         Then I should see "Login"

    Scenario: A user cannot create a project with a name already used
        Given I am connected as "alice"
         When I am on "/create-project"
          And I fill:
            | Name of project | foobar |
            | Slug of project | foobar |
          And I click on "Create"
         Then I should see "This value is already used."

    Scenario: A user cannot create a project with invalid slug (directory separator at first)
        Given I am connected as "alice"
         When I am on "/create-project"
          And I fill:
            | Name of project | toto |
            | Slug of project | /toto |
          And I click on "Create"
         Then I should see "This value is not valid."

    Scenario: A user cannot create a project with invalid slug (directory separator at end)
        Given I am connected as "alice"
         When I am on "/create-project"
          And I fill:
            | Name of project | toto |
            | Slug of project | toto/ |
          And I click on "Create"
         Then I should see "This value is not valid."

    Scenario: A user cannot create a project with invalid slug (directory separator doubled)
        Given I am connected as "alice"
         When I am on "/create-project"
          And I fill:
            | Name of project | toto |
            | Slug of project | to//to |
          And I click on "Create"
         Then I should see "This value is not valid."
