<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output
method="html"
version="4.01"
encoding="UTF-8"
omit-xml-declaration="yes"
standalone="no"
doctype-public="PUBLIC"
indent="yes"
media-type="text/html"/>

<xsl:template match="/">

<xsl:variable name="CurrChapter"><xsl:value-of select="Page/CurrChapter"/></xsl:variable>


  <html>
  <head>
  <title><xsl:value-of select="Page/Title"/></title>
  <link rel='stylesheet' type='text/css' href='tuck.css' />
  </head>
  <body>
	<div class="title"><xsl:value-of select="Page/Title"/></div>
 	<div class="index">
 	<xsl:for-each select="Page/Index/Link">
 		<DIV>
 			<xsl:choose>
 				<xsl:when test="@desc!=$CurrChapter">
					<xsl:element name="a">
					<xsl:attribute name="href">
					<xsl:value-of select="@address" />
					</xsl:attribute>
					<xsl:value-of select="@desc" />
					</xsl:element>
				</xsl:when>
	 			<xsl:otherwise><xsl:value-of select="@desc"/></xsl:otherwise>
 			</xsl:choose>
 		</DIV>
 	</xsl:for-each> 		
 	</div>
    
    <xsl:for-each select="Page/Previous">
 		<DIV class='previous'>
			<xsl:element name="a">
			<xsl:attribute name="href">
			<xsl:value-of select="@address" />
			</xsl:attribute>
			Previous
			</xsl:element>
 		</DIV>
 	</xsl:for-each>
 	
 	<xsl:for-each select="Page/Next">
 		<DIV class='next'>
			<xsl:element name="a">
			<xsl:attribute name="href">
			<xsl:value-of select="@address" />
			</xsl:attribute>
			Next
			</xsl:element>
 		</DIV>
 	</xsl:for-each>
    
    <DIV class='chapter'>
    	<xsl:value-of select='Page/ChapterText'/>
    </DIV>
    
    
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>
