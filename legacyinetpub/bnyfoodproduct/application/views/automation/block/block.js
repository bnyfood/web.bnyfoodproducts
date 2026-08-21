var block;
function addNewBlock(shopid,mapid,url)
{
	var blocksize=$('#block_size').val();
	
    
	block=new Block(shopid,mapid,blocksize,url);

}