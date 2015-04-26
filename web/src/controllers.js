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
                console.log('show()');
                $location.url('/expense/' + id);
            };
            $scope.loadAll = function() {
                //console.log('Load all.');
                Expense.query({}, function(expenses) {
                    $scope.expenses = expenses;
                });
            };
            $scope.loadAll();
            //console.log('ran through controller');
    }])
    .controller('SummaryController', ['$scope', 'WeeklySpend', 'Expense', '$location',
        //console.log('SummaryController');
        function ($scope, WeeklySpend, Expense, $location) {
            $scope.weeklySummary = function() {
                //console.log('call weeklySummary');
                WeeklySpend.query({}, function(data) {
                    $scope.weeklySpend = data;
                }, function() {
                    //console.log('Fetch of weeklySpend failed.');
                });
            }

            // I can't figure out how to get the results of the query displayed on the main page
            // without it being reset and all the records being fetched
            // $scope.filterSpendByDate = function(weekSummary) {
            //     console.log('filterSpendByDate()');
            //     console.log(weekSummary);
            //     console.log(weekSummary.weekStart);
            //     console.log(weekSummary.weekEnd);
            //     Expense.query({'startDate': weekSummary.weekStart, 'endDate': weekSummary.weekEnd}, function(expenses) {
            //         $scope.expenses = expenses;
            //         console.log('Expense.query()');
            //         console.log(expenses);
            //         $location.path('/expenses');
            //     });
            // };

            //$scope.weeklySummary();
            WeeklySpend.query({}, function(data) {
                $scope.weeklySpend = data;
            });
    }])
    .controller('NewController', ['$scope', '$rootScope', 'Expense', '$location',
        function ($scope, $rootScope, Expense, $location) {
        //$rootScope.PAGE = "new";
        //console.log('NewController');
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
        //$rootScope.PAGE = "single";
        //console.log($routeParams.id);
        Expense.get({ id: parseInt($routeParams.id, 10) }, function(data) {
            $scope.expense = data;
            //console.log(data);
            $scope.expense.amount = parseFloat(data.amount).toFixed(2);
            //$scope.expense.created_at = new Date($scope.expense.created_at);
            //console.log($scope.expense);
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
        //$rootScope.PAGE = "filter";
    }]);
;
