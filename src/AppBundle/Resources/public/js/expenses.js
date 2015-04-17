/* expenses.js */

var expensesApp = angular.module('expensesApp', [
  'ngRoute',
  //'expensesControllers'
]);
expensesApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/expenses', {
        templateUrl: 'partials/demo.html',
        controller: 'ExpensesCtrl',
        controllerAs: 'expenses'
      }).
      // when('/expenses/:phoneId', {
      //   templateUrl: 'partials/phone-detail.html',
      //   controller: 'PhoneDetailCtrl'
      // }).

      // go to /expenses by default
      otherwise({
        redirectTo: '/expenses'
      });
  }]);

expensesApp.controller('ExpensesCtrl', ['$scope', '$http', function ($scope, $http) {

  $http.get('/api/v1/demo/expenses.json').success(function(data) {
    $scope.phones = data;
  });

  $scope.orderProp = 'age';
}]);
 
  //   todoList.addTodo = function() {
  //     todoList.todos.push({text:todoList.todoText, done:false});
  //     todoList.todoText = '';
  //   };
 
  //   todoList.remaining = function() {
  //     var count = 0;
  //     angular.forEach(todoList.todos, function(todo) {
  //       count += todo.done ? 0 : 1;
  //     });
  //     return count;
  //   };
