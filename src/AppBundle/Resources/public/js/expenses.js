/* expenses.js */
var expensesApp = angular.module('expensesApp', [ 'ngRoute', 'ngResource', 'expenseApp.services' ]);

expensesApp.config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/expenses', {
        url:'/api/v1/expenses',
        templateUrl:'partials/expenses.html',
        controller:'ExpensesListController'
    })
    .when('/expenses/view', {    // view one
       //#url:'/api/v1/expenses/:id/view',
       templateUrl:'partials/expense_view.html',
       controller:'ExpenseViewController'
    })
    .when('/expenses/new', {     // create new
        url:'/api/v1/expenses/new',
        templateUrl:'partials/expense_add.html',
        controller:'ExpenseCreateController'
    })
    .when('/expenses/edit', {
        url:'/api/v1/expenses/:id/edit',
        templateUrl:'partials/expense_edit.html',
        controller:'ExpenseEditController'
    })
    .when('/about', {
        templateUrl: '/partials/about.html',
    })
    .when('/alister', {
        templateUrl: '/partials/alister.html',
    }).
    // go to /expenses by default
    otherwise({ redirectTo: '/expenses' });
}])

.controller('ExpensesListController',function($scope, popupService, $window, Expense) {
    $scope.expenses = Expense.query();

    $scope.deleteExpense = function(expense) {
        if (popupService.showPopup('Really delete this?')) {
            expense.$delete(function() {
                $window.location.href='';
            });
        }
    };
    $scope.editExpense = function(id) {
        console.log('in editExpense');
        $location.path('/expenses/edit' + id);
    };
})
.controller('ExpenseViewController',function($scope, $stateParams, Expense) {
    $scope.expense = Expense.get({id:$stateParams.id});
})
.controller('ExpenseCreateController',function($scope, $state, $stateParams, Expense) {
    $scope.expense = new Expense();
    $scope.addExpense=function() {
        $scope.expense.$save(function() {
            redirectTo: '/expenses';
        });
    }
})
.controller('ExpenseEditController',function($scope, $state, $stateParams, Expense) {
    $scope.updateExpense=function() {
        $scope.expense.$update(function() {
            redirectTo: '/expenses';
        });
    };
    $scope.loadExpense=function() {
        $scope.expense=Expense.get({id:$stateParams.id});
    };
    $scope.loadExpense();
});
