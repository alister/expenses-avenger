# When entered, each expense has: date, time, description, amount, comment
Feature: each expense has all the required fields
    In order to know the API is returns the correct data
    As a developer
    I need to check each expense has the appropriate fields

    Scenario Outline: The test API returns sensibly
       Given These expense lines exist
        | description      | amount | comment       | createdAt                 |
        | "sample expense" | 9.99   | "just a test" | 2015-04-17T14:28:00+01:00 |
        #When I see all the expenseLines
        When  I call the API route "get_expenseline" for record 1
        Then the response should be json
         And the response should have a field <fieldname>
    Examples:
        | fieldname   |
        | description |
        | amount      |
        | comment     |
        | createdAt   |
