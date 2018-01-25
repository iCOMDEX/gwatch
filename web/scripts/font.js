function Font()
{
}





function handleLoadedFontXML( font, dom )
{
    var fontNode = dom.documentElement;
    
    if ( fontNode.nodeName != "font" )
    {
	return;
    }
    
    var pages = fontNode.getElementsByTagName("page");
    // todo make this more robust
    var textureName = font.path.replace( "font.xml", pages[0].getAttribute("file") );
    
    font.texture = createTexture( font.gl, textureName );
    
    font.charInfo = [];
    
    var charList = fontNode.getElementsByTagName("char");
    for ( var i = 0; i < charList.length; ++i )
    {
	var charElem = charList[i];
	var charData = new Object();
	charData.x = parseInt( charElem.getAttribute( "x" ) );
	charData.y = parseInt( charElem.getAttribute( "y" ) );
	charData.width = parseInt( charElem.getAttribute( "width" ) );
	charData.height = parseInt( charElem.getAttribute( "height" ) );
	charData.xoffset = parseInt( charElem.getAttribute( "xoffset" ) );
	charData.yoffset = parseInt( charElem.getAttribute( "yoffset" ) );
	charData.xadvance = parseInt( charElem.getAttribute( "xadvance" ) );
	
	font.charInfo[ charElem.getAttribute( "id" ) ] = charData;
    }


    font.kernInfo = [];
    
    var kernList = fontNode.getElementsByTagName("kerning");
    for ( var i = 0; i < kernList.length; ++i )
    {
	var kernElem = kernList[i];
	var first = parseInt( kernElem.getAttribute( "first" ) );
	var second = parseInt( kernElem.getAttribute( "second" ) );
	var amt = parseInt( kernElem.getAttribute( "amount" ) );

	if ( !font.kernInfo[ first ] )
	{
	    font.kernInfo[ first ] = [];
	}
	font.kernInfo[first][second] = amt;
    }
    
    var commonList = fontNode.getElementsByTagName("common");
    font.base = parseInt( commonList[0].getAttribute( "base" ) );
    font.lineHeight = parseInt( commonList[0].getAttribute( "lineHeight" ) );
    font.scaleW = parseInt( commonList[0].getAttribute( "scaleW" ) );
    font.scaleH = parseInt( commonList[0].getAttribute( "scaleH" ) );

    font.loaded = true;

    for ( var t = 0 ; t < font.pendingText.length; ++t )
    {
	var text = font.pendingText[t];
	text.regenerate( font.gl );
    }
}

function createFont( gl, shaderProgram, path )
{
    var font = new Font();
    font.path = path;
    font.gl = gl;
    font.loaded = false;

    font.shaderProgram = shaderProgram;

    font.pendingText = [];

    var request = new XMLHttpRequest();
    request.open("GET", path );
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            handleLoadedFontXML( font, request.responseXML);
        }
    }
    request.send();
    
    
    
    return font;
}

TextString.prototype.draw = function( gl )
{
    if ( !this.generated ) return;

    gl.enableVertexAttribArray(this.shaderProgram.vertexPositionAttribute);
    gl.enableVertexAttribArray(this.shaderProgram.vertexTexCoordAttribute );

    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture( gl.TEXTURE_2D, this.font.texture );
    gl.uniform1i(this.shaderProgram.sampler, 0);

    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexBuffer);

    gl.uniform3fv( this.shaderProgram.color, this.color );

    var stride = 2 * 4 + 2 * 4;
    gl.vertexAttribPointer( this.shaderProgram.vertexPositionAttribute, 2, gl.FLOAT, false, stride , 0);
    gl.vertexAttribPointer( this.shaderProgram.vertexTexCoordAttribute, 2, gl.FLOAT, false, stride,  2 * 4 );

    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.indexBuffer );

    gl.enable( gl.BLEND );
    gl.disable( gl.DEPTH_TEST );

    gl.drawElements(gl.TRIANGLES, this.numElements, gl.UNSIGNED_SHORT, 0 );
    
    gl.disable( gl.BLEND );
    gl.enable( gl.DEPTH_TEST );

    gl.disableVertexAttribArray(this.shaderProgram.vertexPositionAttribute);
    gl.disableVertexAttribArray(this.shaderProgram.vertexTexCoordAttribute );
    
    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, null);

}

