var gridoption;
var gridbus;	
var gridbranch;	
var gridbusresult;
var gridbranchresult;
var data = { info: "lf",
			 optLF: [100,10,0.001,1],
			 bus: [],
			 branch: []
		   };

 function readFile(evt) {
	//Retrieve the first (and only!) File from the FileList object
	var f = evt.target.files[0];
	if (f) {
		var r = new FileReader();
		r.onload = function(e) { 
			json = e.target.result;
			data = JSON.parse(json);
			gridoption.clearAll();
			gridoption.parse([data.optLF],"jsarray");
			gridbus.clearAll();
			gridbus.parse(data.bus,"jsarray");						
			gridbranch.clearAll();
			gridbranch.parse(data.branch,"jsarray");
			$("#iter").empty();
			$("#loss").empty();
			gridbusresult.clearAll();
			gridbranchresult.clearAll();
			getDataGrid();
			/*if (TogetherJS.running) {
				TogetherJS.send({
					type: 'loadFile',
					option: data.optLF,
					bus: data.bus,
					branch: data.branch
				});
			}*/
		}
		r.readAsText(f);
	} else { 
		alert("Failed to load file");
	}
 }

function createOptionGrid(name) {
	gridoption = new dhtmlXGridObject(name);  
    gridoption.setImagePath("codebase/imgs/");	
	gridoption.setHeader("Power Base (MVA),Max Iteration,Tolerance,Check Q limit");
	gridoption.setInitWidths("80,80,80,80");                          
	gridoption.setColAlign("right,right,right,right");
	gridoption.setSkin("dhx_skyblue"); 	
	gridoption.init();  
	gridoption.addRow(0,"100,10,0.001,1",gridoption.getRowsNum());	
}

function createBusGrid(name) {
	gridbus = new dhtmlXGridObject(name); 
    gridbus.setImagePath("codebase/imgs/");   
	gridbus.setHeader("Bus,Type,Pgen (MW),Qgen (MVAR),Pload (MW),Qload (MVAR),Rshunt (pu),Xshunt (pu),U (pu),Theta (degree),Qgmax (MVAR),Qgmin (MVAR)"); 
	gridbus.setInitWidths("50,50,100,100,100,100,100,100,100,100,100,100"); 
	gridbus.setColAlign("right,right,right,right,right,right,right,right,right,right,right,right");
	gridbus.setSkin("dhx_skyblue");
	gridbus.init(); 			
}

function createBranchGrid(name) {
	gridbranch = new dhtmlXGridObject(name);        
    gridbranch.setImagePath("codebase/imgs/");   	
	gridbranch.setHeader("From,To,Rser (pu),Xser (pu),Bpar (pu),Tap (pu),Phi (degrees), Status");
	gridbranch.setInitWidths("50,50,100,100,100,100,100,50");                          
	gridbranch.setColAlign("right,right,right,right,right,right,right,right,right");
	gridbranch.setSkin("dhx_skyblue");                                                  
	gridbranch.init();   
}

function createBusResult(name) {
	gridbusresult = new dhtmlXGridObject(name);     
	gridbusresult.setImagePath("codebase/imgs/");   
	gridbusresult.setHeader("Bus,U (pu),Theta (degree),Pgen (MW),Qgen (MVAR),Pload (MW),Qload (MVAR),Qgmax (MVAR),Qgmin (MVAR)"); 
	gridbusresult.setInitWidths("50,100,100,100,100,100,100,100,100"); 
	gridbusresult.setColAlign("right,right,right,right,right,right,right,right,right");
	gridbusresult.setSkin("dhx_skyblue");
	gridbusresult.setEditable(false);
	gridbusresult.init(); 
}

function createBranchResult(name) {
	gridbranchresult = new dhtmlXGridObject(name);     
	gridbranchresult.setImagePath("codebase/imgs/");   
	gridbranchresult.setHeader("From,To,P (MW) (From),Q (MVAR) (From),P (MW) (To),Q (MVAR) (To),P (MW) (Loss),Q (MVAR) (Loss)"); 
	gridbranchresult.setInitWidths("50,50,100,100,100,100,100,100"); 
	gridbranchresult.setColAlign("right,right,right,right,right,right,right,right");
	gridbranchresult.setSkin("dhx_skyblue");
	gridbranchresult.setEditable(false);
	gridbranchresult.init(); 
}

// adds a new row to the grid_bus
function addBus(id,send) {
	gridbus.addRow(id,",1,0,0,0,0,0,0,1,0,0,0",gridbus.getRowsNum());
	updateBusData();
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'addBus',
		  id: id
		});
	}
}

// adds a new row to the grid_branch
function addBranch(id,send) {	
	gridbranch.addRow(id,",,0,0,0,0,0,1",gridbranch.getRowsNum());
	updateBranchData();
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'addBranch',
		  id: id
		});
	}
}

// removes the selected row from the grid_bus
function removeBus(id,send) {
	gridbus.deleteRow(id);
	updateBusData();
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'removeBus',
		  id: id
		});
	}
}

// removes the selected row from the grid_branch
function removeBranch(id,send) {			
	gridbranch.deleteRow(id);
	updateBranchData();
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'removeBranch',
		  id: id
		});
	}			
}

function changeOptionCell(row,col,value,send) {
	gridoption.cells(row,col).setValue(value); 
	updateOptionData();	
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'changeOptionCell',
		  row: row,
		  col: col,
		  value: value
		});
	}
}

function changeBusCell(row,col,value,send) {
	gridbus.cells(row,col).setValue(value); 
	updateBusData();	
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'changeBusCell',
		  row: row,
		  col: col,
		  value: value
		});
	}
}

function changeBranchCell(row,col,value,send) {
	gridbranch.cells(row,col).setValue(value); 
	updateBranchData();	
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'changeBranchCell',
		  row: row,
		  col: col,
		  value: value
		});
	}
}

