angular.module('ExpensesApp')
    .controller('ListController', ['$scope', 'Expense', '$location', '$filter', '$routeParams', 'options', 
        function($scope, Expense, $location, $filter, $routeParams, ngCurrency, options) {  //, 

            $scope.fields = ['created_at', 'amount', 'description', 'comment'];
            $scope.fieldNames = ['Date', 'Amount', 'Description', 'Comment'];
            $scope.sort = function (field) {
                $scope.sort.field = field;
                $scope.sort.order = !$scope.sort.order;
                console.log('sort by '+field);
            };
            $scope.sort.field = 'created_at';
            $scope.sort.order = false;

            $scope.show = function (id) {
                $location.url('/expense/' + id);
            };
            $scope.loadAll = function() {
                Expense.query({}, function(expenses) {
                    $scope.expenses = expenses;
                });
            };
            $scope.loadAll();
    }])

    .controller('Never2Controller', ['$scope', 'Expense', '$location', '$filter', '$routeParams', 'options', 
        function($scope, Expense, $location, $filter, $routeParams, options) {  //, 
            console.log('Running ListController');
            //$scope.paramsObj= {};

            if (! $scope.expenses) {
                console.log('call loadAll, 13');
            }

            $scope.list = function() {
                console.log('fetch data from API, params:');
                console.log($scope.paramsObj);
                Expense.query($scope.paramsObj, function(data) {
                    console.log('Got Data:');
                    console.log(data);
                    $scope.expenses = data;
                }, function() {
                    console.log('Fetch fail. params:');
                    console.log($scope.paramsObj);
                });
            }
            // console.log('call getItems, params: ');
            // console.log($scope.paramsObj);
            //$scope.list();

            // filtering date page, sets $scope.paramsObj
            $scope.activeDate;
            $scope.selectedDates = [new Date().setHours(0, 0, 0, 0)];
            $scope.type = 'range';
            $scope.identity = angular.identity;
            $scope.filterByTimestamps = function() {
                var startDate = $filter('orderBy')($scope.selectedDates, '', false)[0];
                var endDate   = $filter('orderBy')($scope.selectedDates, '', true)[0];
                $scope.paramsObj = {};
                $scope.paramsObj.startDate = moment(startDate);//.format('YYYY-MM-DD');
                $scope.paramsObj.endDate = moment(endDate);//.format('YYYY-MM-DD');

                // put the search filter into the URL, for the record
                console.log('Set path with start/endDate');
                console.log($scope.paramsObj);

                console.log('fetch the filtered data from API, params:');
                console.log($scope.paramsObj);
                $scope.list($scope.paramsObj);

                $location.path('/expenses').search('startDate', $scope.paramsObj.startDate)
                    .search('endDate', $scope.paramsObj.endDate)
                ;
            }
    }])
    .controller('NeverController', ['$scope', 'Expense', '$location', '$filter', '$routeParams', 'options', 
        function($scope, Expense, $location, $filter, $routeParams, options) {  //, 
            var paramsObj = {};
            if ($routeParams.startDate) {
                console.log('update paramsObj.startDate');
                $scope.paramsObj.startDate = $routeParams.startDate;
            }
            if ($routeParams.endDate) {
                console.log('update paramsObj.endDate');
                $scope.paramsObj.endDate = $routeParams.endDate;
            }


    

            //$scope.expenses = Expense.query($scope.paramsObj);
    
            // // $scope.getAll = function() {
            // //     Expense.query($scope.paramsObj, function($data){
            // //         $scope.expenses = data;
            // //     });
            // // };

    
    
            // $scope.removeFromSelected = function(dt) {
            //     this.selectedDates.splice(this.selectedDates.indexOf(dt), 1);
            // }
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
