angular.module('ShoutboxApp', ['ngSanitize', 'angularFileUpload']);

var ShoutboxController = ['$scope', '$upload', function($scope, $upload) {

    var needsRefresh = function(callback) {
        if ($scope.messages && $scope.messages.length > 0) {
            $.get('index.php/last', function(data) {
                callback(data.id != $scope.messages[0].id);
            });
        }
        else {
            callback(true);
        }
    };

    var refresh = function() {
        $.get('index.php/list', function(data) {
            $scope.$apply(function() {
                $scope.messages = _.map(data, function(item) {
                    console.log(item);
                    return {
                        id: item.id,
                        text: item.text,
                        image: item.image,
                        time: moment(item.time).calendar()
                    }
                });
            });
        });
    };

    $scope.addMessage = function() {
        var data = {
            text: $scope.messageText 
        };

        $.post('index.php/post', data, function(html) {
            $scope.$apply(function() {
                refresh();
                $scope.messageText = '';
            });                
        });        
    };

    $scope.onFileSelect = function(files) {
        console.log('on file select');

        for (var i = 0; i < files.length; i++) {
            var file = files[0];

            $scope.upload = $upload.upload({
                url: 'index.php/upload',
                method: 'POST',
                file: file,
            }).progress(function(evt) {
                console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
            }).success(function(data, status, headers, config) {
                console.log('upload success');
                console.log(data);
            });
        }
    };

    refresh();

    setInterval(function() {
        needsRefresh(function(result) {
            if (result) {
                refresh();
            }
        });
    }, 1000);
}];