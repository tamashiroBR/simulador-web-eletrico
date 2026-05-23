var gridoption;
var gridbus;	
var gridbranch;	
var gridresult;	
var dataOption = [];		
var dataBus = [];
var dataBranch = [];
var data = { info: "lf",
			 optLF: [],
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
			gridresult.clearAll();
			getDataGrid();
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

function createResultGrid(name) {
	gridresult = new dhtmlXGridObject(name);     
	gridresult.setImagePath("codebase/imgs/");   
	gridresult.setHeader("Bus,U (pu),Theta (degree),Pgen (MW),Qgen (MVAR),Pload (MW),Qload (MVAR),Qgmax (MVAR),Qgmin (MVAR)"); 
	gridresult.setInitWidths("50,100,100,100,100,100,100,100,100"); 
	gridresult.setColAlign("right,right,right,right,right,right,right,right,right");
	gridresult.setSkin("dhx_skyblue");
	gridresult.setEditable(false);
	gridresult.init(); 
}

// adds a new row to the grid_bus
function addBus(id,send) {
	gridbus.addRow(id,",1,0,0,0,0,0,0,1,0,0,0",gridbus.getRowsNum());
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'addBus',
		  id: id
		});
	}
}

// adds a new row to the grid_branch
function addBranch(id,send) {	
	gridbranch.addRow(id,",0,0,0,0,1,0,1",gridbranch.getRowsNum());
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
	if (send && TogetherJS.running) {
		TogetherJS.send({
		  type: 'removeBranch',
		  id: id
		});
	}			
}

function changeOptionCell(row,col,value,send) {
	gridoption.cells(row,col).setValue(value);            
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
	var option = [];
	var bus = [];
	var branch = [];
	
	gridoption.forEachRow(function(id){
		for (var c = 0; c < gridoption.getColumnsNum(); c++) {
			dataOption[c] = gridoption.cells(id,c).getValue();
			option[c] = parseFloat(dataOption[c]);
		}
	});
	
	r = 0;
	gridbus.forEachRow(function(id){
		row = [];
		rowInt = [];
		for (var c = 0; c < gridbus.getColumnsNum(); c++) {
			row[c] = gridbus.cells(id,c).getValue();
			rowInt[c] = parseFloat(row[c]);
		}
		dataBus[r] = row;
		bus[r] = rowInt;
		r++;
	});
	
	r = 0;
	gridbranch.forEachRow(function(id){
		row = [];
		rowInt = [];
		for (var c = 0; c < gridbranch.getColumnsNum(); c++) {
			row[c] = gridbranch.cells(id,c).getValue();
			rowInt[c] = parseFloat(row[c]);
		}
		dataBranch[r] = row;
		branch[r] = rowInt;
		r++;
	});

	data.optLF = option;
	data.bus = bus;
	data.branch = branch;
}

function loadDataGrid(option,bus,branch) {
	gridoption.clearAll();
	gridoption.parse([option],"jsarray");
	gridbus.clearAll();
	gridbus.parse(bus,"jsarray");
	gridbranch.clearAll();
	gridbranch.parse(branch,"jsarray");
}

function loadResult(result) {
	$("#iter").empty();	
	if (result.bus != null) {
		$("#iter").html("<p>Number of iterations: "+result.iteration+"</p>");
		gridresult.parse(result.bus,"jsarray");
	} else {
		$("#iter").html("<p>Not converged</p>");
	}
}

// Hello is sent from every newly connected user
TogetherJS.hub.on('togetherjs.hello', function () {
	TogetherJS.send({
		type: 'init',
		option: dataOption,
		bus: dataBus,
		branch: dataBranch
	});		
});

TogetherJS.hub.on('init', function (msg) {
	if (!msg.sameUrl) {
		return;
	}	
	loadDataGrid(msg.option,msg.bus,msg.branch);
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
	
dhtmlxEvent(window,"load",function() {	  
	//document.getElementById('fileinput').addEventListener('change', readFile, false);

	createOptionGrid('grid_option');
	createBusGrid('grid_bus');
	createBranchGrid('grid_branch');
	createResultGrid('grid_result');

	$('#together').click(function () {
		TogetherJS(this);
		return false;
	});	

	$('#fileinput').change(function (evt) {
		readFile(evt);
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
		gridresult.clearAll();
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