TextString.prototype.regenerate  = function( gl )
{
    if ( !this.font.loaded )
    {
	this.font.pendingText.push( this );
	return;

    }
    
    this.vertexBuffer = gl.createBuffer();
    this.indexBuffer = gl.createBuffer();

    var numQuads = this.string.length;

    // 4 verts per quad * 4 floats per vert * 4 bytes per float
    var vertsArrayBuffer = new ArrayBuffer( numQuads * 4 * 4 * 4 );
    var vertsFloatArrayData = new Float32Array( vertsArrayBuffer );
    var vertsFloatArray = new TypedArrayWrapper( vertsFloatArrayData );

    var indsArrayBuffer = new ArrayBuffer(  numQuads * 6 * 2 );
    var indsArrayData = new Uint16Array( indsArrayBuffer );
    var indsUintArray = new TypedArrayWrapper( indsArrayData );

    var currentX = 0;
    var currentY = 0;
    var vertCount = 0;


    for ( var i = 0; i < numQuads; ++ i )
    {
	var currentChar = this.string.charCodeAt( i );
	if ( currentChar == 10 )
	{
	    currentX = 0;
	    currentY += this.font.lineHeight;
	}
	var charData =  this.font.charInfo [ currentChar ];
	if ( !charData )
	{
	    continue;
	}
	var charStartX = currentX + charData.xoffset;
	var charStartY = currentY; //charData.yoffset;
	
	var topUV = 1.0 - ( ( charData.y + charData.height ) / this.font.scaleH );
	var bottomUV = 1.0 - ( charData.y / this.font.scaleH );
	
	var leftUV = charData.x / this.font.scaleW;
	var rightUV = ( charData.x + charData.width ) / this.font.scaleW;

	var yBottom = charStartY + this.font.base - charData.height - charData.yoffset;
	var yTop = charStartY + this.font.base - charData.yoffset;

	vertsFloatArray.addData2( charStartX, yBottom );
	vertsFloatArray.addData2( leftUV,  topUV );

	vertsFloatArray.addData2( charStartX,  yTop );
	vertsFloatArray.addData2( leftUV, bottomUV );

	vertsFloatArray.addData2( charStartX + charData.width, yTop );
	vertsFloatArray.addData2( rightUV, bottomUV );
	
	vertsFloatArray.addData2( charStartX + charData.width, yBottom );
	vertsFloatArray.addData2( rightUV, topUV ) ;
	
	indsUintArray.addData6( vertCount, vertCount + 2, vertCount + 1, 
				vertCount + 2, vertCount , vertCount + 3 );
	
	vertCount += 4;

	currentX += charData.xadvance;
	
	if ( i != numQuads - 1 )
	{
	    if ( this.font.kernInfo[ currentChar ] )
	    {
		var nextChar = this.string.charCodeAt( i + 1 );
		var kernValue = this.font.kernInfo[ currentChar ][ nextChar ];
		if ( kernValue )
		{
		    currentX += kernValue;
		}
	    }
	}
	
    }
    
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexBuffer);
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.indexBuffer);
    
    gl.bufferData(gl.ARRAY_BUFFER, vertsFloatArrayData, gl.STATIC_DRAW);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, indsArrayData, gl.STATIC_DRAW);
    
    gl.bindBuffer( gl.ARRAY_BUFFER, null );
    gl.bindBuffer( gl.ELEMENT_ARRAY_BUFFER, null );
    
    this.numElements = indsUintArray.current;

    this.generated = true;
}


function TextString( string, gl, font, shaderProgram )
{
    this.string = string;
    this.font = font;
    this.shaderProgram = shaderProgram;
    this.generated = false;
    this.color = [1,1,1];
    this.regenerate( gl )
    
}
