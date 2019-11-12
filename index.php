
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pagination | Scrolling</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<style type="text/css" media="screen">
		.lds-dual-ring {
			  display: inline-block;
			  width: 64px;
			  height: 64px;
			}
			.lds-dual-ring:after {
			  content: " ";
			  display: block;
			  width: 46px;
			  height: 46px;
			  margin: 1px;
			  border-radius: 50%;
			  border: 5px solid #fff;
			  border-color: #fff transparent #fff transparent;
			  animation: lds-dual-ring 1.2s linear infinite;
			}
			@keyframes lds-dual-ring {
			  0% {
			    transform: rotate(0deg);
			  }
			  100% {
			    transform: rotate(360deg);
			  }
			}
	
	</style>
</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <a class="navbar-brand" href="javascript:void(0)">Scrolling</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav mr-auto">
	    </ul>
	    <form class="form-inline my-2 my-lg-0 searchForm" method="POST">
	      <input class="form-control mr-sm-2 searchValue" type="search" placeholder="Search" aria-label="Search" name="search">
	      <button class="btn btn-outline-success my-2 my-sm-0" style="margin-right:4px;" type="submit" id="searchBtn">Search</button>
	      <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" id="resetBtn">Reset</button>
	    </form>
	  </div>
	</nav>
	<div class="container">
		<div class="row">
			<div class="lds-dual-ring loader" style="display: none;"></div>
			<div class="col-md-12 m-2" style="overflow:scroll;">
				<div class="table-responsive border">
				  <table class="table table-striped">
				    <thead>
				      <tr>
				        <th scope="col">User Id</th>
				        <th scope="col">User name</th>
				        <th scope="col">First Name</th>
				        <th scope="col">Last Name</th>
				      </tr>
				    </thead>
				    <tbody class="showData">

				    </tbody>
				  </table>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	var limit = 20;
	var offset = 0;
	$(document).ready(function() {
		doAjax("POST");

		$("#searchBtn").click(function(e) {
			let searchValue = $(".searchValue").val();
			doAjax("POST", searchValue, 'searchData', limit, offset);
			e.preventDefault();
		});

		$("#resetBtn").click(function(e) {
			$("#searchForm").reset();
		});
	});

	$(document).scroll(function() {	    
	    if($(document).scrollTop() >= $(document).height() - $(window).height()){
	    	let nxtOffset = (offset == 0)?parseInt(limit+offset+1):parseInt(offset+limit);
	    	offset = nxtOffset;
	    	let searchValue = $(".searchValue").val();
	    	if(searchValue) {
	    		doAjax("POST", searchValue, 'searchData', limit, nxtOffset);
	    	} else {
	    		doAjax("POST",'', 'searchData', limit, nxtOffset);
	    	}

		}
	});

	function doAjax(type, data = '', method = '', limit, offset) {

	    $.ajax({
	  		url: "process.php",
	  		type: type,
	  		dataType: "JSON",
	  		beforeSend:function(){
	  			$(".loader").show();
	  		},
	  		data: { data : data, method : method, limit : limit, offset : offset },
	  		success: function(result){
	  			$(".loader").hide();
	  			if((result != '') && isJSON(result)) {
	  				if(result.details.length>0) {
	  					let res = result.details;
		  				let dynamicHtml = createHtml(res);
		  				if(offset == 0) {
		  					$(".showData").html(dynamicHtml);
		  				} else {
		  					$(".showData").append(dynamicHtml);
		  				}
	  				} else {
	  					if(offset == 0) {
		  					$(".showData").html('<th colspan="4" class="text-center">No record found</th>');
		  				} else {
		  					$(".showData").append('<th colspan="4" class="text-center">No record found</th>');
		  				}
	  				}
	  			}
	  		},
	  		error: function(error) {
	  			$(".loader").hide();
	  			alert("Something went wrong");
	  		}
	  	});
	}

	function isJSON (str) {
	    if (typeof str != 'string')
	        str = JSON.stringify(str);
	    try {
	        JSON.parse(str);
	        return true;
	    } catch (e) {
	        return false;
	    }
	}

	function createHtml(row) {
		var html = '';

		$.each(row, function(index, value) {
			html += '<tr>';
				html += '<th scope="row">'+value['user_id']+'</th>';
				html += '<td>'+value['username']+'</td>';
				html += '<td>'+value['first_name']+'</td>';
				html += '<td>'+value['last_name']+'</td>';
			html += '</tr>';
	    });
	  	return html;
	}
</script>
</body>
</html>