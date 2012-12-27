Feature: A user can create a new project
    As a user
    I can create a new project on my own
    So I don't borrow administrator when I want to push some code

    Scenario: A user can create a new project

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