function getDataGrid() {
	updateOptionData();
	updateBusData();
	updateBranchData();
}

function loadDataGrid(data) {
	gridoption.clearAll();
	gridoption.parse([data.optLF],"jsarray");
	gridbus.clearAll();
	gridbus.parse(data.bus,"jsarray");
	gridbranch.clearAll();
	gridbranch.parse(data.branch,"jsarray");
}

function loadResult(result) {
	$("#iter").empty();	
	$("#loss").empty();;
	if (result.bus != null) {
		$("#iter").html("<p>Number of iterations: "+result.iteration+"</p>");
		$("#loss").html("<p>Total loss: P (MW) = "+result.loss[0]+" Q (MW) = "+result.loss[1]+"</p>");
		gridbusresult.parse(result.bus,"jsarray");
		gridbranchresult.parse(result.branch,"jsarray");
	} else {
		$("#iter").html("<p>Not converged</p>");
	}
}

function updateOptionData() {
	var option = [];

	gridoption.forEachRow(function(id){
		for (var c = 0; c < gridoption.getColumnsNum(); c++) {
			option[c] = parseFloat(gridoption.cells(id,c).getValue());
		}
	});
	
	data.optLF = option;
}

function updateBusData() {
	var bus = [];
	
	r = 0;
	gridbus.forEachRow(function(id){
		row = [];
		for (var c = 0; c < gridbus.getColumnsNum(); c++) {
			row[c] = parseFloat(gridbus.cells(id,c).getValue());
		}
		bus[r] = row;
		r++;
	});

	data.bus = bus;
}

function updateBranchData() {
	var branch = [];
	
	r = 0;
	gridbranch.forEachRow(function(id){
		row = [];
		for (var c = 0; c < gridbranch.getColumnsNum(); c++) {
			row[c] = parseFloat(gridbranch.cells(id,c).getValue());
		}
		branch[r] = row;
		r++;
	});

	data.branch = branch;
}

// Hello is sent from every newly connected user
TogetherJS.hub.on('togetherjs.hello', function () {	
	TogetherJS.send({
		type: 'init',
		data: data
	});		
});

TogetherJS.hub.on('init', function (msg) {
	if (!msg.sameUrl) {
		return;
	}
	$('#fileinput').hide();
	$('#together').hide();
	loadDataGrid(msg.data);
});

TogetherJS.hub.on('changeOptionCell', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
   changeOptionCell(msg.row,msg.col,msg.value,false);
});

TogetherJS.hub.on('addBus', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
   addBus(msg.id,false);
});	

TogetherJS.hub.on('removeBus', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
	removeBus(msg.id,false);
});	

TogetherJS.hub.on('changeBusCell', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
   changeBusCell(msg.row,msg.col,msg.value,false);
});

TogetherJS.hub.on('addBranch', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
   addBranch(msg.id,false);
});	

TogetherJS.hub.on('removeBranch', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
	removeBranch(msg.id,false);
});	

TogetherJS.hub.on('changeBranchCell', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
   changeBranchCell(msg.row,msg.col,msg.value,false);
});

TogetherJS.hub.on('loadFile', function (msg) {
   if (!msg.sameUrl) {
	return;
   }
	$('#fileinput').hide();
	loadDataGrid(msg.option,msg.bus,msg.branch);
});
	
dhtmlxEvent(window,"load",function() {
	createOptionGrid('grid_option');
	createBusGrid('grid_bus');
	createBranchGrid('grid_branch');
	createBusResult('bus_result');
	createBranchResult('branch_result');
	
	$('#together').hide();
	//$('#fileinput').hide();
	
	$('#together').click(function () {
		TogetherJS(this);
		return false;
	});	

	$('#fileinput').change(function (evt) {
		readFile(evt);
		$('#fileinput').hide();	
		$('#together').show();
	});
	
	$('#addBus').click(function () {
		var id = (new Date()).valueOf();				
		addBus(id,true);
	});	
	
	$('#addBranch').click(function () {
		var id = (new Date()).valueOf();				
		addBranch(id,true);
	});	
	
	$('#removeBus').click(function () {
		var id = gridbus.getSelectedId();
		removeBus(id,true);
	});		

	$('#removeBranch').click(function () {
		var id = gridbranch.getSelectedId();
		removeBranch(id,true);
	});			

	$('#run').click(function () {
		getDataGrid();
		json = JSON.stringify(data);
		$("#iter").empty();		
		$("#iter").html("<p><img src='img/processing.gif'></img></p>");
		$("#loss").empty();	
		gridbusresult.clearAll();
		gridbranchresult.clearAll();
		$.ajax({
			type: 'POST',
			url: 'http://localhost/NDSE/webapi/nws/v1/loadflow',
			data: json,
			contentType: 'text/plain',
			success: function (result) {
				loadResult(result);
			},
			error: function (resp) {
				$("#iter").empty();
				$("#loss").empty();
				alert("Find error!!!");		
			}
		});
	});

	gridoption.attachEvent("onEditCell", function(stage,row,col,nValue,oValue){ 
		if (stage == 2) {
			if (nValue != oValue) {
				changeOptionCell(row,col,nValue,true);
				return true;
			}
		}
	});
	
	gridbus.attachEvent("onEditCell", function(stage,row,col,nValue,oValue){ 
		if (stage == 2) {
			if (nValue != oValue) {
				changeBusCell(row,col,nValue,true);
				return true;
			}
		}
	});
	
	gridbranch.attachEvent("onEditCell", function(stage,row,col,nValue,oValue){ 
		if (stage == 2) {
			if (nValue != oValue) {
				changeBranchCell(row,col,nValue,true);
				return true;
			}
		}
	});
});