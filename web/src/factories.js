angular.module('ExpensesApp')
    .factory('Expense', function ($resource) {
        return $resource('http://nas.abulman.co.uk:8000/app_dev.php/api/v1/expenses/:id.json', { id: '@id' }, {
            'update':  { method: 'PUT', isArray: true },
            'get':     { method: 'GET' },
            'options': {/*method: 'GET'*/ }
        });
    })
    // .factory('Fields', function ($q, $http, Expense) {
    //     var //url = '/options/displayed_fields',
    //         ignore = ['created_at', 'amount', 'description', 'comment', 'id', 'userId'],
    //         allFields = [],
    //         deferred = $q.defer(),

    //         expenses = Expense.query(function () {
    //             expenses.forEach(function (c) {
    //                 Object.keys(c).forEach(function (k) {
    //                     if (allFields.indexOf(k) < 0 && ignore.indexOf(k) < 0) allFields.push(k);
    //                 });
    //             });
    //             deferred.resolve(allFields);
    //         });

    //     return {
    //         get: function () {
    //             return ['created_at', 'amount', 'description', 'comment'];
    //         },
    //         set: function (newFields) {
    //             return '';
    //             //return $http.post(url, { fields: newFields });
    //         },
    //         headers: function () {
    //             return deferred.promise;
    //         }
    //     };
    // })
;
