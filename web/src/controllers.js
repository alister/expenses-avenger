angular.module('ExpensesApp')
    .controller('ListController', ['$scope', 'Expense', 'WeeklySpend', '$location', '$filter', '$routeParams', 'options', 
        function($scope, Expense, WeeklySpend, $location, $filter, $routeParams, ngCurrency, options) {  //, 

            $scope.fields = ['created_at', 'amount', 'description', 'comment'];
            $scope.fieldNames = ['Date', 'Amount', 'Description', 'Comment'];
            $scope.sort = function (field) {
                $scope.sort.field = field;
                $scope.sort.order = !$scope.sort.order;
                console.log('sort by '+field);
            };
            $scope.sort.field = 'created_at';
            $scope.sort.order = true;   // DESC, recent first

            $scope.show = function (id) {
                $location.url('/expense/' + id);
            };
            $scope.loadAll = function() {
                console.log('Load all.');
                Expense.query({}, function(expenses) {
                    $scope.expenses = expenses;
                });
            };
            $scope.loadAll();
    }])
    .controller('SummaryController', ['$scope', 'WeeklySpend', '$location',
        function ($scope, WeeklySpend, $location) {
            $scope.weeklySummary = function() {
                console.log('call weeklySummary');
                WeeklySpend.query({}, function(data) {
                    $scope.weeklySpend = data;
                }, function() {
                    console.log('Fetch of weeklySpend failed.');
                });
            }
            //$scope.weeklySummary();
            WeeklySpend.query({}, function(data) {
                $scope.weeklySpend = data;
            });

    }])
    .controller('NewController', ['$scope', '$rootScope', 'Expense', '$location',
        function ($scope, $rootScope, Expense, $location) {
            //$rootScope.PAGE = "new";
        console.log(109);
            $scope.expense = new Expense();
            $scope.save = function () {
                if ($scope.newExpense.$invalid) {
                    $scope.$broadcast('record:invalid');
                } else {
                    $scope.expense.$save();
                    $location.url('/expenses');
                }
            };
        }])
    .controller('SingleController', function ($scope, $rootScope, $location, Expense, $routeParams) {
        $rootScope.PAGE = "single";
        console.log($routeParams.id);
        Expense.get({ id: parseInt($routeParams.id, 10) }, function(data) {
            $scope.expense = data;
            console.log(data);
            $scope.expense.amount = parseFloat(data.amount).toFixed(2);
            //$scope.expense.created_at = new Date($scope.expense.created_at);
            console.log($scope.expense);
        });

        $scope.update = function() {
            $scope.expense.$update();
            $location.url('/expenses');
        };
        $scope.delete = function () {
            $scope.expense.$delete();
            $location.url('/expenses');
        };
    })
    //.controller('FilterDateController', function ($scope, $rootScope) {
    .controller('FilterDateController', ['$filter', '$location', function($filter, $location) {
    }]);
;
