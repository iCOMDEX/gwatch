//This scripts creates SNP bars on client side of GWATCH HIGHWAY

function log10(arg) {
    return Math.log(arg) / Math.LN10;
}

function decimalToHex(d, padding) {
    var hex = Number(Math.round(d)).toString(16);
    padding = typeof (padding) === "undefined" || padding === null ? padding = 2 : padding;

    while (hex.length < padding) {
        hex = "0" + hex;
    }

    return hex;
}


function RowCache() {}



function createNormalBuffer(gl, numPoints) {
    /*var frontNormal = [ 0.0, 0.0, 1.0 ];	
      var backNormal = [ 0.0, 0.0, -1.0 ];	
    var topNormal = [ 0.0, 1.0, 0.0 ];
    var leftNormal = [ -1.0, 0.0,  0.0 ];
    var rightNormal = [ 1.0, 0.0, 0.0 ];
    */

    // 16 normals per column, 3 floats per normal, 4 bytes per float    
    var arrayBuffer = new ArrayBuffer(numPoints * 20 * 3 * 4);
    var floatDataArray = new Float32Array(arrayBuffer);
    var floatData = new TypedArrayWrapper(floatDataArray);
    // luckily the arraybuffer inits to zeros
    var numNormals = numPoints * 20;
    //alert(numNormals)
    for (var i = 0; i < numNormals; i += 20) {
        floatData.addData3(0, 0, 1);
        floatData.addData3(0, 0, 1);
        floatData.addData3(0, 0, 1);
        floatData.addData3(0, 0, 1);

        floatData.addData3(0, 0, -1);
        floatData.addData3(0, 0, -1);
        floatData.addData3(0, 0, -1);
        floatData.addData3(0, 0, -1);

        floatData.addData3(0, 1, 0);
        floatData.addData3(0, 1, 0);
        floatData.addData3(0, 1, 0);
        floatData.addData3(0, 1, 0);

        floatData.addData3(-1, 0, 0);
        floatData.addData3(-1, 0, 0);
        floatData.addData3(-1, 0, 0);
        floatData.addData3(-1, 0, 0);

        floatData.addData3(1, 0, 0);
        floatData.addData3(1, 0, 0);
        floatData.addData3(1, 0, 0);
        floatData.addData3(1, 0, 0);


    }

    var normalBuffer = gl.createBuffer();

    gl.bindBuffer(gl.ARRAY_BUFFER, normalBuffer);

    gl.bufferData(gl.ARRAY_BUFFER, floatDataArray, gl.STATIC_DRAW);
    gl.bindBuffer(gl.ARRAY_BUFFER, null);

    normalBuffer.numPoints = numPoints;

    return normalBuffer;
}


function calculateColor(oddsRatio) {
    var output = [1, 1, 1];

    if (oddsRatio == -1.0) {
        output[0] = output[1] = 0.0;
        output[2] = 1.0;
        return output;
    }

    if (oddsRatio < 0.5) {
        output[0] = output[2] = 0.0;
        output[1] = 1.0;
    } else if (oddsRatio < 0.9999999) {
        output[0] = output[2] = 0.5;
        output[1] = 1.0;
    } else if (oddsRatio <= 1.0000001) {
        output[0] = output[1] = output[2] = 1.0;
    } else if (oddsRatio < 2.0) {
        output[0] = 1.0;
        output[1] = output[2] = 0.5;
    } else // > 2.0 
    {
        output[0] = 1.0;
        output[1] = output[2] = 0.0;
    }



    return output;

}

// Add Draw Method,
RowCache.prototype.draw = function (gl, shaderProgram) {
    if (!this.data || !this.indexBuffer || !this.vertexBuffer || this.numItems == 0) {
        return;
    }

    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexBuffer);

    gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 3 * 4 * 2, 0);
    gl.vertexAttribPointer(shaderProgram.vertexColorAttribute, 3, gl.FLOAT, false, 3 * 4 * 2, 3 * 4);

    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.indexBuffer);
    gl.drawElements(gl.TRIANGLES, this.numItems, gl.UNSIGNED_SHORT, 0);


    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, null);

}


