
var code = {};
window.addEventListener('load',function(){code.load();},false);

code.load = function(){
	c = document.getElementsByClassName('code');
	for(i = 0;i < c.length;i++){
		if(c[i].className != 'code inline')c[i].innerHTML = this.parse(c[i].innerHTML);
	}
}

code.parse = function(v){
	v = v.replace(/(\t){4}/g,'');
	v = v.split("\n");
	v.splice(0,1);
	v.splice(v.length-1,1);
	v = v.join("\n");
	v = v.replace(/\n/g,"<br>");
	v = v.replace(/\t/g,"&nbsp;&nbsp;&nbsp;&nbsp;");
	v = this.highlight(v);
	return v;
}

code.highlight = function(v){

	return v;
}