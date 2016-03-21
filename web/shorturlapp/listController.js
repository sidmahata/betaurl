(function(){

    var app = angular.module("shorturlApp");
    
    function getRootUrl() {
        return window.location.origin?window.location.origin+'/':window.location.protocol+'/'+window.location.host+'/';
        
    }
    
    var listController = function($scope, $http, $location, $q, $anchorScroll){
		
		// loading
		$scope.loading=0;
		
		// date choose function
		$scope.today = function() {
		    $scope.dtFrom = new Date();
	  	};
	    $scope.today();
	    
	    $scope.toggleMin = function() {
		    $scope.minDate = $scope.minDate ? null : new Date();
	  	};
	  	
	  	
	  	var currentDate = new Date();
		var firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
		var lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
	  	
	  	$scope.dtFrom = firstDay;
	  	console.log(($scope.dtFrom).toISOString().slice(0,10));
	  	$scope.dtTo = lastDay;
		console.log($scope.dtTo);
	  	
	  	$scope.minDate = new Date("2015-03-25");
	  	$scope.maxDate = lastDay;
	  	
	  	$scope.open1 = function() {
		    $scope.popup1.opened = true;
	  	};
	  	$scope.popup1 = {
		    opened: false
	  	};
	  	
	  	$scope.open2 = function() {
		    $scope.popup2.opened = true;
	  	};
	  	$scope.popup2 = {
		    opened: false
	  	};
	  	
	  	$scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
  		$scope.format = $scope.formats[0];
	  	$scope.altInputFormats = $scope.format;
	  	
	  	$scope.dateOptions = {
		    formatYear: 'yy',
		    startingDay: 1
	  	};
		// end of date choose function
		
		
		// show chart using button
		$("#show-chart-btn").click(function(){
		    $("#chartdiv").toggle('fast');
		    // $(".list-table").animate({'margin-top': "10vh"});
		    $(".list-table").toggleClass("list-table-with-chart");
		}); 
		
        var onUserLoadComplete = function(response){
        	$scope.loading=0;
		  	console.log('loading stop: ' + $scope.loading);
            $scope.shorturlList = response.data;
            $scope.error = null;
       //     $scope.clicks = [];
       //     angular.forEach($scope.shorturlList, function(item){
       //     	$scope.loading++;
       //       	$http.get( getRootUrl()+"app_dev.php/analytics?shortcode="+item.shortcode+"&period=range&date="+($scope.dtFrom).toISOString().slice(0,10)+","+($scope.dtTo).toISOString().slice(0,10) )
			    // .then(function(clickdata) {
			    // 	$scope.loading--;
			    	
			    //     $scope.clicks.push(clickdata.data);
			    //     $scope.number++;
			    //      console.log($scope.clicks);
			    // });
       //     });
            
        };
        
        

        var onError = function(response){
            $scope.error = "Could not fetch data";
        };
		
		// get url list
        $http.get(getRootUrl().concat("app_dev.php/api/urls?dateFrom="+($scope.dtFrom).toISOString().slice(0,10)+"&dateTo="+($scope.dtTo).toISOString().slice(0,10)))
                .then(onUserLoadComplete, onError);
        
        // get url list function 
        $scope.list= function(){
        	$http.get(getRootUrl().concat("app_dev.php/api/urls?dateFrom="+($scope.dtFrom).toISOString().slice(0,10)+"&dateTo="+($scope.dtTo).toISOString().slice(0,10)))
                .then(onUserLoadComplete, onError);	
        };
        
        // $scope.message1 = "hello world , List is working";
        $scope.rooturl = getRootUrl();
        // $scope.analyticsmonth = "FEB";
        
        // pagination
		
		  //$scope.setPage = function (pageNo) {
		  //  $scope.bigCurrentPage = pageNo;
		  //};
		
		// get urllist on page change in pagination with offset
		  $scope.pageChanged = function() {
		  	$scope.loading++;
		  	console.log('loading start: ' + $scope.loading);
		    console.log('Page changed to: ' + $scope.currentPage);
		    $http.get(getRootUrl().concat('app_dev.php/api/urls?offset=').concat($scope.currentPage*$scope.itemsPerPage-$scope.itemsPerPage).concat("&dateFrom="+($scope.dtFrom).toISOString().slice(0,10)+"&dateTo="+($scope.dtTo).toISOString().slice(0,10)))
                .then(onUserLoadComplete, onError);
		  };
		  
		  //get urllist totalitems
		  $http.get(getRootUrl().concat("app_dev.php/api/url/urltotal"))
		    .then(function(response) {
		        $scope.totalItems = response.data;
		    });
		
		  $scope.maxSize = 5;
		  $scope.currentPage = 1;
		  $scope.itemsPerPage = 10;
        // end of pagination
        
       

		// analytics daily line chart data
		$scope.drawdailychart = function(shortcode){
			$("#chartdiv").show('fast');
			$(".list-table").addClass("list-table-with-chart");
			console.log(shortcode);
			$scope.loading++;
			$http.get( getRootUrl()+"app_dev.php/analytics?shortcode="+shortcode+"&period=day&date="+($scope.dtFrom).toISOString().slice(0,10)+","+($scope.dtTo).toISOString().slice(0,10) )
			    .then(function(response) {
			    	$scope.loading--;
			    	$scope.dailydata=response.data;
			        $scope.chartdata = [];
			        angular.forEach($scope.dailydata, function(value, key) {
			        	$scope.date = key;
			        	// console.log($scope.date);  
		        		angular.forEach(value, function(data) {
			            	$scope.nbvisits = data.nb_visits;
			            	// console.log(data.nb_visits);
				        });
			        	
				        $scope.chartdata.push({"date":key, "nb_visits": $scope.nbvisits});
				        $scope.nbvisits=0;
				        
			        });
			        
			        var chart = AmCharts.makeChart("chartdiv", {
		                type: "serial",
		                theme: "dark",
		                dataProvider: $scope.chartdata,
		                categoryField: "date",
		                dataDateFormat: "YYYY-MM-DD",
		                startDuration: 1,
		                // startEffect: "bounce",
		                // rotate: true,
		                
		                chartCursor: {
							"enabled": true
						},
						chartScrollbar: {
							"enabled": true
						},
						// titles: [{
						// 		"text": "Monthly data",
						// 		"size": 15
						// }],
		                categoryAxis: {
		                    gridPosition: "start",
		                    parseDates: true
		                },
		                valueAxes: [{
		                    position: "top",
		                    title: "No. of Visits",
		                    minorGridEnabled: true,
		                    integersOnly: true
		                }],
		                graphs: [{
		                	// bullet: "round",
		                	fillAlphas: 1,
		                    title: getRootUrl()+shortcode,
		                    valueField: "nb_visits",
		                    type: "column",
		                    // fillAlphas:1,
		                    // fillColors: "#ff851b",
		                    lineColor: rainbow(10, 3),
		                    // balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b></span>"
		                    balloonText: "<span style='font-size:13px;'><b>[[value]]</b></span>"
		                }],
		                legend: {
		                    useGraphSettings: true,
		                    position: "absolute",
		                    top: "15px",
		                    right: "15px"
		                },
		                creditsPosition:"top-right"
		
		            });
		            chart.addListener("clickGraphItem", chartDataClick);
		            
			    });
			
		};
		
		function chartDataClick(event)
		{
		    console.log("hello");
		    // alert(Object.keys(event.item) + ": " + event.item.values.value);
		    console.log(event.item.dataContext.date);
		}
		
		
		
		// analytics daily bar chart data
		$scope.comparisonshortcode=[];
		$scope.drawcomparisonchart = function(shortcode){
			console.log("added:"+shortcode);
			
			// push shortcode to comparison shortcode array if not already present
			if($.inArray(shortcode, $scope.comparisonshortcode)<0) {
			    $scope.comparisonshortcode.push(shortcode);
			}
			console.log("comparison:"+$scope.comparisonshortcode );
			
			$scope.chartdata = [];
			$scope.graphsData=[];
 			
 			// var deferred = $q.defer();
			angular.forEach($scope.comparisonshortcode, function(shortcode,i) {
				
				// charts graph data settings
				var graphDataObj = {};
			    graphDataObj["bullet"] = "round";
			    // graphDataObj["type"]= "smoothedLine";
			    graphDataObj["lineColor"]= rainbow(10, i);
			    graphDataObj["title"] = getRootUrl().concat(shortcode);
			    graphDataObj["valueField"]= getRootUrl().concat(shortcode);
		 		$scope.graphsData.push(graphDataObj);
				 
				// call analytics for each shortcode
				$http.get( getRootUrl()+"app_dev.php/analytics?shortcode="+shortcode+"&period=day&date="+($scope.dtFrom).toISOString().slice(0,10)+","+($scope.dtTo).toISOString().slice(0,10) )
			    .then(function(response) {
			    	console.log(response.data);
			    	angular.forEach(response.data, function(value, key) {
			    		// setting nb_visits to zero for if initial data is null
			    		$scope.nbvisits = 0;
			    		
			    		// setting shorturl to be inserted to chart data
			    		$scope.comparisonchartshorturl = getRootUrl().concat(shortcode);
			    		angular.forEach(value, function(data) {
			            	$scope.nbvisits = data.nb_visits;
			            	$scope.comparisonchartshorturl = data.url;
			            	console.log("nb_visits:"+$scope.nbvisits);
				        });
				        
				        // make obj for inserting to chartdata by using dynamic key value pair
				        var shorturldataobj = {};
				        shorturldataobj["date"] = key;
						shorturldataobj["shorturl"] = getRootUrl().concat(shortcode);
						shorturldataobj["nb_visits"] = $scope.nbvisits;
				        
				        // push the shorturldataobj to chartdata
				        $scope.chartdata.push(shorturldataobj);
				        $scope.nbvisits=0;
			    	});
			    	// console.log($scope.chartdata);

					// group chartdata datewise
			    	var groupedchartdata = {};
			    	angular.forEach($scope.chartdata, function(item){
			    		if(!groupedchartdata[item.date]){
			    			groupedchartdata[item.date]=[];
			    		}	
			    		
			    		var shorturldataobj={};
			    		shorturldataobj["date"] = item.date;
						shorturldataobj[item.shorturl] = item.nb_visits;
			    		groupedchartdata[item.date].push(shorturldataobj);
			    	});
			    	console.log(groupedchartdata);
			    	
			    	var groupedChartDataResult = [];
					for(var x in groupedchartdata) {
					    if(Object.prototype.hasOwnProperty.call(groupedchartdata, x)) {
					        var obj = {};
					        obj[x] = groupedchartdata[x];
					        groupedChartDataResult.push(obj);
					    }
					}
			    	console.log(groupedChartDataResult);
			    	$scope.groupedChartDataResultScope = groupedChartDataResult;
			    	
			    	// merging of all arrays inside groupedchartdata result datewise
			    	$scope.array1=[];
			    	angular.forEach(groupedChartDataResult, function(item){
			    		// console.log(key);
			    		angular.forEach(item, function(value, key){
			    			$scope.array1.push(value);	
			    		});
			    		
			    	});
			    	console.log($scope.array1);
			    	
			    	// merging of all objs inside array1
			    	$scope.array2=[];
			    	angular.forEach($scope.array1, function(item){
			    		// console.log(key);
			    		angular.forEach(item, function(value, key){
			    			$scope.array2.push(value);	
			    		});
			    		
			    	});
			    	console.log($scope.array2);
			    	
			    	// deferred.resolve();
	
			    });
			    
			    	
			});
			
			console.log($scope.graphsData);
			
			// $q.all($scope.chartdata).then(function(){
			//   console.log("data:fisnihed");
			   
			// })
			
			setTimeout(function () {
               	console.log('long-running operation inside loop done');
               	AmCharts.makeChart("chartdiv", {
		                type: "serial",
		                theme: "dark",
		                dataProvider: $scope.array2,
		                categoryField: "date",
		                dataDateFormat: "YYYY-MM-DD",
		                startDuration: 1,
		                startEffect: "easeOutSine",
		                // rotate: true,
		                
		                chartCursor: {
							"enabled": true
						},
						chartScrollbar: {
							"enabled": true
						},
						// titles: [{
						// 		"text": "Monthly data",
						// 		"size": 15
						// }],
		                categoryAxis: {
		                    gridPosition: "start",
		                    parseDates: true
		                },
		                valueAxes: [{
		                    position: "top",
		                    title: "No. of Visits",
		                    minorGridEnabled: true,
		                    integersOnly: true
		                }],
		                graphs: $scope.graphsData,
		                legend: {
		                    useGraphSettings: true,
		                    position: "absolute",
		                    top: "15px",
		                    right: "15px"
		                },
		                creditsPosition:"top-right"
		
		            });
    		}, 1500);
			
			
			
			
		};
		
		
		

		// rainbow hex code generator function
		function rainbow(numOfSteps, step) {
		    // This function generates vibrant, "evenly spaced" colours (i.e. no clustering). This is ideal for creating easily distiguishable vibrant markers in Google Maps and other apps.
		    // HSV to RBG adapted from: http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
		    // http://blog.adamcole.ca/2011/11/simple-javascript-rainbow-color.html
		    // Adam Cole, 2011-Sept-14
		    var r, g, b;
		    var h = step / numOfSteps;
		    var i = ~~(h * 6);
		    var f = h * 6 - i;
		    var q = 1 - f;
		    switch(i % 6){
		        case 0: r = 1, g = f, b = 0; break;
		        case 1: r = q, g = 1, b = 0; break;
		        case 2: r = 0, g = 1, b = f; break;
		        case 3: r = 0, g = q, b = 1; break;
		        case 4: r = f, g = 0, b = 1; break;
		        case 5: r = 1, g = 0, b = q; break;
		    }
		    var c = "#" + ("00" + (~ ~(r * 255)).toString(16)).slice(-2) + ("00" + (~ ~(g * 255)).toString(16)).slice(-2) + ("00" + (~ ~(b * 255)).toString(16)).slice(-2);
		    return (c);
		}
		// rainbow(10, 6);
		
		// end of rainbow hex code generator function
		

        // copy button
        $scope.copyToClipboard = function(text){
            
              // Create a "hidden" input
              var aux = document.createElement("input");
            
              // Assign it the value of the specified element
              aux.setAttribute("value", getRootUrl().concat(text));
            
              // Append it to the body
              document.body.appendChild(aux);
            
              // Highlight its content
              aux.select();
            
              // Copy the highlighted text
              document.execCommand("copy");
            
              // Remove it from the body
              document.body.removeChild(aux);
              
                
            }

		
    };

    app.controller("listController", ["$scope", "$http", "$location", "$q", "$anchorScroll", listController]);

}());