RowCache.prototype.drawOne = function (gl, shaderProgram, startIndex) {
    if (!this.data || !this.indexBuffer || !this.vertexBuffer) {
        return;
    }

    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexBuffer);

    gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 3 * 4 * 2, 0);
    gl.vertexAttribPointer(shaderProgram.vertexColorAttribute, 3, gl.FLOAT, false, 3 * 4 * 2, 3 * 4);

    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.indexBuffer);
    gl.drawElements(gl.TRIANGLES, 30, gl.UNSIGNED_SHORT, startIndex * 2);

    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, null);


}
RowCache.prototype.deleteBuffers = function (gl) {
    if (this.vertexBuffer) {
        gl.deleteBuffer(this.vertexBuffer);
        gl.deleteBuffer(this.indexBuffer);
    }

}

RowCache.prototype.regenerateBuffers = function (gl, columnData) {

    if (!this.vertexBuffer) {
        this.vertexBuffer = gl.createBuffer();
        this.indexBuffer = gl.createBuffer();
    }


    var halfColumnSize = 0.5;
    var vertCount = 0;
	var prev = 0
    var numColumns = columnData["NumColumns"]; // more than the number of actual data columns
    //alert(colOrder.length)
    //alert(numColumns)
    //document.write(JSON.stringify(this.data["NumRows"]));
    var numVisibleColumns = columnData.numVisibleColumns;
    var numRows = this.data["NumRows"];
    var numDataPoints = this.data["NumDataPoints"];
//alert(numDataPoints)
    if (!RowCache.prototype.normalBuffer || RowCache.prototype.normalBuffer.numPoints < numDataPoints) {
        RowCache.prototype.normalBuffer = createNormalBuffer(gl, numDataPoints);
    }

    
    var mask = columnData.columnMask;
    var tmask = columnData.TcolumnMask;
    
	var tmaskarr = tmask.split(" ");
 
    // 20 verts per column 6 floats per vert 4 bytes per float
    var vertsArrayBuffer = new ArrayBuffer(numDataPoints * 20 * 6 * 4);
    var vertsFloatArrayData = new Float32Array(vertsArrayBuffer);
    var vertsFloatArray = new TypedArrayWrapper(vertsFloatArrayData);

    // 5 faces per column, 6 inds per face, 2 byts per ind
    var indsArrayBuffer = new ArrayBuffer(numDataPoints * 5 * 6 * 2);
    var indsArrayData = new Uint16Array(indsArrayBuffer);
    var indsUintArray = new TypedArrayWrapper(indsArrayData);

    var dataPoint = 0;
   // alert(numRows)
    for (var row = 0; row < numRows; ++row) {
        var startX = (-numVisibleColumns * 0.5) - 0.5;
        var visCol = 0;
        var zOffset = -row;
        var rowData = this.data[row];
        if (!rowData) {
            continue;
        }

        var flipOddsRatio = false;
        var grey = false;
        var white = false;

        if (this.polarizationData) {
            var numPolSamples = this.polarizationData["NumPolSamples"];
            var rowRelativeToPolData = (this.startRow + row) - this.polarizationData["Row"] + ((numPolSamples - 1) / 2);
            if ((rowRelativeToPolData < 0) || (rowRelativeToPolData >= numPolSamples)) {
                // hide row, outside polarisation region
                continue;
            }

            flipOddsRatio = this.polarizationData[rowRelativeToPolData] == -1;
            if (this.polarizationData[rowRelativeToPolData] == -3) {
                grey = true;
            }
            if (this.polarizationData[rowRelativeToPolData] == -2) {
                white = true;
            }

        }

        var averageOdds = 0;
        var actualColumns = 0;
        var dddd = numColumns * numRows
       
        var showTrack = 0;
        
//////////////////////////////////////////////////////////////////


        for (var i = 0; i < numColumns; ++i) {
              
            var groupIndex = m_ColumnData[i]["DataColumn"];
            var IdColumn = m_ColumnData[i]["IdColumn"];

			showTrack = 0;
			if((i+1) >= numColumns)
				showTrack = 1;
   
     //       if ((mask & (1 << groupIndex)) == 0) {
            if (tmaskarr[IdColumn] == -1) {
           
                continue;
            }

            ++visCol;

			var dataColumn = m_ColumnData[i]["DataColumn"];
          	var dC = dataColumn
            if (dataColumn == -1) // separator column
            {
                continue;
            }

		 	if(colOrder.length > 1 ) {
			
				var dC = colOrder[dataColumn]             
	 
			}


            var data = rowData[dC]; 
            if (!data) continue;
            var height = data[0];
            var oddsRatio = data[1];
            averageOdds += oddsRatio;
            ++actualColumns;
            ++dataPoint;
              
			var xOffset = startX + visCol;


            /* this data is filtered server side now
               if ( logP == -1.0 ) continue;; // missing data or not significant enough 
             //if ( height < 0.0 ) continue;//
	     */

            // for polaraisation
            if (flipOddsRatio) {
                oddsRatio = 1.0 / oddsRatio;
            }


            var color;
            if (!grey) {
                color = calculateColor(oddsRatio);
            } else {
                color = [0.7, 0.7, 0.7];
                //color = [ 0, 0, 0 ];
            }
            if (white) {
                //color = [ 0.7, 0.7, 0.7 ];
                color = [0, 0, 0];
            }

            var rs = this.data["Rows"][row]["Name"];

            if (rs.indexOf('-1') + 1) {
                color[2] = color[2] - .7;
                color[1] = color[1] - .7;
                color[0] = color[0] - .7;
            }
if(m_ColumnData[i]["Name"].indexOf('Density') != -1)
	{
 	
	var red;
	var green;
	var blue;
	   
	if (height < 1.5) 
	{
        red = blue = 0.0;
        green = 1.0;
    
    } 
    else if (height < 2) {
        red = blue = 0.5;
        green = 1.0;
    
    } 
    else if (height < 6) {
        red = blue = blue = 1.0;
    } else if (height < 3.0) 
    {
        red = 1.0;
        green = blue = 0.5;
    } else  
    {
        red = 1.0;
        green = blue = 0.0;
    }
	
	height = 0;
	
    var color = [red,green,blue];	
    
    
	
	
	}
            var left = -halfColumnSize + xOffset;
            var right = halfColumnSize + xOffset;
            
            
        //Track of genes
        
			if(showTrack == 1){
				
				right = startX - 3 ;
				left = right - 2*halfColumnSize;
			
				right = right + 3
				left = left + 1 //numColumns
				if(oddsRatio == -1){
				color = [.5,.3,.1]; 
				}
				if(oddsRatio == 1){ 
				color = [1,1,1];
				}
				var prev = oddsRatio
				 
			}
		// End track of genes
		
            var front = halfColumnSize + zOffset ;
            var back = -halfColumnSize + zOffset ;

            data.min = vec3.create([left, 0.0, back]);
            data.max = vec3.create([right, height, front]);
            data.indexStart = indsUintArray.current;

            //front
            vertsFloatArray.addData3(left, 0.0, front);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(right, 0.0, front);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(right, height, front);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(left, height, front);
            vertsFloatArray.addDataList(color);

            indsUintArray.addData6(vertCount, vertCount + 1, vertCount + 2,
            vertCount + 2, vertCount + 3, vertCount);


            vertCount += 4;

            //back

            vertsFloatArray.addData3(left, 0.0, back);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(right, 0.0, back);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(right, height, back);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(left, height, back);
            vertsFloatArray.addDataList(color);

            indsUintArray.addData6(vertCount, vertCount + 2, vertCount + 1,
            vertCount + 2, vertCount, vertCount + 3);


            vertCount += 4;

            // top
            vertsFloatArray.addData3(left, height, back);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(left, height, front);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(right, height, front);
            vertsFloatArray.addDataList(color);

            vertsFloatArray.addData3(right, height, back);
            vertsFloatArray.addDataList(color);

            indsUintArray.addData6(vertCount, vertCount + 1, vertCount + 2,
                vertCount + 2, vertCount + 3, vertCount);

            vertCount += 4;

            // left face
            vertsFloatArray.addData3(left, 0.0, back);
            vertsFloatArray.addDataList(color);
            vertsFloatArray.addData3(left, 0.0, front);
            vertsFloatArray.addDataList(color);
            vertsFloatArray.addData3(left, height, front);
            vertsFloatArray.addDataList(color);
            vertsFloatArray.addData3(left, height, back);
            vertsFloatArray.addDataList(color);

            indsUintArray.addData6(vertCount, vertCount + 1, vertCount + 2,
                vertCount + 2, vertCount + 3, vertCount);
            vertCount += 4;

            // right face
            vertsFloatArray.addData3(right, 0.0, back);
            vertsFloatArray.addDataList(color);
            vertsFloatArray.addData3(right, height, back);
            vertsFloatArray.addDataList(color);
            vertsFloatArray.addData3(right, height, front);
            vertsFloatArray.addDataList(color);
            vertsFloatArray.addData3(right, 0.0, front);
            vertsFloatArray.addDataList(color);

            indsUintArray.addData6(vertCount, vertCount + 1, vertCount + 2,
                vertCount + 2, vertCount + 3, vertCount);

            vertCount += 4;

        }

        if (actualColumns != 0) {
            averageOdds /= actualColumns;
            var color = calculateColor(averageOdds);
            this.data["Rows"][row].averageColor = "#" +
                decimalToHex(color[0] * 255) +
                decimalToHex(color[1] * 255) +
                decimalToHex(color[2] * 255);
        } else {
            this.data["Rows"][row].averageColor = "white";
        }

        //alert( rowData.averageColor );

    }


    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexBuffer);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.indexBuffer);

    gl.bufferData(gl.ARRAY_BUFFER, vertsFloatArrayData, gl.DYNAMIC_DRAW);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, indsArrayData, gl.DYNAMIC_DRAW);
    this.numItems = indsUintArray.current;

    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, null);
}


