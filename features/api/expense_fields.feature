# When entered, each expense has: date, time, description, amount, comment
Feature: each expense has all the required fields
    In order to know the API is returns the correct data
    As a developer
    I need to check each expense has the appropriate fields

    Background:
       Given These expense lines exist
        | description      | amount | comment       | createdAt  |
        | "sample expense" | 9.99   | "just a test" | 2015-04-17 |
        And There are the following users
        | username | password | email          | role     |
        | fred     | password | xx@example.com | ROLE_API |

    Scenario Outline: The test API returns sensibly
        When I call the API route "get_expense" for record 1
        Then the response should be json
         And the response should have a field <fieldname>
    Examples:
        | fieldname   |
        | created_at  |
        | amount      |
        | comment     |
        | description |

         #And I see the response
         #And I see all the expenses
