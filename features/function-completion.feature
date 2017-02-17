Feature: Function Completion
    As a user
    I want to have all functions when using functions' return value in argument list

    Scenario: Getting all global functions with prefix
        Given there is a file with:
        """
        <?php

        function padawan_test_1(){}
        function padawan_test_2(){}
        function n_padawan_test_3(){}
        function padawan_other(){}
        """
        When I type "$a = new DateTime(padawan_test" on the 7 line
        And I ask for completion
        Then I should get:
            | Menu |
            | padawan_test_1 |
            | padawan_test_2 |

    Scenario: Getting core functions with prefix
        Given there is a file with:
        """
        <?php

        function array_pop_custom(){}
        """
        When I type "$a = new DateTime(array_pop" on the 4 line
        And I ask for completion
        Then I should get:
            | Menu |
            | array_pop_custom |
            | array_pop |

    Scenario: Getting function completion in return statement
        Given there is a file with:
        """
        <?php

        """
        When I type "return array_p" on the 2 line
        And I ask for completion
        Then I should get:
            | Menu          |
            | array_push    |
            | array_pop     |
            | array_pad     |
            | array_product |

    Scenario: Getting function completion in parentheses
        Given there is a file with:
        """
        <?php

        """
        When I type "(array_p" on the 2 line
        And I ask for completion
        Then I should get:
            | Menu          |
            | array_push    |
            | array_pop     |
            | array_pad     |
            | array_product |
