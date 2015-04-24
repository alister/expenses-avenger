angular.module('ExpensesApp')
    .factory('Expense', function ($resource) {
        return $resource('http://nas.abulman.co.uk:8000/app_dev.php/api/v1/expenses/:id.json', { id: '@id' }, {
            'update':  { method: 'PUT', isArray: true },
            'get':     { method: 'GET' },
            'options': {/*method: 'GET'*/ }
        });
    })
;