function peptidValue(val){

	var min = 1443.5;
	var max = 98304.0;
	var N = (val-min)/(max-min)+2;  
	  
	res = Math.pow(Math.exp(-val),1/N);
	return res;	
		
	}


RowCache.prototype.getDescriptiveText = function (offset, eyePos, mouseDir, invert) {

    var translatedEye = vec3.create(eyePos);
    if (invert) {
        translatedEye[2] *= -1.0;
    }
    translatedEye[2] += offset;

    var actualMouseDir = vec3.create(mouseDir);
    if (invert) {
        actualMouseDir[2] *= -1.0;
    }
    var tmask = m_ColumnData.TcolumnMask;
    
	var tmaskarr = tmask.split(" ");
	
	
	//document.write(JSON.stringify(m_ColumnData));
	
    var numColumns = m_ColumnData["NumColumns"]; // more than the number of actual data columns
    var numVisibleColumns = m_ColumnData.numVisibleColumns;
     
    var numRows = this.data["NumRows"];
    var numDataPoints = this.data["NumDataPoints"];
    var mask = m_ColumnData.columnMask;
    var dataPoint = 0;
    for (var row = 0; row < numRows; ++row) {
        var startX = -numVisibleColumns * 0.5;
        var visCol = 0;
        var zOffset = -row;
        var rowData = this.data[row];
        if (!rowData) {
            continue;
        }

        for (var i = 0; i < numColumns; ++i) {
        
        
           // var groupIndex = m_ColumnData[i]["GroupIndex"];
            var IdColumn = m_ColumnData[i]["IdColumn"];

        //    if ((mask & (1 << groupIndex)) == 0) {
            if (tmaskarr[IdColumn] == -1) {
           
            
                continue;
            }

            ++visCol;
            var dataColumn = m_ColumnData[i]["DataColumn"];
            if (dataColumn == -1) // separator column
            {
                continue;
            }

      
          var dC = dataColumn
             
		 if(colOrder.length > 1 ) {
			var dC = colOrder[dataColumn]             
	 
			 }


            var data = rowData[dC];/////// tuta
            if (!data) continue;

            var hitCol = testAABBIntersection(data.min, data.max,
                translatedEye, actualMouseDir, 5000);
                
                
            if (hitCol) {

                var rs = this.data["Rows"][row]["Name"];
                var tab = this.data["Rows"][row]["Tab"];

                if (rs.indexOf('-1') != -1) {
                    var trax = 'report'
                } else {
                    var trax = 'page'
                }
            
        var	coltest = m_ColumnData[i]["Name"];   
        
    
		var compare = "<br><span id=\"compare_link\"><a href=\"#\" onClick = \"fill('bits/compare_link.php?module=" + m_module + "&pos=" + this.data["Rows"][row]["Coords"]  + "&chr=" + m_chr + "', 'compare_link')\"> Generate Link </a></span>"
        

       
 		if (coltest.indexOf('Genes') == -1 && coltest.indexOf('Effect') == -1) 
 		{ 
 		
 	
 		
                our_string = "<a href=\"/bits/arg.php?snp=" + rs + "&tab=" + m_module + "\" target=_blank>TRAX " + trax + " </a>";
                rs = rs.replace(/-1/, '')
                var statname = 'QSS';
                if (m_ColumnData[i]["StatName"] != '') statname = m_ColumnData[i]["StatName"];
                var descString =  "<br>SNP rs ID: " + this.data["Rows"][row]["Name"];
             	descString += compare;
                if (this.data["Rows"][row]["marker"] != '') descString += "<br/>Affymetrix id: " + this.data["Rows"][row]["marker"]; 
                descString += "<br>Coordinates: " + addCommas(this.data["Rows"][row]["Coords"]);
                
                descString += "<br/>MAF: " + this.data["Rows"][row]["MAF"] + "<br/>Test: ";
                descString += "<a href=\"/bits/mplinkmap.php?test=" + m_ColumnData[i]["Name"].replace(/\//g, "!") + "&module=" + m_module + "\" target=\"_blank\">" + m_ColumnData[i]["Name"] + "</a><br/>";
                if (this.data["Rows"][row]["Gene"] != '' && this.data["Rows"][row]["Gene"] != null) descString += "Gene: " + this.data["Rows"][row]["Gene"] + "<br/>";
                var logP = (data[0] / -6.0) - 2.0;
               descString += "-log( P ): " + -logP.toFixed(3) + "<br/>";
               //  descString += "-log( P ): " + data[0] + " " + data[2] +"<br/>";
                descString += statname + ": " + Number(data[1]).toFixed(3) + "<br/>";

                if (rs.indexOf('rs') != -1) descString += "<a href=\"http://genome-mirror.duhs.duke.edu/cgi-bin/hgTracks?hgHubConnect.destUrl=..%2Fcgi-bin%2FhgTracks&clade=mammal&org=Human&db=hg19&position=" + rs + "&hgt.suggest=&hgt.suggestTrack=knownGene&Submit=submit&hgsid=3151620&hgt.newJQuery=1\" target=_blank>UCSC report for " + rs + " </a><br> <a href=\http://www.ncbi.nlm.nih.gov/projects/SNP/snp_ref.cgi?searchType=adhoc_search&type=rs&rs=" + rs + " target=_blank>dbSNP report for " + rs + " </a><br>";
                descString += our_string;
                
		}
		else if (coltest.indexOf('Genes') == -1) 
 		{
 		 
 			 	our_string = "";
                rs = rs.replace(/-1/, '')
                var descString = "SNP rs ID: " + this.data["Rows"][row]["Name"];
                descString += "<br/>Track: GWAS catalog <br/>";
                
                if (this.data["Rows"][row]["Effect"] != '' && this.data["Rows"][row]["Effect"] != null) 
                	descString += "Trait: " + this.data["Rows"][row]["Effect"] + "<br/>";
               		descString += "Link to GWAS catalog: <a href=\"https://www.ebi.ac.uk/gwas/search?query=" + this.data["Rows"][row]["Name"]+"\" target=_blank>"+this.data["Rows"][row]["Name"]+"</a>";
                descString += our_string;
	 	}
		else{
		
		        our_string = "";
                rs = rs.replace(/-1/, '')
                var descString = "SNP rs ID: " + this.data["Rows"][row]["Name"];
                descString += "<br/>Track: ";
                descString += m_ColumnData[i]["Name"] + "<br/>";
                if (this.data["Rows"][row]["Gene"] != '' && this.data["Rows"][row]["Gene"] != null) descString += "Gene: " + this.data["Rows"][row]["Gene"] + "<br/>";
                descString += our_string;

		
		
		
		}        	
		return [descString, data.indexStart, row + this.startRow];
		 
		 
    	}
    }
}

    return null;


}

function handleLoadedData(data, rowCache, columnData, gl) {
    rowCache.data = data;
    if (!data["Error"]) {
        rowCache.regenerateBuffers(gl, columnData);
        rowCache.age = 0.0;
        // call the function passed us when creating
        rowCache.dataLoadedFunction();
    } else {
        alert(data["Error"]);
    }
}

function createRowCache(m_module, m_chr, gl, index, count, columnData, polData, dataLoadedFunction) {
    var rowCache = new RowCache();

    rowCache.startRow = index;
    rowCache.rowCount = count;
    rowCache.polarizationData = polData;
    rowCache.dataLoadedFunction = dataLoadedFunction;
    var request = new XMLHttpRequest();
    var result = getUrlVar();
 
//alert ("getData.php?module=" + m_module + "&chr=" + m_chr + "&rowStart=" + index + "&rowCount=" + count)
    request.open("GET", "getData.php?module=" + m_module + "&chr=" + m_chr + "&rowStart=" + index + "&rowCount=" + count + "&threshold=" + result['threshold']);
    request.onreadystatechange = function () {
        if (request.readyState == 4) {


            //alert(request.responseText);
            handleLoadedData(parseJSONAndCheckErrors(request.responseText), rowCache, columnData, gl);
        }
    }
    request.send();
     return rowCache;
}