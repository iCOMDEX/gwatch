//This scripts creates road elements on client side of GWATCH HIGHWAY
function Road() {}


Road.prototype.regenerateRoad = function (gl, columnData) {
    if (!this.vertexBuffer) {
        this.vertexBuffer = gl.createBuffer();
        this.indexBuffer = gl.createBuffer();
    }
    var tmask = m_ColumnData.TcolumnMask;
    
	var tmaskarr = tmask.split(" ");
    var numColumns = columnData["NumColumns"];


    var mask = m_ColumnData.columnMask;

    var separatorCols = [];
    var bg = [];
    var colIndex = 0;
    
    for (var i = 0; i < numColumns; ++i) { 
    	
			
        var groupIndex = m_ColumnData[i]["GroupIndex"];

            var IdColumn = m_ColumnData[i]["IdColumn"];
            var dataColumn = m_ColumnData[i]["DataColumn"];

        //    if ((mask & (1 << groupIndex)) == 0) {
        
        if (tmaskarr[IdColumn] == -1) {
        
            continue;
        }
		
        if(m_ColumnData[i]["bg"] > 0 ){
        
        	bg.push(i);
        
        }

        if (dataColumn == -1) // separator column
        {
            separatorCols.push(colIndex);
        }

        ++colIndex;
    }
    
    
    var numVisibleColumns = columnData.numVisibleColumns;
    
    var left = (numVisibleColumns * -0.5);
    var right = ((numVisibleColumns - 2) * 0.5);
    
    
    var roadStart = 800;
    var roadEnd = -2000;

    if (this.polarized) {
        roadStart = 80;
        roadEnd = -80;
    }

    this.min = vec3.create([left, 0, roadEnd]);
    this.max = vec3.create([right, 0.01, roadStart]);

    var numQuads = 2 + separatorCols.length + 2 * bg.length;

    var vertsArrayBuffer = new ArrayBuffer(numQuads * 4 * 6 * 4);
    var vertsFloatArrayData = new Float32Array(vertsArrayBuffer);
    var vertsFloatArray = new TypedArrayWrapper(vertsFloatArrayData);

    var indsArrayBuffer = new ArrayBuffer(numQuads * 6 * 2);
    var indsArrayData = new Uint16Array(indsArrayBuffer);
    var indsUintArray = new TypedArrayWrapper(indsArrayData);

    var vertCount = 0;

    vertsFloatArray.addData3(left, -1.0, roadStart);
    vertsFloatArray.addData3(0.3, 0.3, 0.3);

    vertsFloatArray.addData3(left, -1.0, roadEnd);
    vertsFloatArray.addData3(0.3, 0.3, 0.3);

    vertsFloatArray.addData3(right, -1.0, roadEnd);
    vertsFloatArray.addData3(0.3, 0.3, 0.3);

    vertsFloatArray.addData3(right, -1.0, roadStart);
    vertsFloatArray.addData3(0.3, 0.3, 0.3);

    indsUintArray.addData6(vertCount, vertCount + 2, vertCount + 1,
        vertCount + 2, vertCount, vertCount + 3);

    vertCount += 4;

    vertsFloatArray.addData3(left, -0.5, 0.5);
    vertsFloatArray.addData3(1.0, 1.0, 1.0);

    vertsFloatArray.addData3(left, -0.5, -0.5);
    vertsFloatArray.addData3(1.0, 1.0, 1.0);

    vertsFloatArray.addData3(right, -0.5, -0.5);
    vertsFloatArray.addData3(1.0, 1.0, 1.0);

    vertsFloatArray.addData3(right, -0.5, 0.5);
    vertsFloatArray.addData3(1.0, 1.0, 1.0);

    indsUintArray.addData6(vertCount, vertCount + 2, vertCount + 1,
        vertCount + 2, vertCount, vertCount + 3);

    vertCount += 4;


    for (var i = 0; i < separatorCols.length; ++i) {
    
    
    
        var sepLeft = left + separatorCols[i];
        var sepRight = sepLeft + 1;

        vertsFloatArray.addData3(sepLeft, 0, roadStart);
        vertsFloatArray.addData3(1.0, 1.0, 1.0);

        vertsFloatArray.addData3(sepLeft, 0, roadEnd);
        vertsFloatArray.addData3(1.0, 1.0, 1.0);

        vertsFloatArray.addData3(sepRight, 0, roadEnd);
        vertsFloatArray.addData3(1.0, 1.0, 1.0);

        vertsFloatArray.addData3(sepRight, 0, roadStart);
        vertsFloatArray.addData3(1.0, 1.0, 1.0);

        indsUintArray.addData6(vertCount, vertCount + 2, vertCount + 1,
            vertCount + 2, vertCount, vertCount + 3);

        vertCount += 4;

    }
    var deep = -0.95;
        for (var i = 0; i < bg.length; ++i) {
    
    
    
        var sepLeft = left + bg[i];
        var sepRight = sepLeft + 1;

        vertsFloatArray.addData3(sepLeft, deep, roadStart);
        vertsFloatArray.addData3(.4, .4, .4);

        vertsFloatArray.addData3(sepLeft, deep, roadEnd);
        vertsFloatArray.addData3(.4, .4, .4);

        vertsFloatArray.addData3(sepRight, deep, roadEnd);
        vertsFloatArray.addData3(.4, .4, .4);

        vertsFloatArray.addData3(sepRight, deep, roadStart);
        vertsFloatArray.addData3(.4, .4, .4);

        indsUintArray.addData6(vertCount, vertCount + 2, vertCount + 1,
            vertCount + 2, vertCount, vertCount + 3);

        vertCount += 4;

    }

    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexBuffer);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.indexBuffer);

    gl.bufferData(gl.ARRAY_BUFFER, vertsFloatArrayData, gl.STATIC_DRAW);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, indsArrayData, gl.STATIC_DRAW);

    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, null);

    this.numElements = indsUintArray.current;


}

Road.prototype.draw = function (gl, shaderProgram) {
    gl.disableVertexAttribArray(shaderProgram.vertexNormalAttribute);

    // the road is all straight up 
    // so just use a constnt vertex attrib
    gl.vertexAttrib3f(shaderProgram.vertexNormalAttribute, 0, 1, 0);

    gl.bindBuffer(gl.ARRAY_BUFFER, m_Road.vertexBuffer);
    gl.vertexAttribPointer(m_ShaderProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 3 * 4 * 2, 0);
    gl.vertexAttribPointer(m_ShaderProgram.vertexColorAttribute, 3, gl.FLOAT, false, 3 * 4 * 2, 3 * 4);

    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, m_Road.indexBuffer);
    gl.drawElements(gl.TRIANGLES, m_Road.numElements, gl.UNSIGNED_SHORT, 0);

    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, null);

}


function createRoad(gl, columnData) {
    var road = new Road();

    road.regenerateRoad(gl, columnData);

    return road;
}