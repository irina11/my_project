function chimg(vid)
{
var obj;
var firstimg="image/smile-17.gif";
var secondimg="image/smile-3.gif";
obj=document.getElementById("pictureSmile");
if(vid == 'title')
{
obj.src=firstimg;

}
else
{
obj.src=secondimg;

}
} 
